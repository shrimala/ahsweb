<?php

namespace Drupal\ahs_er_enhanced\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\user\EntityOwnerInterface;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Drupal\Core\Field\Plugin\Field\FieldWidget\EntityReferenceAutocompleteWidget;

/**
 * Plugin implementation of the 'ahs_entity_reference_autocomplete_enhanced' widget.
 *
 * @FieldWidget(
 *   id = "ahs_entity_reference_autocomplete_enhanced",
 *   label = @Translation("Autocomplete enhanced"),
 *   description = @Translation("An enhanced entity reference autocomplete widget."),
 *   field_types = {
 *     "entity_reference"
 *   },
 *   list_class = "\Drupal\Core\Field\EntityReferenceFieldItemList",
 * )
 */
class EntityReferenceAutocompleteEnhancedWidget extends EntityReferenceAutocompleteWidget {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return array(
      'preview' => FALSE,
      'preview_view_mode' => 'Default',
      'preview_hide_ui' => FALSE,
      'preview_empty_message' => t('No items have been referenced.'),
    ) + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $element = parent::settingsForm($form, $form_state);
    $entityDisplayRepository = \Drupal::service('entity_display.repository');

    $element['preview'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t("Preview existing items?"),
      '#default_value' => $this->getSetting('preview'),
    );

    $element['preview_view_mode'] = array(
      '#type' => 'select',
      '#options' => $entityDisplayRepository->getViewModeOptions($this->getFieldSetting('target_type')),
      '#title' => t('View mode'),
      '#default_value' => $this->getSetting('preview_view_mode'),
      '#description' => t('The view mode used to preview the referenced entity.'),
      '#states' => [
        'visible' => [
          ':input[name="fields[' . $this->fieldDefinition->getName() . '][settings_edit_form][settings][preview]"]' => ['checked' => TRUE],
        ],
        'required' => [
          ':input[name="fields[' . $this->fieldDefinition->getName() . '][settings_edit_form][settings][preview]"]' => ['checked' => TRUE],
        ],
      ],
    );

    $element['preview_hide_ui'] = array(
      '#type' => 'checkbox',
      '#title' => t('Hide UI'),
      '#default_value' => $this->getSetting('preview_hide_ui'),
      '#description' => t('Hide controls for adding new items and reordering or removing existing items.'),
      '#states' => [
        'visible' => [
          ':input[name="fields[' . $this->fieldDefinition->getName() . '][settings_edit_form][settings][preview]"]' => ['checked' => TRUE],
        ],
      ],
    );

    $element['preview_empty_message'] = array(
      '#type' => 'textfield',
      '#title' => t('Text if empty'),
      '#default_value' => $this->getSetting('preview_empty_message'),
      '#description' => t('A message that will be displayed if the field has no data. Leave empty to show no message.'),
      '#states' => [
        'visible' => [
          ':input[name="fields[' . $this->fieldDefinition->getName() . '][settings_edit_form][settings][preview_hide_ui]"]' => ['checked' => TRUE],
        ],
      ],
    );

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = parent::settingsSummary();
    $previewMode = $this->getSetting('preview') ? ucfirst($this->getSetting('preview_view_mode')) : "None";
    $previewUI = $this->getSetting('preview_hide_ui') ? " without UI" : " with UI";
    $previewSummary = $previewMode == 'None' ? $previewMode : $previewMode . $previewUI;
    $summary[] =  t('Preview: @preview', array('@preview' => $previewSummary));
    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $referenced_entities = $items->referencedEntities();
    $element = parent::formElement($items, $delta, $element, $form, $form_state);

    // If this is an existing (not new item).
    if ($delta < (count($referenced_entities))) {

      // Mark element as being existing, not new item.
      // Top level of the returned element must be called 'target_id',
      // so we cannot create a container.
      // Autocomplete element does some fancy processing to handle empty strings,
      // so we must use an autocomplete element not a hidden or textfield element.
      // But #states[#visible] does not seem to have an option to always hide.,
      // and autocomplete elements don't seem to accept #attributes, so we must
      // use #prefix and #suffix to add a class so that we can hide it.
      $element['#prefix'] = '<div class="er-enhanced-existing">';
      $element['#suffix'] = '</div>';


      if ($this->getSetting('preview')) {
        // Add preview.
        $element['#prefix'] = '<div class="er-enhanced-existing er-enhanced-previewing">';
        $element['#attached']['library'][] = 'ahs_er_enhanced/preview';
        $entityTypeName = $referenced_entities[$delta]->getEntityType()->id();
        $view_builder = \Drupal::entityTypeManager()
          ->getViewBuilder($entityTypeName);
        $preview = $view_builder->view($referenced_entities[$delta], $this->getSetting('preview_view_mode'));
        $element['preview_container'] = [
          '#type' => 'container',
          '#attributes' => [
            'class' => ['er-enhanced-preview'],
          ],
          'preview' => $preview,
        ];

        // Add a remove link to the preview.
        $element['remove'] = [
          '#markup' => '<a class="er-enhanced-remove" href="">' . t('Remove') . '</a>',
        ];
        $element['#attached']['library'][] = 'ahs_er_enhanced/remove';
      }
    }
    // Else this is the last (i.e. new) item.
    else {
      $element['#prefix'] = '<div class="er-enhanced-new">';
      $element['#suffix'] = '</div>';
    }
    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function form(FieldItemListInterface $items, array &$form, FormStateInterface $form_state, $get_delta = NULL) {
    $elements = parent::form($items, $form, $form_state);
    // Add class to indicate preview is active.
    if ($this->getSetting('preview')) {
      $elements['#attributes']['class'][] = 'er-enhanced-previewing';

      // Add an Edit link if UI is hidden.
      if ($this->getSetting('preview_hide_ui')) {
        /*
        $elements['er-enhanced-edit'] = [
          '#markup' => '<a class="er-enhanced-edit" style="border:1px solid red;display:none;" href="">' . t('Edit') . '</a>',
          '#visible' => FALSE,
        ];
        */
        if ((count($items) < 2) && !empty($this->getSetting('preview_empty_message'))) {
          $elements['er-enhanced-empty-message'] = [
            '#markup' => '<p class="er-enhanced-empty-message">' . $this->getSetting('preview_empty_message') . '</p>',
            //'#visible' => FALSE,
          ];
        }

        $elements['#attributes']['class'][] = 'er-enhanced-hideui-requested';
        $elements['#attached']['library'][] = 'ahs_er_enhanced/hideui';
        //$elements['#attached']['drupalSettings']['ahs_er_enhanced']['hideui']['preview_empty_message'] = $this->getSetting('preview_empty_message');
      }
    }
    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  protected function getAutocreateBundle() {
    $bundle = NULL;
    if ($this->getSelectionHandlerSetting('auto_create')) {
      // If there's only one target bundle, use it.
      $target_bundles = $this->getSelectionHandlerSetting('target_bundles');
      if (count($target_bundles) == 1) {
        $bundle = reset($target_bundles);
      }
      // Otherwise use the autocreate bundle stored in selection handler settings.
      elseif (!$bundle = $this->getSelectionHandlerSetting('auto_create_bundle')) {
        // If no bundle has been set as auto create target means that there is
        // an inconsistency in entity reference field settings.
        trigger_error(sprintf(
          "The 'Create referenced entities if they don't already exist' option is enabled but a specific destination bundle is not set. You should re-visit and fix the settings of the '%s' (%s) field.",
          $this->fieldDefinition->getLabel(),
          $this->fieldDefinition->getName()
        ), E_USER_WARNING);
      }
    }
    return $bundle;
  }

}
