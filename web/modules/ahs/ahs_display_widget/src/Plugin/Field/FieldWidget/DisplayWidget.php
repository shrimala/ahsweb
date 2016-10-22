<?php

namespace Drupal\ahs_display_widget\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\user\EntityOwnerInterface;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Drupal\Core\Field\Plugin\Field\FieldWidget\EntityReferenceAutocompleteWidget;

/**
 * Plugin implementation of the 'ahs_display' widget.
 *
 * @FieldWidget(
 *   id = "ahs_display",
 *   label = @Translation("Display"),
 *   description = @Translation("Display the field, not as a form."),
 *   field_types = {
 *     "comment",
 *     "datetime",
 *     "file",
 *     "image",
 *     "link",
 *     "list_string",
 *     "list_float",
 *     "list_integer",
 *     "path",
 *     "text_with_summary",
 *     "text",
 *     "text_long",
 *     "email",
 *     "boolean",
 *     "created",
 *     "changed",
 *     "timestamp",
 *     "string_long",
 *     "language",
 *     "decimal",
 *     "uri",
 *     "float",
 *     "password",
 *     "string",
 *     "integer",
 *     "entity_reference",
 *     "uuid",
 *     "map",
 *     "taxonomy_term_reference"
 *   },
 *   list_class = "\Drupal\Core\Field\FieldItemList",
 * )
 */
class DisplayWidget extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return array(
      'view_mode' => 'Default',
    ) + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $element = parent::settingsForm($form, $form_state);
    $entityDisplayRepository = \Drupal::service('entity_display.repository');

    $element['view_mode'] = array(
      '#type' => 'select',
      '#options' => $entityDisplayRepository->getViewModeOptions($this->getFieldSetting('target_type')),
      '#title' => t('View mode'),
      '#default_value' => $this->getSetting('view_mode'),
      '#description' => t('Display the field using the formatter & settings for the field from this view mode.'),
    );

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = parent::settingsSummary();
    $viewMode = ucfirst($this->getSetting('view_mode'));
    $summary[] =  t('View mode: @view_mode', array('@view_mode' => $viewMode));
    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function form(FieldItemListInterface $items, array &$form, FormStateInterface $form_state, $get_delta = NULL) {
    $viewMode = $this->getSetting('view_mode');
    $display = $items->view($viewMode);

    //return array('test' => ['#markup'=>'<p>test</p>', ]);
    return $display;
  }

}
