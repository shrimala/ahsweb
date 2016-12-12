<?php

/**
 * @file
 * Contains Drupal\changes\Plugin\Field\FieldType\diffItem.
 */

namespace Drupal\changes\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'changes' field type.
 *
 * @FieldType(
 *   id = "changes",
 *   label = @Translation("Changes"),
 *   module = "changes",
 *   description = @Translation("Stores a pair of revision IDs."),
 *   default_formatter = "changes",
 * )
 */
class changesItem extends FieldItemBase {
 
  /**
   * {@inheritdoc}
  */
  public function isEmpty() {
    return empty($this->get('right_rid')->getValue());
  }
  
  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    
    $properties['left_rid'] = DataDefinition::create('integer')
      ->setLabel(t('Left revision ID'))
      ->setSetting('unsigned', TRUE);
 
    $properties['right_rid'] = DataDefinition::create('integer')
      ->setLabel(t('Right revision ID'))
      ->setSetting('unsigned', TRUE);
 
      return $properties;
  }

  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    return array(
      'columns' => array(
        'left_rid' => array(
          'type' => 'int',
          'description' => 'A revision ID.',
          'unsigned' => TRUE,
        ),
        'right_rid' => array(
          'type' => 'int',
          'description' => 'A revision ID.',
          'unsigned' => TRUE,
        ),
      ),
    );
  }
  
}
