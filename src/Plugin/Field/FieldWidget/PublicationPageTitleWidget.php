<?php

namespace Drupal\dpublication\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Defines the 'publication_page_title' field widget.
 *
 * @FieldWidget(
 *   id = "publication_page_title",
 *   label = @Translation("Publication page title"),
 *   field_types = {"string"},
 * )
 */
class PublicationPageTitleWidget extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'always_show' => FALSE,
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {

    $element['always_show'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Always show'),
      '#default_value' => $this->getSetting('always_show'),
    ];

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary[] = $this->t('Always show: @status', [
      '@status' => $this->getSetting('always_show') ? $this->t('Yes') : $this->t('No'),
    ]);

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element['set_title'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Set title'),
      '#default_value' => FALSE,
      '#access' => !$this->getSetting('always_show'),
    ];

    $element['value'] = $element + [
      '#type' => 'textfield',
      '#default_value' => $items[$delta]->value ?? '',
    ];

    if (!$this->getSetting('always_show')) {
      $selector = ':input[name="title[' . $delta . '][set_title]"]';
      $element['value']['#states'] = [
        'visible' => [
          $selector => ['checked' => TRUE],
        ],
        'required' => [
          $selector => ['checked' => TRUE],
        ],
      ];
    }

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public static function isApplicable(FieldDefinitionInterface $field_definition) {
    return $field_definition->getTargetEntityTypeId() === 'publication_page' && $field_definition->getName() === 'title';
  }

}
