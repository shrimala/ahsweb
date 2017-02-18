<?php
/**
 * @file
 * Contains \Drupal\changes\Plugin\Field\FieldFormatter\diff1Formatter.
 */
 
namespace Drupal\changes\Plugin\Field\FieldFormatter;
 
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\NodeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Url;
use Drupal\Component\Utility\Xss;
use Drupal\Component\Utility\SafeMarkup;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\diff\DiffEntityComparison;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Utility\LinkGeneratorInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\diff\Controller\NodeRevisionController;
/**
 * Plugin implementation of the 'changes' formatter.
 *
 * @FieldFormatter (
 *   id = "changes",
 *   label = @Translation("Changes"),
 *   field_types = {
 *     "changes"
 *   }
 * )
 */
class changesFormatter extends FormatterBase  implements ContainerFactoryPluginInterface {
 /**
  * The layout manager service for diff.
  *
  * @var \Drupal\diff\DiffLayoutManager
  */
 protected $diffLayoutManager;

 /**
  * Constructs a changesFormatter object.
  *
  * @param string $plugin_id
  * The plugin_id for the formatter.
  * @param mixed $plugin_definition
  * The plugin implementation definition.
  * @param \Drupal\Core\Field\FieldDefinitionInterface $field_definition
  * The definition of the field to which the formatter is associated.
  * @param array $settings
  * The formatter settings.
  * @param string $label
  * The formatter label display setting.
  * @param string $view_mode
  * The view mode.
  * @param array $third_party_settings
  * Any third party settings settings.
  * @param \Drupal\diff\DiffLayoutManager $layout_manager
  * The diff layout manager service.
  */
  public function __construct($plugin_id, $plugin_definition, FieldDefinitionInterface $field_definition, array $settings, $label, $view_mode, array $third_party_settings, $layout_manager) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $label, $view_mode, $third_party_settings);
    $this->diffLayoutManager = $layout_manager;
  }
 /**
  * {@inheritdoc}
  */
  public static function create(ContainerInterface $container,array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['label'],
      $configuration['view_mode'],
      $configuration['third_party_settings'],
      $container->get('plugin.manager.diff.layout')

    );
  }
  
 /**
  * {@inheritdoc}
  */
  public function viewElements(FieldItemListInterface $items, $langcode = NULL) {
   $elements = array();

    // This field should be on a comment.
    // Get the entity type of the entity this comment is attached to.
    $entityType = $items->getEntity()->get('entity_type')->value;


   foreach ($items as $delta => $item) {
     drupal_set_message("Delta:" . $delta . '-' . $items->getEntity()->id());
     if (!empty($item->right_rid)) {
       drupal_set_message("Notemptyright" . '-' . $items->getEntity()->id());
       $storage = \Drupal::entityManager()->getStorage($entityType);
       $right_revision = $storage->loadRevision($item->right_rid);
       $entity = $storage->load($right_revision->id());
       if (!empty($item->left_rid)) {
         drupal_set_message("NotEmptyleft" . '-' . $items->getEntity()->id());
         // We have a pair of revisions
         //$left_revision = $storage->loadRevision($item->left_rid);
         //$plugin = $this->diffLayoutManager->createInstance('changes');
         //$elements[$delta] = $plugin->build($left_revision, $right_revision, $entity);
         $elements[$delta] = [
           '#markup' => '<p>diff</p>',
         ];
       }
       else {
         drupal_set_message("Emptyleft" . '-' . $items->getEntity()->id());
         $loose = ($item->right_rid == $entity->getRevisionId()) ? 'TRUE' : 'FALSE';
         $strict = ($item->right_rid === $entity->getRevisionId()) ? 'TRUE' : 'FALSE';
         drupal_set_message("right_rid" . $item->right_rid . '-' . $items->getEntity()->id());
         drupal_set_message("getRevisionId" . $entity->getRevisionId() . '-' . $items->getEntity()->id());
         drupal_set_message("Strict:" . $strict . '-' . $items->getEntity()->id());
         drupal_set_message("Loose:" . $loose . '-' . $items->getEntity()->id());

         //$elements[$delta] = [
         //  '#type' => 'details',
         //  '#title' => 'a:' . $item->right_rid . ' b:' . $entity->getRevisionId() . ' ' . $loose . ' ' . $strict
         //];
         // We have just a single revision, the original of this entity.
         // Only link to it if it is not the current revision.
         if ($item->right_rid !== $entity->getRevisionId()) {
           drupal_set_message("RightNotCurrent" . '-' . $items->getEntity()->id());
           $elements[$delta] = [
             '#type' => 'details',
             '#title' => 'Original version',
           ];
           $elements[$delta] = [
             '#markup' => '<p>test</p>',
           ];

           // Trigger exclusion of interactive items like on preview.
           //$right_revision->in_preview = TRUE;
           //$view_builder = \Drupal::entityTypeManager()
           //  ->getViewBuilder($entityType);
           //$original = $view_builder->view($right_revision);
           //$elements[$delta] = [
           //  '#type' => 'details',
           //  '#title' => 'Original version',
           //];
           //$elements[$delta]['original'] = $original;
         }
       }
     }
    }
    return $elements;
  }

}
