<?php

namespace Drupal\ahs_discussions\Plugin\views\style;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\views\style\EntityReference;

/**
 * EntityReference style plugin.
 *
 * @ingroup views_style_plugins
 *
 * @ViewsStyle(
 *   id = "entity_reference_enhanced",
 *   title = @Translation("Entity Reference enhanced list"),
 *   help = @Translation("Adds additional options to entity reference views displays."),
 *   theme = "views_view_unformatted",
 *   register_theme = FALSE,
 *   display_types = {"entity_reference"}
 * )
 */
class EntityReferenceEnhanced extends EntityReference {

  /**
   * {@inheritdoc}
   */
  protected function defineOptions() {
    $options = parent::defineOptions();
    $options['concatenate_search_fields'] = array('default' => FALSE);
    $options['match_with_interruptions'] = array('default' => FALSE);
    $options['block_typed_in_matches'] = array('default' => FALSE);

    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    parent::buildOptionsForm($form, $form_state);
    
    $form['concatenate_search_fields'] = array(
      '#type' => 'checkbox',
      '#title' => t('Concatenate search fields'),
      '#default_value' => $this->options['concatenate_search_fields'],
      '#description' => t('Match against the concatenated search fields, instead of against any of them individually.'),
    );
    $form['match_with_interruptions'] = array(
      '#type' => 'checkbox',
      '#title' => t('Allow interruptions in matches'),
      '#default_value' => $this->options['match_with_interruptions'],
      '#description' => t('Do not require the whole value to match as a single phrase without interruption.'),
    );
    $form['block_typed_in_matches'] = array(
      '#type' => 'checkbox',
      '#title' => t('Block typed-in matches'),
      '#default_value' => $this->options['block_typed_in_matches'],
      '#description' => t('With typed-in values (not selected from the autocomplete dropdown) never match as reference to existing items. Check this to have autocreation of new items even if typed-in value exactly matches the label of an existing item. Typed-in values wrapped in square brackets will bypass this block.'),
    );

  }

}
