<?php

namespace Drupal\changes\Plugin\diff\Layout;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Datetime\DateFormatter;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\diff\DiffEntityComparison;
use Drupal\diff\DiffEntityParser;
use Drupal\diff\DiffLayoutBase;
use Drupal\user\UserInterface;
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
    $html_diff->getConfig()
      ->setPurifierCacheLocation(dirname($storage->getFullPath('cache.php')));
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
  public function build(ContentEntityInterface $left_revision, ContentEntityInterface $right_revision, ContentEntityInterface $entity) {

    $fields = $this->entityComparison->compareRevisions($left_revision, $right_revision);
    // Build the diff rows for each field and append the field rows
    // to the table rows.
    $diff_rows = [];
    foreach ($fields as $field_key => $field) {
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

      // If nothing has changed, skip this field. This isn't completely reliable,
      // as some things are not exactly equal but may still equivalent for diff
      // puposes, e.g. references in a different order.
      if ($field['#data']['#left'] === $field['#data']['#right']) {
        continue;
      }

      // Extract field machine name from $field_key as $field_id
      list (, $field_id) = explode(':', $field_key);
      list (, $field_id) = explode('.', $field_id);

      switch ($field_id) {
        case 'title':
          $this->addSingleton($diff_rows, $field_id,
            'Changed the title: <span class="diffchange"' . $this->processSingleLineText($field) . '.</span>');
          break;
        case 'body':
          $diff_rows[$field_id] = [
            '#type' => 'details',
            '#title' => t('Changed the summary <span class="ahs-expand"> (show changes)</span>'),
            'details' => $this->processMultiLineText($field),
          ];
          break;
        case 'field_private':
          $this->addSingleton($diff_rows, $field_id,
            'Marked as ' . (($field['#data']['#right'] === 'Yes') ? 'private' : 'not private') . '.');
          break;
        case 'field_finished':
          $this->addSingleton($diff_rows, $field_id,
            'Marked as ' . (($field['#data']['#right'] === 'Yes') ? 'finished' : 'not finished') . '.');
          break;
        case 'field_help_wanted':
          $this->addSingleton($diff_rows, $field_id,
            'Marked as ' . (($field['#data']['#right'] === 'Yes') ? 'help wanted' : 'help not wanted') . '.');
          break;
        case 'promote':
          $this->addSingleton($diff_rows, $field_id,
            (($field['#data']['#right'] === 'Yes') ? 'Promoted to the front page' : 'Demoted from the front page') . '.');
          break;
        case 'field_top_level_category':
          $this->addSingleton($diff_rows, $field_id,
            (($field['#data']['#right'] === 'Yes') ? 'Marked as a top-level category' : 'Marked as not a top-level category') . '.');
          break;
        case 'field_participants':
          $this->addParticipants($diff_rows, $field_id, $field, FALSE, 'Removed participant ', 'Removed participants ', $right_revision->getRevisionUser());
          $this->addParticipants($diff_rows, $field_id, $field, TRUE, 'Added participant ', 'Added participants ', $right_revision->getRevisionUser());
          break;
        case 'field_assigned':
          $this->addReferences($diff_rows, $field_id, $field, FALSE, 'Unassigned as a task from ', 'Unassigned as a task from ');
          $this->addReferences($diff_rows, $field_id, $field, TRUE, 'Assigned as a task to ', 'Assigned as a task to ');
          break;
        case 'field_files':
          $this->addReferences($diff_rows, $field_id, $field, FALSE, 'Removed file ', 'Removed files ');
          $this->addReferences($diff_rows, $field_id, $field, TRUE, 'Added file ', 'Added files ');
          break;
        case 'field_children':
          $this->addReferences($diff_rows, $field_id, $field, FALSE, 'Removed from being part of this ', 'Removed from being parts of this ', TRUE);
          $this->addReferences($diff_rows, $field_id, $field, TRUE, 'Added as part of this ', 'Added as parts of this ', TRUE);
          break;
        case 'field_parents':
          $this->addReferences($diff_rows, $field_id, $field, FALSE, 'Marked this as not part of ', 'Marked this as not part of ', TRUE);
          $this->addReferences($diff_rows, $field_id, $field, TRUE, 'Marked this as part of ', 'Marked this as part of ', TRUE);
          break;
      }
    }

    foreach ($diff_rows as $field_id => $diff) {
      $build['diff'][$field_id] = [
        '#type' => 'container',
        'change' => $diff,
        '#attributes' => [
          'class' => ['change', $field_id],
        ],
      ];
    }
    $build['diff']['#type'] = 'container';
    $build['diff']['#attributes']['class'] = ['changes', 'text-muted', 'small'];

    if (count($diff_rows) === 0) {
      $build['diff']['empty'] = [
        '#markup' => 'No visible changes.'
      ];
    }

    return $build;
  }

  protected function addSingleton(&$diff_rows, $field_id, $value) {
    if (!is_null($value)) {
      $diff_rows[$field_id] = [
        '#markup' => $value,
      ];
    }
  }

  protected function addMulti(&$diff_rows, $field_id, $values, $singular, $plural, $quote = FALSE) {
    // Format the items as a nice string e.g. "Done something(s) to A, B and C."
    if (count($values) > 0) {
      // Optionally, wrap the values in quotes
      if ($quote) {
        foreach ($values as $key => $value) {
          $values[$key] = "'" . $value . "'";
        }
      }

      if (count($values) === 1) {
        $text = $singular . array_values($values)[0] . '.';
      }
      else {
        $first = array_slice($values, 0, -1);
        $last = array_slice($values, -1)[0];
        if (count($first) > 1) {
          $firstText = implode(", ", $first);
        }
        else {
          $firstText = $first[0];
        }
        $text = $plural . $firstText . ' and ' . $last . '.';
      }
      $this->addSingleton($diff_rows, $field_id, $text);
    }
  }

  protected function addReferences(&$diff_rows, $field_id, $field, $additions, $singular, $plural, $quote = FALSE) {
    $values = $this->processReferences($field, $additions);
    $this->addMulti($diff_rows, $field_id, $values, $singular, $plural, $quote);
  }

  protected function addParticipants(&$diff_rows, $field_id, $field, $additions, $singular, $plural, UserInterface $editor) {
    $values = $this->processReferences($field, $additions);
    foreach($values as $key => $value) {
      if ($value === $editor->getDisplayName()) {
          unset($values[$key]);
        }
    }
    $this->addMulti($diff_rows, $field_id, $values, $singular, $plural);
  }

  protected function processReferences($field, $additions, $quote = FALSE) {
    // Find added or removed items
    $right = explode(PHP_EOL, $field['#data']['#right']);
    $left = explode(PHP_EOL, $field['#data']['#left']);
    if ($additions) {
      $modifications = array_diff($right, $left);
    }
    else {
      $modifications = array_diff($left, $right);
    }
    // Remove any empty string items
    $modifications = array_filter($modifications, 'strlen');
    // Sort the array, otherwise the order can be rndom which makes testing hard.
    sort($modifications);
    return $modifications;
  }


  protected function processMultiLineText($field) {
    // Process the array (split the strings into single line strings)
    // and get line counts per field.
    $this->entityComparison->processStateLine($field);
    $field_diff_rows = $this->entityComparison->getRows(
      $field['#data']['#left'],
      $field['#data']['#right']
    );
    $final_diff = [];
    foreach ($field_diff_rows as $key => $value) {
      $processedText = $this->processText($field_diff_rows[$key]);
      if (!is_null($processedText)) {
        $final_diff[] = [
          '#markup' => $processedText,
        ];
      }
    }
    return $final_diff;
  }

  protected function processSingleLineText($field) {
    $row = [
      NULL,
      [
        'data' =>
          [
            $field['#data']['#left']
          ]
      ],
      NULL,
      [
        'data' => [
          $field['#data']['#right']
        ]
      ]
    ];
    $text = $this->processText($row);
    return $text;
  }


  protected function processText($row) {
    $html_1 = isset($row[1]['data']) ? $this->mb_trim(implode(' ', $row[1]['data'])) : NULL;
    $html_2 = isset($row[3]['data']) ? $this->mb_trim(implode(' ', $row[3]['data'])) : NULL;
    // Ignore empty lines
    $html_1 = ($html_1 == '&#160;' || $html_1 == '&nbsp;') ? '' : $html_1;
    $html_2 = ($html_2 == '&#160;' || $html_1 == '&nbsp;') ? '' : $html_2;
    if (!empty($html_1) || !empty($html_2)) {
      $this->htmlDiff->setOldHtml($html_1);
      $this->htmlDiff->setNewHtml($html_2);
      $this->htmlDiff->build();
      return $this->htmlDiff->getDifference();
    }
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
  protected function mb_trim($string, $charlist = '\\\\s', $ltrim = true, $rtrim = true) {
    $both_ends = $ltrim && $rtrim;

    $char_class_inner = preg_replace(
      array('/[\^\-\]\\\]/S', '/\\\{4}/S'),
      array('\\\\\\0', '\\'),
      $charlist
    );

    $work_horse = '[' . $char_class_inner . ']+';
    $ltrim && $left_pattern = '^' . $work_horse;
    $rtrim && $right_pattern = $work_horse . '$';

    if ($both_ends) {
      $pattern_middle = $left_pattern . '|' . $right_pattern;
    }
    elseif ($ltrim) {
      $pattern_middle = $left_pattern;
    }
    else {
      $pattern_middle = $right_pattern;
    }

    return preg_replace("/$pattern_middle/usSD", '', $string);
  }

}