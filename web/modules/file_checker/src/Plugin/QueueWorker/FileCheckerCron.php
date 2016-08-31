<?php

namespace Drupal\file_checker\Plugin\QueueWorker;

/**
 * A File Checker queue worker that checks files when cron runs.
 *
 * @QueueWorker(
 *   id = "file_checker",
 *   title = @Translation("File Checker Cron"),
 *   cron = {"time" = 3600}
 * )
 */
class FileCheckerCron extends FileCheckerQueueWorkerBase {}
