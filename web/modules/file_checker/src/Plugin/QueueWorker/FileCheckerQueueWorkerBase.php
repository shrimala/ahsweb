<?php

namespace Drupal\file_checker\Plugin\QueueWorker;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Queue\QueueWorkerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\State\StateInterface;
use Drupal\Core\Logger\LoggerChannelFactory;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\file\FileInterface;

/**
 * Provides base functionality for File Checker Queue Workers.
 */
abstract class FileCheckerQueueWorkerBase extends QueueWorkerBase implements ContainerFactoryPluginInterface {
  use StringTranslationTrait;

  /**
   * The file entity storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $fileStorage;

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
   * Creates a new FileCheckingBase.
   *
   * @param \Drupal\Core\Entity\EntityTypeManager $entity_manager
   *   The file storage.
   * @param \Drupal\Core\State\StateInterface $state
   *   The state service.
   * @param \Drupal\Core\Logger\LoggerChannelFactory $logger_factory
   *   The logger channel factory service.
   */
  public function __construct(EntityTypeManager $entity_manager, StateInterface $state, LoggerChannelFactory $logger_factory) {
    $this->fileStorage = $entity_manager->getStorage('file');
    $this->state = $state;
    $this->logger = $logger_factory->get('file_checker');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('state'),
      $container->get('logger.factory')
    );
  }

  /**
   * Checks a file exists at the uri of a file entity.
   *
   * @param \Drupal\file\FileInterface $file
   *   The file entity.
   *
   * @return bool
   *   Whether or not the file exists at the uri.
   */
  protected function fileExists(FileInterface $file) {
    $fileExists = file_exists($file->uri->value);
    if (!$fileExists) {
      $results = $this->state->get('file_checker.files_missing');
      $results[] = $file->uri->value;
      $this->state->set('file_checker.files_missing', $results);
    }
    $this->state->set('file_checker.files_checked', \Drupal::state()->get('file_checker.files_checked') + 1);
    return $fileExists;
  }

  /**
   * Log results from the whole finished checking run.
   */
  protected function finishingChecking() {
    $files_missing = $this->state->get('file_checker.files_missing');

    $this->state->set('file_checker.run_in_progress', FALSE);
    $this->state->set('file_checker.files_queued', 0);
    $this->state->set('file_checker.last_run_end', time());
    $this->state->set('file_checker.last_run_start', \Drupal::state()->get('file_checker.run_start'));
    $this->state->set('file_checker.last_run_files_missing', $files_missing);

    $files_missing_count = count($files_missing);
    $missingReport = $this->formatPlural($files_missing_count, '1 file missing: ', '@count files missing: ');
    $headline = t("File checking finished.") . ' ' . $missingReport;
    if (count($files_missing_count) > 0) {
      $files_missing_string = implode("; ", $files_missing);
      $this->logger->warning($headline . $files_missing_string);
    }
    else {
      $this->logger->notice($headline);
    }
  }

  /**
   * Log the start of the checking run.
   */
  protected function startingChecking() {
    $this->state->set('file_checker.run_in_progress', TRUE);
    $this->state->set('file_checker.run_start', time());
    $this->logger->notice("File checking started.");
  }

  /**
   * {@inheritdoc}
   */
  public function processItem($item) {
    if ($this->state->get('file_checker.run_in_progress') === FALSE) {
      // This is the start of the run.
      $this->startingChecking();
    }
    $fileExists = NULL;
    /** @var FileInterface $file */
    $file = $this->fileStorage->load($item->fileId);
    if ($file instanceof FileInterface) {
      $fileExists = $this->fileExists($file);
    }
    if ($item->lastItem == TRUE) {
      // The queue filler designated this item as the last item in the run.
      $this->finishingChecking();
    }
    return $fileExists;
  }

}
