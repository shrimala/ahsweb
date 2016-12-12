<?php

namespace Drupal\changes\Plugin\diff\Layout;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Datetime\DateFormatter;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\diff\DiffEntityComparison;
use Drupal\diff\DiffEntityParser;
use Drupal\diff\DiffLayoutBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use HtmlDiffAdvancedInterface;
use Drupal\Core\PhpStorage\PhpStorageFactory;

/**
 * @DiffLayoutBuilder(
 *   id = "changes",
 *   label = @Translation("Condensed change record"),
 * )
 */
class changesDiffLayout extends DiffLayoutBase {

  /**
   * The renderer.
   *
   * @var \Drupal\Core\Render\RendererInterface
   */
  protected $renderer;

  /**
   * The diff entity comparison service.
   *
   * @var \Drupal\diff\DiffEntityComparison
   */
  protected $entityComparison;

  /**
   * The request stack.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $requestStack;

  /**
   * The html diff service.
   *
   * @var \HtmlDiffAdvancedInterface
   */
  protected $htmlDiff;

  /**
   * Constructs a SplitFieldsDiffLayout object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config
   *   The configuration factory object.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\diff\DiffEntityParser $entity_parser
   *   The entity parser.
   * @param \Drupal\Core\DateTime\DateFormatter $date
   *   The date service.
   * @param \Drupal\Core\Render\RendererInterface $renderer
   *   The renderer.
   * @param \Drupal\diff\DiffEntityComparison $entity_comparison
   *   The diff entity comparison service.
   * @param \Symfony\Component\HttpFoundation\RequestStack $request_stack
   *   The request stack.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, ConfigFactoryInterface $config, EntityTypeManagerInterface $entity_type_manager, DiffEntityParser $entity_parser, DateFormatter $date, RendererInterface $renderer, DiffEntityComparison $entity_comparison, RequestStack $request_stack, HtmlDiffAdvancedInterface $html_diff) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $config, $entity_type_manager, $entity_parser, $date);
    $this->renderer = $renderer;
    $this->entityComparison = $entity_comparison;
    $this->requestStack = $request_stack;
    $storage = PhpStorageFactory::get('html_purifier_serializer');
    if (!$storage->exists('cache.php')) {
      $storage->save('cache.php', 'dummy');
    }
    $html_diff->getConfig()->setPurifierCacheLocation(dirname($storage->getFullPath('cache.php')));
    $this->htmlDiff = $html_diff;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('config.factory'),
      $container->get('entity_type.manager'),
      $container->get('diff.entity_parser'),
      $container->get('date.formatter'),
      $container->get('renderer'),
      $container->get('diff.entity_comparison'),
      $container->get('request_stack'),
      $container->get('diff.html_diff')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build(EntityInterface $left_revision, EntityInterface $right_revision, EntityInterface $entity) {
    
    $fields = $this->entityComparison->compareRevisions($left_revision, $right_revision);

    // Build the diff rows for each field and append the field rows
    // to the table rows.
    $diff_rows = [];
    foreach ($fields as $field) {
      $field_row = [];
      $field_row['label'] = [
        'data' => !empty($field['#name']) ? $field['#name'] : '',
        'class' => ['field-name'],
      ];

      // Strip HTML
      $field_settings = $field['#settings'];
      if (!empty($field_settings['settings']['markdown'])) {
        $field['#data']['#left'] = $this->applyMarkdown($field_settings['settings']['markdown'], $field['#data']['#left']);
        $field['#data']['#right'] = $this->applyMarkdown($field_settings['settings']['markdown'], $field['#data']['#right']);
      }
      // In case the settings are not loaded correctly use drupal_html_to_text
      // to avoid any possible notices when a user clicks on markdown.
      else {
        $field['#data']['#left'] = $this->applyMarkdown('drupal_html_to_text', $field['#data']['#left']);
        $field['#data']['#right'] = $this->applyMarkdown('drupal_html_to_text', $field['#data']['#right']);
      }

      // Process the array (split the strings into single line strings)
      // and get line counts per field.
      $this->entityComparison->processStateLine($field);

      $field_diff_rows = $this->entityComparison->getRows(
        $field['#data']['#left'],
        $field['#data']['#right']
      );

      $final_diff = [];
      $row_count = 0;
      foreach ($field_diff_rows as $key => $value) {
        $html_1 = isset($field_diff_rows[$key][1]['data']) ? $this->mb_trim(implode(' ', $field_diff_rows[$key][1]['data'])) : NULL;
        $html_2 = isset($field_diff_rows[$key][3]['data']) ? $this->mb_trim(implode(' ', $field_diff_rows[$key][3]['data'])) : NULL;
        // Ignore empty lines
        $html_1 = ($html_1 == '&#160;' || $html_1 == '&nbsp;') ? '' : $html_1;
        $html_2 = ($html_2 == '&#160;' || $html_1 == '&nbsp;') ? '' : $html_2;
        if (!empty($html_1) || !empty($html_2)) {
          $row_count++;
          $this->htmlDiff->setOldHtml($html_1);
          $this->htmlDiff->setNewHtml($html_2);
          $this->htmlDiff->build();
          $final_diff[] = [
            '#markup' => $this->htmlDiff->getDifference(),
          ];
        }
      }

      // Add the field row to the table only if there are changes to that field.
      if (!empty($final_diff)) {
        if ($row_count > 6) {
          $final_diff = [
            '#type' => 'details',
            '#title' => t('Show changes'),
            'details' => $final_diff,
          ];
        }
        $field_row['diff'] = [
          'data' => $final_diff,
        ];
        $diff_rows[] = $field_row;
      }
    }

    $build['diff'] = [
      '#type' => 'table',
      '#rows' => $diff_rows,
      '#weight' => 10,
      '#empty' => $this->t('No visible changes'),
      '#attributes' => [
        'class' => ['diff'],
      ],
    ];

    return $build;
  }

  /**
   * Trim characters from either (or both) ends of a string in a way that is
   * multibyte-friendly.
   *
   * Mostly, this behaves exactly like trim() would: for example supplying 'abc' as
   * the charlist will trim all 'a', 'b' and 'c' chars from the string, with, of
   * course, the added bonus that you can put unicode characters in the charlist.
   *
   * We are using a PCRE character-class to do the trimming in a unicode-aware
   * way, so we must escape ^, \, - and ] which have special meanings here.
   * As you would expect, a single \ in the charlist is interpretted as
   * "trim backslashes" (and duly escaped into a double-\ ). Under most circumstances
   * you can ignore this detail.
   *
   * As a bonus, however, we also allow PCRE special character-classes (such as '\s')
   * because they can be extremely useful when dealing with UCS. '\pZ', for example,
   * matches every 'separator' character defined in Unicode, including non-breaking
   * and zero-width spaces.
   *
   * It doesn't make sense to have two or more of the same character in a character
   * class, therefore we interpret a double \ in the character list to mean a
   * single \ in the regex, allowing you to safely mix normal characters with PCRE
   * special classes.
   *
   * *Be careful* when using this bonus feature, as PHP also interprets backslashes
   * as escape characters before they are even seen by the regex. Therefore, to
   * specify '\\s' in the regex (which will be converted to the special character
   * class '\s' for trimming), you will usually have to put *4* backslashes in the
   * PHP code - as you can see from the default value of $charlist.
   *
   * @param string
   * @param charlist list of characters to remove from the ends of this string.
   * @param boolean trim the left?
   * @param boolean trim the right?
   * @return String
   */
  protected function mb_trim($string, $charlist='\\\\s', $ltrim=true, $rtrim=true)
  {
    $both_ends = $ltrim && $rtrim;

    $char_class_inner = preg_replace(
      array( '/[\^\-\]\\\]/S', '/\\\{4}/S' ),
      array( '\\\\\\0', '\\' ),
      $charlist
    );

    $work_horse = '[' . $char_class_inner . ']+';
    $ltrim && $left_pattern = '^' . $work_horse;
    $rtrim && $right_pattern = $work_horse . '$';

    if($both_ends)
    {
      $pattern_middle = $left_pattern . '|' . $right_pattern;
    }
    elseif($ltrim)
    {
      $pattern_middle = $left_pattern;
    }
    else
    {
      $pattern_middle = $right_pattern;
    }

    return preg_replace("/$pattern_middle/usSD", '', $string);
    }

}
