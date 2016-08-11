<?php
/**
 * @file
 * Contains \EntityContext.
 */
use Behat\Gherkin\Node\TableNode;
use Drupal\Core\Entity\EntityInterface;
use Drupal\DrupalExtension\Context\RawDrupalContext;
use Behat\Behat\Context\SnippetAcceptingContext;

/**
 * Context for creating and cleaning up entities of any type.
 */
class EntityContext extends RawDrupalContext implements SnippetAcceptingContext {
  /**
   * Entities created during the scenario, organized by type.
   *
   * @var array
   */
  protected $entities = [];

  /**
   * Creates a set of entities of a given type.
   *
   * @param string $entity_type
   *   The entity type to create.
   * @param \Behat\Gherkin\Node\TableNode $table
   *   The entities to create.
   *
   * @Given :entity_type entities:
   */
  public function createEntities($entity_type, TableNode $table) {
    foreach ($table as $row) {
      $entity = \Drupal::entityTypeManager()->getStorage($entity_type)->create($row);
      $entity->save();
      $this->queueEntity($entity);
    }
  }
 
  /**
   * Queues an entity to be deleted at the end of the scenario.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity to queue.
   */
  public function queueEntity(EntityInterface $entity) {
    $entity_type = $entity->getEntityTypeId();
    $this->entities[$entity_type][] = $entity;
  }
  /**
   * Deletes all entities created during the scenario.
   *
   * @AfterScenario
   */
  public function cleanEntities() {
    foreach ($this->entities as $entity_type => $entities) {
      /** @var \Drupal\Core\Entity\EntityInterface $entity */
      foreach ($entities as $entity) {
        // Clean up the entity's alias, if there is one.
        $path = '/' . $entity->toUrl()->getInternalPath();
        $alias = \Drupal::service('path.alias_manager')->getAliasByPath($path);
        if ($alias != $path) {
          \Drupal::service('path.alias_storage')->delete(['alias' => $alias]);
        }
        $entity->delete();
      }
    }
  }

}
