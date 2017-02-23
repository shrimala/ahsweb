<?php

function ahs_discussions_views_query_alter(Drupal\views\ViewExecutable $view, Drupal\views\Plugin\views\query\Sql $query) {
  if ($view->id() == 'discussions') {
    $uid = Drupal::currentUser()->id();

    if ($view->getDisplay()->display['id'] == 'tasks') {
      // Include discussions in 'My tasks' only if assigned to current user
      ahs_discussions_views_where_value_in_field($query, $uid, 'node__field_assigned', 'field_assigned_target_id', TRUE);
      $view->element['#cache']['contexts'][]='user';
    }

    if ($view->getDisplay()->display['id'] == 'my_discussions') {
      // Include discussions in 'My discussions' only if current user is participant
      ahs_discussions_views_where_value_in_field($query, $uid, 'node__field_participants', 'field_participants_target_id', TRUE);

      // Exclude discussions from 'My discussions' if they are already assigned
      // to me but not finished, and are therefore listed under 'My tasks'
      $query->setWhereGroup('OR', 3);
      ahs_discussions_views_where_value_in_field($query, $uid, 'node__field_assigned', 'field_assigned_target_id', FALSE, 3);
      $query->addTable('node__field_finished');
      $query->addWhere(3, 'node__field_finished.field_finished_value', 1, '=');

      $view->element['#cache']['contexts'][]='user';
    }

    if ($view->getDisplay()->display['id'] == 'active') {
      // Only show discussions in 'Active' if they are not private,
      // and the current user is not a participant
      // (as in that case they would already be in My Tasks or My Discussions.
      ahs_discussions_views_where_value_in_field($query, $uid, 'node__field_participants', 'field_participants_target_id', FALSE);
      ahs_discussions_views_where_value_in_field($query, $uid, 'node__field_assigned', 'field_assigned_target_id', FALSE);
      $view->element['#cache']['contexts'][]='user';
    }

    if ($view->getDisplay()->display['id'] == 'search') {
      // Only show discussions in search results if they are either not private,
      // or the current user is a participant.
      $query->setWhereGroup('OR', 2);
      ahs_discussions_views_where_value_in_field($query, $uid, 'node__field_participants', 'field_participants_target_id', TRUE, 2);
      ahs_discussions_views_where_value_in_field($query, $uid, 'node__field_assigned', 'field_assigned_target_id', TRUE, 2);
      $query->addTable('node__field_private');
      $query->addWhere(2, 'node__field_private.field_private_value', 0, '=');
      $view->element['#cache']['contexts'][]='user';
    }
  }
}

// Filter nodes where a specified value is present in a specified field.
function ahs_discussions_views_where_value_in_field(&$query, $value, $table, $field, $exclude = TRUE, $group = 2) {
  $definition = [
    'table' => $table,
    'field' => 'entity_id',
    'left_table' => 'node_field_data',
    'left_field' => 'nid',
    'extra' => [
      0 => [
        'field' => $field,
        'value' => $value,
      ],
    ],
  ];
  $join = Drupal::service('plugin.manager.views.join')->createInstance('standard', $definition);
  $query->addRelationship($table . 'alias', $join, 'node');
  $comparison = $exclude ? "IS NOT NULL" : "IS NULL";
  $query->addWhere($group, $field, NULL, $comparison);
}