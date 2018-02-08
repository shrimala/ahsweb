<?php
/**
 * @file
 * Contains \Drupal\bootstrap\Plugin\Preprocess\BootstrapPanel.
 */

namespace Drupal\ahs_bootstrap\Plugin\Preprocess;

use Drupal\bootstrap\Annotation\BootstrapPreprocess;
use Drupal\bootstrap\Utility\Variables;
use Drupal\bootstrap\Utility\Element;
use Drupal\bootstrap\Plugin\Preprocess\BootstrapPanel as BootstrapPanelBase;

/**
 * Pre-processes variables for the "bootstrap_panel" theme hook.
 *
 * @ingroup plugins_preprocess
 *
 * @BootstrapPreprocess("bootstrap_panel")
 */
class BootstrapPanel extends BootstrapPanelBase {

  /**
   * {@inheritdoc}
   */
  protected function preprocessVariables(Variables $variables) {
    if ($variables['collapsible'] && $variables['collapsed']) {
      $variables['#attached']['library'][] = 'ahs_bootstrap/panel-enhance';
    }
    parent::preprocessVariables($variables);
  }

}
