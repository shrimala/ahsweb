<?php

namespace Drupal\file_checker\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\State\StateInterface;
use Drupal\file_checker\FileChecker;

/**
 * Controller routines for update routes.
 */
class FileCheckerResults extends ControllerBase {

  /**
   * The File Checker service.
   */
  protected $fileChecker;

  /**
   * The state service.
   *
   * @var \Drupal\Core\State\StateInterface $state
   */
  protected $state;

  /**
   * Constructs FileCheckerResults object.
   *
   * @param $file_checker
   *   File Checker Service.
   * @param \Drupal\Core\State\StateInterface $state
   *   The state service.
   */
  public function __construct($file_checker, StateInterface $state) {
    $this->fileChecker = $file_checker;
    $this->state = $state;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('file_checker.file_checker'),
      $container->get('state')
    );
  }

  /**
   * Displays the results of the last File Checker run.
   *
   * @return array
   *   A build array with the File Checker results.
   */
  public function displayLastResults() {
    $build = [];
    $build['summary'] = [
      '#markup' => $this->fileChecker->lastStatus(),
    ];
    $build['files'] = [
      '#markup' => "<p>" . $this->listMissingFiles($this->state->get('file_checker.last_run_files_missing')) . "</p>",
    ];
    return $build;
  }

  /**
   * Displays the results of the current in progress File Checker run.
   *
   * @return array
   *   A build array with the File Checker results.
   */
  public function displayCurrentResults() {
    $build = [];
    $build['summary'] = [
      '#markup' => $this->fileChecker->currentStatus(),
    ];

    if ($this->state->get('file_checker.run_in_progress')) {
      $build['refresh'] = [
        '#markup' => "<p>" . t("File checking is in progress, refresh the page to see updated results.") . "</p>",
      ];
    }

    $build['files'] = [
      '#markup' => $this->listMissingFiles($this->state->get('file_checker.files_missing')),
    ];
    return $build;
  }

  /**
   * List missing files.
   *
   * @return string
   *   A markup string with the File Checker results.
   */
  protected function listMissingFiles($files) {
    $markup = '';
	if(!empty($files)) {
		if (count($files) > 0) {
		  $markup = t("Missing files:") . "<br>";
		}
		foreach ($files as $file) {
		  $markup = $markup . $file . "<br>";
		}
	}
    return $markup;
  }

}
