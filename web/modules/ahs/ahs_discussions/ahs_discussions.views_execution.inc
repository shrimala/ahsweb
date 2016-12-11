<?php

function ahs_discussions_views_query_alter(Drupal\views\ViewExecutable $view, Drupal\views\Plugin\views\query\Sql $query) {
  if ($view->id() == 'discussions') {
    $uid = Drupal::currentUser()->id();
    //if ($view->getDisplay()->display['id'] == 'help') {
      // Exclude discussion from 'Help wanted' section if
      // the current user has already participated or been assigned it.
      // Maybe not a good idea - admins need to monitor what is being show there.
      // ahs_discussions_views_where_value_in_field($query, $uid, 'node__field_participants', 'field_participants_target_id', TRUE);
      // ahs_discussions_views_where_value_in_field($query, $uid, 'node__field_assigned', 'field_assigned_target_id', TRUE);
    //}
    if ($view->getDisplay()->display['id'] == 'my_discussions') {
      // Exclude discussions from 'My discussions' if they are already listed
      // assigned to me and therefore listed  instead under 'My tasks'
      ahs_discussions_views_where_value_in_field($query, $uid, 'node__field_assigned', 'field_assigned_target_id', TRUE);
    }
    if ($view->getDisplay()->display['id'] == 'search') {
      // Only show discussions in search results if they are either not private,
      // or the current user is a participant.
      $query->setWhereGroup('OR', 0);
      ahs_discussions_views_where_value_in_field($query, $uid, 'node__field_participants', 'field_participants_target_id', FALSE);
      ahs_discussions_views_where_value_in_field($query, $uid, 'node__field_assigned', 'field_assigned_target_id', FALSE);
      $query->addTable('node__field_private');
      $query->addWhere(0, 'node__field_private.field_private_value', 0, '=');
    }
  }
}

// Filter nodes where a specified value is present in a specified field.
function ahs_discussions_views_where_value_in_field(&$query, $value, $table, $field, $exclude = FALSE) {
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
  $comparison = $exclude ? "IS NULL" : "IS NOT NULL";
  $query->addWhere(0, $field, NULL, $comparison);
}