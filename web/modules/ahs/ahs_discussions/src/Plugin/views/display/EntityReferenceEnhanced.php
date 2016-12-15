<?php

namespace Drupal\ahs_discussions\Plugin\views\display;

use Drupal\views\Plugin\views\display\EntityReference;
/**
 * The plugin that handles an entity reference display for discussions autocomplete.
 *
 * "entity_reference_display" is a custom property, used with
 * \Drupal\views\Views::getApplicableViews() to retrieve all views with a
 * 'Entity Reference' display.
 *
 * @ingroup views_display_plugins
 *
 * @ViewsDisplay(
 *   id = "entity_reference_enhanced",
 *   title = @Translation("Entity Reference Enhanced"),
 *   admin = @Translation("Enhanced entity reference views source."),
 *   help = @Translation("Provides display for Entity Reference Views Enhanced widget."),
 *   theme = "views_view",
 *   register_theme = FALSE,
 *   uses_menu_links = FALSE,
 *   entity_reference_display = TRUE
 * )
 */
class EntityReferenceEnhanced extends EntityReference {

  /**
   * {@inheritdoc}
   */
  protected function defineOptions() {
    $options = parent::defineOptions();

    // Force the style plugin to 'entity_reference_enhanced'
    $options['style']['contains']['type'] = array('default' => 'entity_reference_enhanced');

    return $options;
  }
  
  /**
   * {@inheritdoc}
   */
  public function query() {
    if (!empty($this->view->live_preview)) {
      return;
    }

    // Make sure the id field is included in the results.
    $id_field = $this->view->storage->get('base_field');
    $id_table = $this->view->storage->get('base_table');
    $this->id_field_alias = $this->view->query->addField($id_table, $id_field);

    $options = $this->getOption('entity_reference_options');

    // Restrict the autocomplete options based on what's been typed already.
    if (isset($options['match'])) {
      $style_options = $this->getOption('style');
      $value = $options['match'];
      if ($options['match_operator'] === '=') {
        $operator = '=';
      }
      else {
        $operator = 'LIKE';
        $value = db_like($value) . '%';
        if ($options['match_operator'] != 'STARTS_WITH') {
          $value = '%' . $value;
        }
      }

      if ($style_options['options']['match_with_interruptions']) {
        // Allow interruptions in matched values.
        // So "Hermitage manual" would match "Hermitage test manual"
        $value = str_replace(' ', '%', $value);
      }

      // The = operator is only used checking for exact matches for typed-in data.
      // We have to use these to trigger referencing in non-javascript behat tests,
      // but sometimes we don't want this behaviour outside of behat because we
      // want autocreation of a new item to happen instead.
      // Therefore we use square brackets around values to trigger this
      // for Behat.
      // A nasty hack!
      $block_match = FALSE;
      $concatenate_search_fields = $style_options['options']['concatenate_search_fields'];
      if ($operator === '=' && $style_options['options']['block_typed_in_matches']) {
        $block_match = TRUE;
        if (substr($value, 0, 1) === '[' && substr($value, -1) === ']') {
          // This must be Behat input not real user input
          // So prepare to allow an exact match to reference existing item
          // instead of autocreating.
          $block_match = FALSE;
          $concatenate_search_fields = FALSE;
          $value = substr($value, 1, -1);
        }
      }

      // Build the condition using the selected search fields.
      $search_fields = array();
      foreach ($style_options['options']['search_fields'] as $field_id) {
        if (!empty($field_id)) {
          // Get the table and field names for the checked field.
          $field_handler = $this->view->field[$field_id];
          $table_alias = $this->view->query->ensureTable($field_handler->table, $field_handler->relationship);
          $field_alias = $this->view->query->addField($table_alias, $field_handler->realField);
          $field = $this->view->query->fields[$field_alias];

          $field_with_table = $field['table'] . "." . $field['field'];
          if ($concatenate_search_fields) {
            $search_fields[] = "COALESCE($field_with_table, '')";
          }
          else {
            $search_fields[] = $field_with_table;
          }
         }
      }

      // Combine search fields
      if ($concatenate_search_fields) {
        // By concatenation
        if ($block_match) {
          $where_field = "''";
        }
        else {
          $fields_string = implode(", ", $search_fields);
          $where_field = "CONCAT($fields_string)";
        }
        $this->view->query->addWhereExpression(0, "$where_field $operator '$value'");
      }
      else {
        // By OR
        $conditions = db_or();
        foreach ($search_fields as $where_field) {
          if ($block_match) {
            $where_field = "''";
          }
          $conditions->condition($where_field, $value, $operator);
        }
        $this->view->query->addWhere(0, $conditions);
      }
      
    }

    // Add an IN condition for validation.
    if (!empty($options['ids'])) {
      $this->view->query->addWhere(0, $id_table . '.' . $id_field, $options['ids'], 'IN');
    }

    $this->view->setItemsPerPage($options['limit']);
  }

}
