<?php

/**
 * @file
 * File Checker drush commands.
 */

/**
 * Implements hook_drush_command().
 */
function file_checker_drush_command() {

  $commands['file-checking-start'] = [
    'description' => 'Start file checking.',
    'aliases' => ['fcheck-start'],
    'examples' => [
      'drush file-checking-start' => 'Starts file checking.',
    ],
  ];

  $commands['file-checking-cancel'] = [
    'description' => 'Start file checking.',
    'aliases' => ['fcheck-cancel'],
    'examples' => [
      'drush file-checking-start' => 'Cancels file checking.',
    ],
  ];

  $commands['file-checking-execute'] = [
    'description' => 'Execute file checking for a period of time.',
    'aliases' => ['fcheck-exec'],
    'arguments' => [
      'seconds' => 'Number of seconds to execute for.',
    ],
    'examples' => [
      'drush file-checking-execute' => 'Check files for 50 seconds.',
      'drush fcheck-exec 110' => 'Check files for 110 seconds".',
    ],
  ];

  return $commands;
}

/**
 * Drush logic for command file-checking-start.
 */
function drush_file_checker_file_checking_start() {
  $success = \Drupal::service('file_checker.bulk_file_checking')->start();
  if ($success) {
    drush_print(dt("Bulk file checking requested. To actually check files, next run 'drush file-checking-execute'."));
  }
  else {
    drush_print(dt("Bulk file checking has already been requested. To actually check files, instead run 'drush file-checking-execute'."));
  }
}

/**
 * Drush logic for command file-checking-cancel.
 */
function drush_file_checker_file_checking_cancel() {
  \Drupal::service('file_checker.bulk_file_checking')->cancel();
  drush_print(dt("Bulk file checking cancelled."));
}

/**
 * Drush logic for command file-checking-execute.
 */
function drush_file_checker_file_checking_execute($seconds = 50) {
  drush_print(dt('Files will be checked for up to @seconds seconds.', array('@seconds' => $seconds)));
  $runState = \Drupal::service('file_checker.bulk_file_checking')->executeInBackground($seconds);
  if ($runState['aborted']) {
    drush_print(dt("File checking has not been previously started, checking aborted."));
    drush_print(dt("To start checking, first run 'drush file-checking-start'."));
  }
  else {
    drush_print(dt("@files_just_checked files just checked.", ['@files_just_checked' => $runState['files_just_checked']]));
    drush_print(dt("Now checked @files_checked_count of @files_to_check files. @files_missing_count missing files detected.", [
      '@files_checked_count' => $runState['files_checked_count'],
      '@files_to_check' => $runState['files_to_check'],
      '@files_missing_count' => $runState['files_missing_count'],
    ]));
    if ($runState['finished']) {
      drush_print(dt("File checking completed."));
    }
    else {
      drush_print(dt("To check more files, run 'drush file-checking-execute' again."));
    }
  }
}
