<?php

use Drupal\DrupalExtension\Context\RawDrupalContext;
use Drupal\Core\User;
use Behat\Behat\Context\SnippetAcceptingContext;
use Drupal\Group\Entity\GroupMembership;
use Drupal\group\Entity\GroupContent;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;

/**
 * Steps for the Group module.
 */
class GroupContext extends RawDrupalContext implements SnippetAcceptingContext {

  /* Keeps track of group values for usage and later cleanup */
  protected $current_group;
  protected $groups = array();

  /**
   * Initializes context.
   *
   * Every scenario gets its own context instance.
   * You can also pass arbitrary arguments to the
   * context constructor through behat.yml.
   */
  public function __construct() {
  }

  /**
   * @Given I am viewing a group of type :arg1 with the title :arg2
   *
   * Creates and takes users to a Group with title of group_type
   */
  public function createNewGroup($group_type, $title) {
    $group = entity_create('group', array(
      'type' => $group_type,
      'label' => $title,
      'uid' => 1,
    ));
    $group->save();

    $this->current_group = $group;
    $this->groups[] = $group;

    $this->getSession()->visit($this->locatePath('/group/' . $group->id()));
  }

  /**
   * @Then I view the path :relative_path relative to my current group
   *
   * Creates and takes users to a Group with title of group_type
   */
  public function viewGroupPage($relative_path) {
    if (is_null($this->current_group)) {
      $message = sprintf('No current group set to view path relative to');
      throw new \Exception($message);
    }

    $path_to_visit = '/group/' . $this->current_group->id();
    $path_to_visit .= '/' . $relative_path;

    $this->getSession()->visit($this->locatePath($path_to_visit));
  }

  /**
   * @Then I am a member of the current group with the role :group_role
   *
   * Adds current user to the current group with the specified role.
   */
  public function joinCurrentGroupWithRole($group_role) {

    $group = $this->current_group;
    $group_type = $group->get('type')->getvalue()[0]['target_id'];

    $account = \Drupal::entityTypeManager()->getStorage('user')->load(
      $this->getUserManager()->getCurrentUser()->uid
    );

    $values = ['group_roles' => $group_type . '-' . $group_role];
    $group->addMember($account, $values);
  }

  /**
   * @Then I am a member of the current group
   *
   * Adds user to the current group.
   */
  public function joinCurrentGroup() {
    $plugin = $this->current_group->getGroupType()->getContentPlugin('group_membership');

    // Pre-populate a group membership with the current user.
    $group_content = GroupContent::create([
      'type' => $plugin->getContentTypeConfigId(),
      'gid' => $this->current_group->id(),
      'entity_id' => $this->getUserManager()->getCurrentUser()->uid,
    ]);

    $group_content->save();
  }

  /**
   * Unset current group after scenario as cleanup.
   *
   * @AfterScenario
   */
  public function cleanCurrentGroup() {
    if (!is_null($this->current_group)) {
      $this->current_group = NULL;
    }
  }

  /**
   * Remove groups after scenario as cleanup.
   *
   * @AfterScenario
   */
  public function cleanCurrentGroupst() {
    if (!empty($this->groups)) {
      foreach ($this->groups as $group) {
        $this->getDriver()->entityDelete('group', $group);
      }
    }
  }
}