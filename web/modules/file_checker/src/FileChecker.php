<?php

namespace Drupal\file_checker;

use Drupal\Core\Queue\QueueFactory;
use Drupal\Core\State\StateInterface;
use Drupal\Core\Logger\LoggerChannelFactory;
use Drupal\Core\Entity\Query\QueryFactory;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Creates a FileChecker object.
 */
class FileChecker {
  use StringTranslationTrait;

  /**
   * The File Checker queue.
   *
   * @var QueueInterface $queue
   */
  protected $queue;

  /**
   * The state service.
   *
   * @var \Drupal\Core\State\StateInterface $state
   */
  protected $state;

  /**
   * The File Checker logger channel.
   *
   * @var LoggerChannelInterface $logger
   */
  protected $logger;

  /**
   * The entity query service.
   *
   * @var QueryFactory $entityQuery
   */
  protected $entityQuery;

  /**
   * The date formatter service.
   *
   * @var \Drupal\Core\Datetime\DateFormatterInterface
   */
  protected $dateFormatter;

  /**
   * Constructs a FileCheckerManager object.
   *
   * @param \Drupal\Core\Queue\QueueFactory $queue_factory
   *   The queue factory service.
   * @param \Drupal\Core\State\StateInterface $state
   *   The state service.
   * @param \Drupal\Core\Logger\LoggerChannelFactory $logger_factory
   *   The logger channel factory service.
   * @param \Drupal\Core\Entity\QueryFactory $entity_query
   *   The entity query factory service.
   * @param \Drupal\Core\Datetime\DateFormatterInterface $date_formatter
   *   The date formatter service.
   */
  public function __construct(QueueFactory $queue_factory, StateInterface $state, LoggerChannelFactory $logger_factory, QueryFactory $entity_query, DateFormatterInterface $date_formatter) {
    $this->queue = $queue_factory->get('file_checker');
    $this->state = $state;
    $this->logger = $logger_factory->get('file_checker');
    $this->entityQuery = $entity_query;
    $this->dateFormatter = $date_formatter;
  }

  /**
   * Creates a queue item for each file entity.
   */
  public function queueFiles() {
    if (!$this->isQueueEmpty()) {
      $this->logger->notice("Files already queued for checking, none added.");
      return FALSE;
    }
    $this->reset();
    $fileIds = $this->entityQuery->get('file')->execute();
    // Get the last in $fileIds.
    // @todo errors if fileIds is zero length.
    $lastId = array_slice($fileIds, -1)[0];

    foreach ($fileIds as $fileId) {
      $queueItem = new \stdClass();
      $queueItem->fileId = $fileId;
      $queueItem->lastItem = FALSE;
      if ($fileId == $lastId) {
        // Mark this as the last item in the queue for this run.
        $queueItem->lastItem = TRUE;
      }
      $this->queue->createItem($queueItem);
    }
    $this->state->set('file_checker.files_queued', count($fileIds));
    $this->logger->notice(count($fileIds) . " files added to queue for checking.");
    return TRUE;
  }

  /**
   * Resets File Checker's internal variables.
   */
  protected function reset() {
    $this->state->set('file_checker.files_queued', 0);
    $this->state->set('file_checker.files_checked', 0);
    $this->state->set('file_checker.run_start', NULL);
    $this->state->set('file_checker.run_in_progress', FALSE);
    $this->state->set('file_checker.files_missing', []);
  }

  /**
   * Checks whether the File Checker queue is empty.
   *
   * @return bool
   *   Whether or not files are queued.
   */
  public function isQueueEmpty() {
    $queued = $this->queue->numberOfItems();
    return (($queued == 0) ? TRUE : FALSE);
  }

  /**
   * Cancels the queued or in progress file checking run.
   *
   * @return bool
   *   Whether or not files are still queued.
   */
  public function cancel() {
    $this->queue->deleteQueue();
    $this->reset();
    $this->logger->notice("File checking cancelled by user.");
    // If cancellation has been successful, the queue should be empty.
    return $this->isQueueEmpty();
  }

  /**
   * Compiles a report about the last completed file checking run.
   *
   * @return string
   *    Text describing the last File Checker run and its results.
   */
  public function lastStatus($makeLink = TRUE) {
    $last_run_start = $this->state->get('file_checker.last_run_start');
    if (empty($last_run_start)) {
      $statusReport = t("Last check: <em>Never</em>.");
    }
    else {
      $last_run_end = $this->state->get('file_checker.last_run_end');
      $ago = $this->dateFormatter->formatTimeDiffSince($last_run_end);
      $duration = $this->dateFormatter->formatDiff($last_run_start, $last_run_end);
      $timesReport = t("Last check: Completed %time_elapsed ago , took %duration,", array('%time_elapsed' => $ago, '%duration' => $duration));
      $missing = count($this->state->get('file_checker.last_run_files_missing'));
      $missingReport = $this->formatPlural($missing, "found 1 file missing.", "found @count files missing.");
      if ($makeLink === TRUE && $missing > 0) {
        $link = '<a href="file-checker/last-results">';
        $missingReport = $link . $missingReport . '</a>';
      }

      $statusReport = $timesReport . ' ' . $missingReport;
    }

    return $statusReport;
  }

  /**
   * Compiles a report about in progress file checking.
   *
   * @return string
   *   Text describing the progress of the current File Checker run.
   */
  public function currentStatus($makeLink = TRUE) {
    $statusReport = '';
    $filesQueued = $this->state->get('file_checker.files_queued');
    if (!empty($filesQueued)) {
      $queueReport = $this->formatPlural($filesQueued, "1 file queued", "@count files queued. ");
      if (!empty($this->state->get('file_checker.run_in_progress'))) {
        $started = $this->dateFormatter->formatTimeDiffSince(\Drupal::state()
          ->get('file_checker.run_start'));
        $startedReport = t("Checking started %time_elapsed ago:", array('%time_elapsed' => $started));
        $checked = $this->state->get('file_checker.files_checked');
        $checkedReport = $this->formatPlural($checked, "1 file checked, ", "@count files checked, ");
        $missing = count($this->state->get('file_checker.files_missing'));
        $missingReport = $this->formatPlural($missing, "1 file missing.", "@count files missing.");
        if ($makeLink === TRUE && $missing > 0) {
          $link = '<a href="file-checker/current-results">';
          $missingReport = $link . $missingReport . '</a>';
        }
        $progressReport = $startedReport . ' ' . $checkedReport . $missingReport;
      }
      else {
        $progressReport = t("Checking will start when cron runs.");
      }
      $statusReport = $queueReport . $progressReport . "<br>";
    }

    return $statusReport;
  }

}
