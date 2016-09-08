<?php

namespace Drupal\file_checker\Plugin\QueueWorker;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Queue\QueueWorkerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\file_checker\SingleFileChecking;

/**
 * A File Checker queue worker that checks queued files exist.
 *
 * @QueueWorker(
 *   id = "file_checker",
 *   title = @Translation("File Checking"),
 *   cron = {"time" = 20}
 * )
 */
class FileChecker extends QueueWorkerBase implements ContainerFactoryPluginInterface {

  /**
   * The File Checker single file checking service.
   *
   * @var $singleFileChecking
   */
  protected $singleFileChecking;

  /**
   * Creates a new FileChecker queueworker object.
   *
   * @param $single_file_checking
   *   File Checker single file checking service
   */
  public function __construct($single_file_checking) {
    $this->singleFileChecking = $single_file_checking;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $container->get('file_checker.single_file_checking')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function processItem($item) {
    $this->singleFileChecking->checkFileFromId($item->fileId, TRUE);
  }

}
