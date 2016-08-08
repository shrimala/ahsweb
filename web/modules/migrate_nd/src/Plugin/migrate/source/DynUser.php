<?php

/**
 * @file
 * Contains \Drupal\migrate_nd\Plugin\migrate\source\DynUser.
 */

namespace Drupal\migrate_nd\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;

/**
 * Source plugin for dyn user accounts.
 *
 * @MigrateSource(
 *   id = "dyn_user"
 * )
 */
class DynUser extends SqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    return $this->select('migrate_nd_dyn_account', 'mea')
      ->fields('mea', ['aid', 'status', 'registered', 'username', 'nickname',
                            'password', 'email', 'sex', 'dyns']);
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    $fields = [
      'aid' => $this->t('Account ID'),
      'status' => $this->t('Blocked/Allowed'),
      'registered' => $this->t('Registered date'),
      'username' => $this->t('Account name (for login)'),
      'nickname' => $this->t('Account name (for display)'),
      'password' => $this->t('Account password (raw)'),
      'email' => $this->t('Account email'),
      'sex' => $this->t('Gender'),
      'dyns' => $this->t('Favorite dyns, pipe-separated'),
    ];

    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return [
      'aid' => [
        'type' => 'integer',
        'alias' => 'mea',
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
    /**
     * prepareRow() is the most common place to perform custom run-time
     * processing that isn't handled by an existing process plugin. It is called
     * when the raw data has been pulled from the source, and provides the
     * opportunity to modify or add to that data, creating the canonical set of
     * source data that will be fed into the processing pipeline.
     *
     * In our particular case, the list of a user's favorite dyns is a pipe-
     * separated list of dyn IDs. The processing pipeline deals with arrays
     * representing multi-value fields naturally, so we want to explode that
     * string to an array of individual dyn IDs.
     */
    if ($value = $row->getSourceProperty('dyns')) {
      $row->setSourceProperty('dyns', explode('|', $value));
    }
    /**
     * Always call your parent! Essential processing is performed in the base
     * class. Be mindful that prepareRow() returns a boolean status - if FALSE
     * that indicates that the item being processed should be skipped. Unless
     * we're deciding to skip an item ourselves, let the parent class decide.
     */
    return parent::prepareRow($row);
  }

}
