<?php

namespace Drupal\file_checker;

use Drupal\Core\Entity\Query\QueryInterface;
use Drupal\Core\State\StateInterface;
use Drupal\Core\Logger\LoggerChannelFactory;
use Drupal\Core\Entity\Query\QueryFactory;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;

const MAX_SET_SIZE = 5000;
const MIN_SET_SIZE = 25;

/**
 * Creates a BulkFileChecking object.
 */
class BulkFileChecking {
  use StringTranslationTrait;

  /**
   * The state service.
   *
   * @var \Drupal\Core\State\StateInterface $state
   */
  protected $state;

  /**
   * The File Checker logger channel.
   *
   * @var LoggerChannelFactory $logger
   */
  protected $logger;

  /**
   * The entity query service.
   *
   * @var QueryFactory $queryFactory
   */
  protected $queryFactory;

  /**
   * The date formatter service.
   *
   * @var \Drupal\Core\Datetime\DateFormatterInterface
   */
  protected $dateFormatter;

  /**
   * The FileChecker SingleFileChecking service.
   */
  protected $singleFileChecking;

  /**
   * Constructs a FileCheckerManager object.
   *
   * @param \Drupal\Core\State\StateInterface $state
   *   The state service.
   * @param \Drupal\Core\Logger\LoggerChannelFactory $logger_factory
   *   The logger channel factory service.
   * @param \Drupal\Core\Entity\Query\QueryFactory $query_factory
   *   The entity query factory service.
   * @param \Drupal\Core\Datetime\DateFormatterInterface $date_formatter
   *   The date formatter service.
   * @param $single_file_checking
   *   The FileChecker SingleFileChecking service.
   */
  public function __construct(StateInterface $state, LoggerChannelFactory $logger_factory, QueryFactory $query_factory, DateFormatterInterface $date_formatter, $single_file_checking) {
    $this->state = $state;
    $this->logger = $logger_factory->get('file_checker');
    $this->queryFactory = $query_factory;
    $this->dateFormatter = $date_formatter;
    $this->singleFileChecking = $single_file_checking;
  }

  /**
   * Start a new bulk file checking run.
   */
  public function start() {
    if ($this->hasBeenRequested()) {
      $this->logger->notice("Bulk file checking has already been requested.");
      return FALSE;
    }
    $this->reset();
    $this->state->set('file_checker.background_requested', TRUE);
    $this->logger->notice("Bulk file checking requested.");
    return TRUE;
  }

  /**
   * Execute bulk file checking, probably from cron or drush.
   *
   * @param int $timeLimit
   *   How many seconds to check files for.
   *
   * @return int
   *   The fid of the last file checked.
   */
  public function executeInBackground($timeLimit) {
    // Only execute checking if it has been requested.
    // For example, this function may be called every minute by cron,
    // to do a 1 minute chunk of work, but that doesn't mean we want to be
    // checking files continuously.
    if (!$this->hasBeenRequested()) {
      $runState['aborted'] = TRUE;
      return $runState;
    }
    $runState['aborted'] = FALSE;

    // If this is the first execution within a run, set the start time.
    if (empty($this->state->get('file_checker.background_run_start'))) {
      $this->state->set('file_checker.background_run_start', time());
    }

    // Load the state of the checking run.
    $previousLastCheckedFile = $this->state->get('file_checker.background_last_checked_file');
    $previousFilesMissingCount = $this->state->get('file_checker.background_files_missing_count');
    $previousFilesCheckedCount = $this->state->get('file_checker.background_files_checked_count');
    $runState['last_checked_file'] = $previousLastCheckedFile;
    $runState['files_missing_count'] = $previousFilesMissingCount;
    $runState['files_checked_count'] = $previousFilesCheckedCount;
    $runState['speed'] = $this->state->get('file_checker.speed');

    // Check files.
    $runState = $this->checkForSomeTime($timeLimit, $runState);
    $runState['files_just_checked'] = $runState['files_checked_count'] - $previousFilesCheckedCount;
    $runState['files_to_check'] = $this->filesCount();

    // See if the checking run has stopped.
    if ($previousLastCheckedFile != $runState['last_checked_file']) {
      // If not stopped, store the state of the checking run ready to resume.
      $this->state->set('file_checker.background_last_checked_file', $runState['last_checked_file']);
      $this->state->set('file_checker.background_files_missing_count', $runState['files_missing_count']);
      $this->state->set('file_checker.background_files_checked_count', $runState['files_checked_count']);
      $this->state->set('file_checker.speed', $runState['speed']);
      $runState['finished'] = FALSE;
      return $runState;
    }
    else {
      // If it has stopped, conclude the checking run.
      $this->conclude($runState['files_missing_count'], $runState['files_checked_count'], $this->state->get('file_checker.background_run_start'));
      $this->reset();
      $runState['finished'] = TRUE;
      return $runState;
    }
  }

  /**
   * Batch check callback used when running by batch API from UI.
   */
  public function executeInUI($timeLimit, &$context) {
    // If this is a new batch API run, initialise the context.
    if (empty($context['sandbox'])) {
      $this->logger->notice("File checking initiated using batch API.");
      $context['sandbox']['files_to_check'] = $this->filesCount();
      $context['sandbox']['run_start'] = time();
      $context['sandbox']['run_state']['last_checked_file'] = 0;
      $context['sandbox']['run_state']['files_missing_count'] = 0;
      $context['sandbox']['run_state']['files_checked_count'] = 0;
      $context['sandbox']['run_state']['speed'] = $this->state->get('file_checker.speed');
    }
    $previousLastChecked = $context['sandbox']['run_state']['last_checked_file'];

    // Check files.
    $context['sandbox']['run_state'] = $this->checkForSomeTime($timeLimit, $context['sandbox']['run_state']);

    // See if the checking run has stopped.
    // We only stop when no newer files can be loaded, not when we think we've
    // done a certain number of files. This is because the number of file
    // entities could change over the course of the run.
    if ($previousLastChecked != $context['sandbox']['run_state']['last_checked_file']) {
      // If it has not stopped, inform batch API of proportion completed.
      // Must be less than 1 or we trigger batch API finishing without a final
      // check for new files.
      $context['finished'] = min(0.999, $context['sandbox']['run_state']['files_checked_count'] / $context['sandbox']['files_to_check']);
    }
    else {
      // If it has stopped, conclude the batch API processing.
      $context['finished'] = 1;
      $this->conclude($context['sandbox']['run_state']['files_missing_count'], $context['sandbox']['run_state']['files_checked_count'], $context['sandbox']['run_start']);
    }
  }

  /**
   * Bulk check files for a period of time.
   *
   * @param int $timeLimit
   *   How many seconds to check files for.
   *
   * @return int
   *   The fid of the last file checked.
   */
  public function checkForSomeTime($timeLimit, $runState) {
    $endTime = time() + $timeLimit;
    // This will usually loop only once, as a single call to checkFiles
    // inside this loop should take up all the allotted time.
    while (time() < $endTime) {
      $remainingTime = $endTime - time();
      // Sets of files to check should contain twice as many files as we expect
      // to need as it's more inefficient to have to load a second set than it
      // is to load an over-large batch. But sets should not be too large, or
      // they consume too much memory.
      $setSize = min(MAX_SET_SIZE, 2 * ($runState['speed'] * $remainingTime));
      // Tiny sets are also undesirable - they can occur if a previous
      // batch stalled, for example due to a remote server not responding.
      $setSize = max(MIN_SET_SIZE, $setSize);

      // Get the next files with fid greater than the last checked file.
      $fileIds = $this->query()
        ->condition('fid', $runState['last_checked_file'], '>')
        ->sort('fid', 'ASC')
        ->range(0, $setSize)
        ->execute();

      // If there are files subsequent to the last checked file, check them.
      if (count($fileIds) > 0) {
        $runState = $this->checkFiles($fileIds, $runState, $endTime);
      }
      // If there are not, then checking has been completed.
      else {
        break;
      }
    }
    return $runState;
  }

  /**
   * Bulk check a set of files.
   *
   * @param array $fileIds
   *   The file Ids to check.
   * @param int $runState
   *   An array that tracks progress of the current run.
   * @param int $endTime
   *   The time to stop checking regardless of progress.
   *
   * @return int
   *   The fid of the last file checked.
   */
  protected function checkFiles($fileIds, $runState, $endTime) {
    // Check the files until we reach the end of the batch or run out of time.
    $startTime = time();
    $startCheckedCount = $runState['files_checked_count'];
    foreach ($fileIds as $fileId) {
      if (time() > ($endTime)) {
        break;
      }
      $fileIsMissing = $this->singleFileChecking->checkFileFromId($fileId);
      if ($fileIsMissing) {
        $runState['files_missing_count']++;
      }
      $runState['files_checked_count']++;
      $runState['last_checked_file'] = $fileId;
    }
    $runState['speed'] = ($runState['files_checked_count'] - $startCheckedCount) / max(1, (time() - $startTime));
    return $runState;
  }

  /**
   * Specify the file entities to check.
   *
   * @return \Drupal\Core\Entity\Query\QueryInterface
   *   An entity query object selecting the file entities to be checked.
   */
  public function query() {
    return $this->queryFactory->get('file');
  }

  /**
   * How many file entities are eligible for checking.
   *
   * @return int
   *   A count of how many file entities are eligible for checking.
   */
  public function filesCount() {
    return $this->query()->count()->execute();
  }

  /**
   * Resets bulk File Checker's internal state variables.
   */
  protected function reset() {
    $this->state->set('file_checker.background_last_checked_file', 0);
    $this->state->set('file_checker.background_files_checked_count', 0);
    $this->state->set('file_checker.background_files_missing_count', 0);
    $this->state->set('file_checker.background_requested', FALSE);
    $this->state->set('file_checker.background_run_start', NULL);
  }

  /**
   * Log the results of the file checking run.
   */
  protected function conclude($filesMissingCount, $filesCheckedCount, $runStart) {
    $this->state->set('file_checker.last_run_start', $runStart);
    $this->state->set('file_checker.last_run_end', time());
    $missingReport = $this->formatPlural($filesMissingCount, '1 missing file detected,', '@count missing files detected,');
    $checkedReport = $this->formatPlural($filesCheckedCount, '1 file checked.', '@count files checked.');
    if ($filesMissingCount > 0) {
      $this->logger->warning($missingReport . ' ' . $checkedReport);
    }
    else {
      $this->logger->notice($missingReport . ' ' . $checkedReport);
    }
  }

  /**
   * Cancels initiated bulk file checking.
   */
  public function cancel() {
    $this->reset();
    $this->logger->notice("File checking cancelled by user.");
  }

  /**
   * Cancels initiated bulk file checking.
   */
  public function hasBeenRequested() {
    return ($this->state->get('file_checker.background_requested'));
  }

  /**
   * Compiles a report about the last completed file checking bulk run.
   *
   * @return string
   *    Text describing the last File Checker bulk run and its results.
   */
  public function lastStatus() {
    $last_run_start = $this->state->get('file_checker.last_run_start');
    if (empty($last_run_start)) {
      $statusReport = t("Files have never been checked.");
    }
    else {
      $last_run_end = $this->state->get('file_checker.last_run_end');
      $ago = $this->dateFormatter->formatTimeDiffSince($last_run_end);
      $duration = $this->dateFormatter->formatDiff($last_run_start, $last_run_end);
      $statusReport = t("Last check completed @time_elapsed ago, took @duration.", array('@time_elapsed' => $ago, '@duration' => $duration));
    }
    return $statusReport;
  }

  /**
   * Compiles a report about in progress background bulk file checking.
   *
   * @return string
   *   Text describing the progress of the current background file checking run.
   */
  public function backgroundStatus() {
    $statusReport = '';
    if (!empty($this->state->get('file_checker.background_requested'))) {
      $stateReport = t("Background file checking has been requested.");
      $filesReport = $this->formatPlural($this->filesCount(), "Currently 1 file to check.", "Currently @count files to check.");
      $runStart = $this->state->get('file_checker.background_run_start');
      if (!empty($runStart)) {
        $stateReport = t("Background file checking is under way.");
        $runStartedAgo = $this->dateFormatter->formatTimeDiffSince($runStart);
        $startedReport = t("Checking started @time_elapsed ago:", ['@time_elapsed' => $runStartedAgo]);
        $checked = $this->state->get('file_checker.background_files_checked');
        $checkedReport = $this->formatPlural($checked, "1 file checked, ", "@count files checked, ");
        $missingCount = $this->state->get('file_checker.files_missing_count');
        $missingReport = $this->formatPlural($missingCount, "detected 1 missing.", "detected @count missing.");
        $progressReport = $startedReport . ' ' . $checkedReport . $missingReport;
      }
      else {
        $progressReport = t("Actual file checking has not yet started.");
      }
      $statusReport = $stateReport . ' ' . $filesReport . ' ' . $progressReport;
    }
    return $statusReport;
  }

  /**
   * Count how many files have been recorded as missing.
   *
   * @return int
   *   The number of missing files.
   */
  public function missingCount() {
    return $this->query()
      ->condition('missing', TRUE)
      ->count()
      ->execute();
  }

  /**
   * Create a report about the number of missing files.
   *
   * @return array
   *   A render array with link to view missing files.
   */
  public function missingStatus() {
    $missingCount = $this->missingCount();
    if ($missingCount == 0) {
      $missingReport = t("No missing files have been detected.");
    }
    else {
      $missingReport = $this->formatPlural($missingCount, "1 file is missing.", "@count files are missing.");
      $missingReport = array(
        '#type' => 'link',
        '#title' => $missingReport,
        '#url' => Url::fromRoute('view.files_missing.page_1'),
      );
    }
    return $missingReport;
  }

}
