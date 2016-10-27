<?php

namespace Drupal\ahs_discussions\Plugin\EntityReferenceSelection;

use Drupal\Core\Database\Query\SelectInterface;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Entity\EntityReferenceSelection\SelectionInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Plugin\PluginBase;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Url;
use Drupal\views\Views;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Drupal\views\Plugin\EntityReferenceSelection\ViewsSelection;
use Drupal\Core\Entity\EntityReferenceSelection\SelectionWithAutocreateInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Plugin implementation of the 'selection' entity_reference.
 *
 * @EntityReferenceSelection(
 *   id = "er_enhanced_views",
 *   label = @Translation("Views enhanced: Can autocreate new entities"),
 *   group = "er_enhanced_views",
 *   weight = 10
 * )
 */
class EnhancedViewsSelection extends ViewsSelection implements SelectionInterface, SelectionWithAutocreateInterface, ContainerFactoryPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    /*
    $selection_handler_settings = $this->configuration['handler_settings'];
    $view_settings = !empty($selection_handler_settings['view']) ? $selection_handler_settings['view'] : array();
    $displays = Views::getApplicableViews('entity_reference_display');
    // Filter views that list the entity type we want, and group the separate
    // displays by view.
    $entity_type = $this->entityManager->getDefinition($this->configuration['target_type']);
    $view_storage = $this->entityManager->getStorage('view');

    $options = array();
    foreach ($displays as $data) {
      list($view_id, $display_id) = $data;
      $view = $view_storage->load($view_id);
      if (in_array($view->get('base_table'), [$entity_type->getBaseTable(), $entity_type->getDataTable()])) {
        $display = $view->get('display');
        $options[$view_id . ':' . $display_id] = $view_id . ' - ' . $display[$display_id]['display_title'];
      }
    }

    // The value of the 'view_and_display' select below will need to be split
    // into 'view_name' and 'view_display' in the final submitted values, so
    // we massage the data at validate time on the wrapping element (not
    // ideal).
    $form['view']['#element_validate'] = array(array(get_called_class(), 'settingsFormValidate'));

    if ($options) {
      $default = !empty($view_settings['view_name']) ? $view_settings['view_name'] . ':' . $view_settings['display_name'] : NULL;
      $form['view']['view_and_display'] = array(
        '#type' => 'select',
        '#title' => $this->t('View used to select the entities'),
        '#required' => TRUE,
        '#options' => $options,
        '#default_value' => $default,
        '#description' => '<p>' . $this->t('Choose the view and display that select the entities that can be referenced.<br />Only views with a display of type "Entity Reference" are eligible.') . '</p>',
      );

      $default = !empty($view_settings['arguments']) ? implode(', ', $view_settings['arguments']) : '';
      $form['view']['arguments'] = array(
        '#type' => 'textfield',
        '#title' => $this->t('View arguments'),
        '#default_value' => $default,
        '#required' => FALSE,
        '#description' => $this->t('Provide a comma separated list of arguments to pass to the view.'),
      );
    }
    else {
      if ($this->currentUser->hasPermission('administer views') && $this->moduleHandler->moduleExists('views_ui')) {
        $form['view']['no_view_help'] = array(
          '#markup' => '<p>' . $this->t('No eligible views were found. <a href=":create">Create a view</a> with an <em>Entity Reference</em> display, or add such a display to an <a href=":existing">existing view</a>.', array(
            ':create' => Url::fromRoute('views_ui.add')->toString(),
            ':existing' => Url::fromRoute('entity.view.collection')->toString(),
          )) . '</p>',
        );
      }
      else {
        $form['view']['no_view_help']['#markup'] = '<p>' . $this->t('No eligible views were found.') . '</p>';
      }
    }
    */
    $form = parent::buildConfigurationForm($form, $form_state);
    $entity_type = $this->entityManager->getDefinition($this->configuration['target_type']);
    $selection_handler_settings = $this->configuration['handler_settings'];
    // Merge-in default values.
    $selection_handler_settings += array(
      'auto_create' => FALSE,
      'auto_create_bundle' => NULL,
    );

    $form['auto_create'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t("Create referenced entities if they don't already exist"),
      '#default_value' => $selection_handler_settings['auto_create'],
    );
    if ($entity_type->hasKey('bundle')) {
      $entity_type = $this->configuration['target_type'];
      $bundles = $this->entityManager->getBundleInfo($entity_type);
      $bundle_options = array();
      foreach ($bundles as $bundle_name => $bundle_info) {
        $bundle_options[$bundle_name] = $bundle_info['label'];
      }
      natsort($bundle_options);
      $form['auto_create_bundle'] = [
        '#type' => 'select',
        '#title' => $this->t('Store new items in'),
        '#options' => $bundle_options,
        '#default_value' => $selection_handler_settings['auto_create_bundle'],
        '#access' => count($bundles) > 1,
        '#states' => [
          'visible' => [
            ':input[name="settings[handler_settings][auto_create]"]' => ['checked' => TRUE],
          ],
        ],
      ];
    }
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function createNewEntity($entity_type_id, $bundle, $label, $uid) {
    $entity_type = $this->entityManager->getDefinition($entity_type_id);
    $bundle_key = $entity_type->getKey('bundle');
    $label_key = $entity_type->getKey('label');
    $entity = $this->entityManager->getStorage($entity_type_id)->create(array(
      $bundle_key => $bundle,
      $label_key => $label,
    ));
    if ($entity instanceof EntityOwnerInterface) {
      $entity->setOwnerId($uid);
    }
    return $entity;
  }

  /**
   * {@inheritdoc}
   */
  public function validateReferenceableNewEntities(array $entities) {
    return array_filter($entities, function ($entity) {
      if (isset($this->configuration['handler_settings']['auto_create_bundle'])) {
        return ($entity->bundle() === $this->configuration['handler_settings']['auto_create_bundle']);
      }
      return TRUE;
    });
  }

}
