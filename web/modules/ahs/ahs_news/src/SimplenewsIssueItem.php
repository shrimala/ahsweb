<?php

namespace Drupal\ahs_news;

use Drupal\simplenews\Plugin\Field\FieldType\IssueItem;

class SimplenewsIssueItem extends IssueItem {

  public function setValue($values, $notify = TRUE) {
    $values['handler'] = 'ahs_news';
    $values['handler_settings']['id'] = $this->getEntity()->id();
    parent::setValue($values, $notify);
  }

}