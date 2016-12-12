<?php

namespace Drupal\ahs_discussions\Plugin\Field\FieldWidget;

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
 *   id = "ahs_entity_reference_with_views_autocreate",
 *   label = @Translation("Autocomplete with views autocreate"),
 *   description = @Translation("An entity reference autocomplete widget that supports autocreate with views."),
 *   field_types = {
 *     "entity_reference"
 *   },
 *   list_class = "\Drupal\Core\Field\EntityReferenceFieldItemList",
 * )
 */
class EntityReferenceWithViewsAutocreate extends EntityReferenceAutocompleteWidget {



  /**
   * {@inheritdoc}
   */
  /*
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element = parent::formElement($items, $delta, $element, $form, $form_state);
   return $element;
  }
*/
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
