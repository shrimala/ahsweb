<?php

namespace Drupal\ahs_events\Plugin\Field\FieldFormatter;

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\datetime\Plugin\Field\FieldFormatter\DateTimeDefaultFormatter;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItem;
use Drupal\Core\Cache\Cache;

/**
 * Plugin extending the 'Custom' formatter for 'datetime' fields by
 * adding an option to use the venue's time zone.
 *
 * @FieldFormatter(
 *   id = "datetime_using_venue_timezone",
 *   label = @Translation("Using venue's timezone"),
 *   field_types = {
 *     "datetime"
 *   }
 * )
 */
class DateTimeUsingVenueTimezoneFormatter extends DateTimeDefaultFormatter {

  /**
   * The parent entity.
   *
   * @var \Drupal\Core\Entity\ContentEntityInterface
   */
  protected $entity;

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return array(
      'user_timezone_format_type' => 'medium',
      'add_user_timezone' => FALSE,
    ) + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $form = parent::settingsForm($form, $form_state);

    $form['timezone_override']['#options']['venue'] = 'Venue';

    $form['add_user_timezone'] = [
      '#type' => 'checkbox',
      '#title' => t("Also show in user's timezone"),
      '#default_value' => $this->getSetting('add_user_timezone'),
    ];

    $time = new DrupalDateTime();
    $format_types = $this->dateFormatStorage->loadMultiple();
    $options = [];
    foreach ($format_types as $type => $type_info) {
      $format = $this->dateFormatter->format($time->getTimestamp(), $type);
      $options[$type] = $type_info->label() . ' (' . $format . ')';
    }

    $form['user_timezone_format_type'] = array(
      '#type' => 'select',
      '#title' => t('Date format for user time zone message'),
      '#options' => $options,
      '#default_value' => $this->getSetting('user_timezone_format_type'),
      '#states' => [
        'visible' => [
          ':input[name="fields[' . $this->fieldDefinition->getName() . '][settings_edit_form][settings][add_user_timezone]"]' => ['checked' => TRUE],
        ],
      ],
    );

    return $form;
  }

  public function viewElements(FieldItemListInterface $items, $langcode) {
    $this->entity = $items->getEntity();
    $elements = parent::viewElements($items, $langcode);

    foreach ($elements as $delta => $element) {

      // Make the element provided by the parent be a child inside a container.
      unset($elements[$delta]);
      $elements[$delta]['#type'] = 'container';
      $elements[$delta]['in_venue_timezone'] = $element;
      $elements[$delta]['#attributes'] = $element['#attributes'];
      unset($elements[$delta]['in_venue_timezone']['#attributes']);
      if (isset($elements[$delta]['in_venue_timezone']['#attributes']['class'])) {
        $elements[$delta]['in_venue_timezone']['#attributes']['class'][] = 'in-venue-timezone';
      }
      else {
        $elements[$delta]['in_venue_timezone']['#attributes']['class'] = ['in-venue-timezone'];
      }
      $elements[$delta]['#cache'] = $elements[$delta]['in_venue_timezone']['#cache'];

      // Add cache tags to depend on the event or venue.
      $event = ahs_events_get_event($this->entity);
      $eventTags = empty($event) ? [] : $event->getCacheTags();
      $venue = ahs_events_get_venue($event);
      $venueTags = empty($venue) ? [] : $venue->getCacheTags();
      $tags = Cache::mergeTags($eventTags, $venueTags);
      if (isset($elements[$delta]['#cache']['tags'])) {
        $tags = Cache::mergeTags($tags, $elements[$delta]['#cache']['tags']);
      }
      $elements[$delta]['#cache']['tags'] = $tags;

      // If the user is outside the venue time zone, optionally add a sibling
      // inside the container, in the user's time zone.
      $userTimezone = drupal_get_user_timezone();
      $userIsDifferent = ($userTimezone !== $this->getVenueTimezone());
      if ($userIsDifferent && $this->getSetting('add_user_timezone') && $items[$delta]->date && $this->getFieldSetting('datetime_type') == 'datetime') {
        $date = $items[$delta]->date;
        $format_type = $this->getSetting('user_timezone_format_type');
        $date->setTimeZone(timezone_open($userTimezone));
        $output = $this->dateFormatter->format($date->getTimestamp(), $format_type, '', $userTimezone);
        $elements[$delta]['in_user_timezone'] = [
          '#markup' => "<span class='in-user-timezone'>($output in your time $userTimezone)</span>",
          '#cache' => [
            'contexts' => [
              'timezone',
              ],
            ],
          ];
      }
      
    }

    return $elements;
  }

  protected function setTimeZone(DrupalDateTime $date) {
    if ($this->getFieldSetting('datetime_type') === DateTimeItem::DATETIME_TYPE_DATE) {
      // A date without time has no timezone conversion.
      $timezone = DATETIME_STORAGE_TIMEZONE;
    }
    else {
      $timezone = $this->getVenueTimezone();
    }
    $date->setTimeZone(timezone_open($timezone));
  }

  /**
   * {@inheritdoc}
   */
  protected function formatDate($date) {
    $format_type = $this->getSetting('format_type');
    if ($this->getSetting('timezone_override') === 'venue') {
      $timezone = $this->getVenueTimezone();
    }
    else {
      $timezone = $this->getSetting('timezone_override') ?: $date->getTimezone()->getName();
    }
    return $this->dateFormatter->format($date->getTimestamp(), $format_type, '', $timezone != '' ? $timezone : NULL);
  }

  protected function getVenueTimezone() {
    if ($this->entity) {
      if ($this->entity->bundle() !== 'session' && $this->entity->bundle() !== 'event') {
        throw new \Exception("Venue time zone formatter only works on fields on event or session content.");
      }
      // The timezone property has been added to events and sessions using hook_node_load()
      return ahs_events_get_event_timezone($this->entity);
    }
    else {
      // The current entity is not set, probably indicating we're on the
      // formatter settings page, so get the default time zone.
      return ahs_events_get_venue_timezone(NULL);
    }
  }


  /**
   * Gets a settings array suitable for DrupalDateTime::format().
   *
   * @return array
   *   The settings array that can be passed to DrupalDateTime::format().
   */
  protected function getFormatSettings() {
    $settings = [];

    if ($this->getSetting('timezone_override') === 'venue') {
      $settings['timezone'] = $this->getVenueTimezone();
    }
    elseif ($this->getSetting('timezone_override') != '') {
      $settings['timezone'] = $this->getSetting('timezone_override');
    }

    return $settings;
  }

}
