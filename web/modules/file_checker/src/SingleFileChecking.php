<?php

namespace Drupal\file_checker;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\file\FileInterface;
use Drupal\Core\Logger\LoggerChannelFactory;
use Drupal\Core\Queue\QueueFactory;
use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\Entity\EntityTypeManager;

/**
 * Checks a single file entity.
 */
class SingleFileChecking {

  /**
   * The file entity storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $fileStorage;

  /**
   * The File Checker logger channel.
   *
   * @var LoggerChannelFactory $logger
   */
  protected $logger;

  /**
   * The File Checker checking queue.
   *
   * @var QueueInterface $checkingQueue
   */
  protected $checkingQueue;

  /**
   * The File Checker settings config.
   *
   * @var $config
   */
  protected $config;

  /**
   * Creates a new SingleFileChecking object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManager $entity_type_manager
   *   The file storage.
   * @param \Drupal\Core\Logger\LoggerChannelFactory $logger_factory
   *   The logger channel factory service.
   * @param \Drupal\Core\Queue\QueueFactory $queue_factory
   *   The queue factory service.
   * @param \Drupal\Core\Config\ConfigFactory $config_factory
   *   The config factory service.
   */
  public function __construct(EntityTypeManager $entity_type_manager, LoggerChannelFactory $logger_factory, QueueFactory $queue_factory, ConfigFactory $config_factory) {
    $this->fileStorage = $entity_type_manager->getStorage('file');
    $this->logger = $logger_factory->get('file_checker');
    $this->checkingQueue = $queue_factory->get('file_checker');
    $this->config = $config_factory->get('file_checker.settings');
  }

  /**
   * Check files when recorded location changes, if configured to do so.
   *
   * Triggered by hook_file_insert and hook_file_update.
   *
   * @param FileInterface $file
   *   The file which has just been inserted or updated.
   */
  public function checkIfChanged(FileInterface $file) {
    // If there is no original uri, then this is a new file entity.
    $oldUri = empty($file->original->uri->value) ? '' : $file->original->uri->value;
    // Check files only if the recorded location has changed.
    if ($oldUri !== $file->uri->value) {
      $check_on_change = $this->config->get('check_on_change');
      if ($check_on_change == 'immediately') {
        $this->checkFile($file, TRUE);
      }
      elseif ($check_on_change == 'later') {
        $this->queue($file);
      }
    }
  }

  /**
   * Adds a file to the queue for checking later.
   *
   * @param FileInterface $file
   *   The file to be queued for checking.
   */
  public function queue(FileInterface $file) {
    $queueItem = new \stdClass();
    $queueItem->fileId = $file->id();
    $this->checkingQueue->createItem($queueItem);
  }

  /**
   * Checks whether a file exists at the uri given a file entity id.
   *
   * @param int $fileId
   *   The id of the file entity to check.
   *
   * @return bool
   *   Whether or not the file exists.
   */
  public function checkFileFromId($fileId, $log = FALSE) {
    $fileIsMissing = FALSE;
    /** @var FileInterface $file */
    $file = $this->fileStorage->load($fileId);
    if ($file instanceof FileInterface) {
      $fileIsMissing = $this->checkFile($file, $log);
    }
    return $fileIsMissing;
  }

  /**
   * Checks whether a file exists at the uri given a file entity.
   *
   * @param FileInterface $file
   *   The file entity to check.
   *
   * @return bool
   *   Whether or not the file exists.
   */
  public function checkFile(FileInterface $file, $log = FALSE) {
    $fileIsMissing = $this->fileIsMissing($file->uri->value);
    // Log missing files.
    if ($fileIsMissing && $log) {
      $this->logger->warning(t("Missing file") . ' ' . $file->fid->value . ': ' . $file->uri->value);
    }
    // Record the file as missing or not.
    if ($file->missing->value !== $fileIsMissing) {
      $file->set('missing', $fileIsMissing);
      $file->save();
    }
    return $fileIsMissing;
  }

  /**
   * A wrapper for is_file && is_readable, to ease swapping out for testing.
   *
   * @param string $uri
   *   The uri of a file.
   *
   * @return bool
   *   Whether or not the file exists at the uri.
   */
  protected function fileIsMissing($uri) {
    return !(is_file($uri) && is_readable($uri));
  }

}
