<?php

namespace Drupal\dpublication\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldFormatter\EntityReferenceFormatterBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'Pages' formatter.
 *
 * @FieldFormatter(
 *   id = "dpublication_pages",
 *   label = @Translation("Pages"),
 *   field_types = {
 *     "entity_reference"
 *   }
 * )
 */
class PagesFormatter extends EntityReferenceFormatterBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'override_field_label' => TRUE,
      'label' => 'Table of Contents',
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $formState) {
    $form = parent::settingsForm($form, $formState);

    $form['override_field_label'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('New field label'),
      '#default_value' => $this->getSetting('override_field_label'),
    ];

    $fieldName = $this->fieldDefinition->getName();
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('New field label'),
      '#default_value' => $this->getSetting('label'),
      '#states' => [
        'visible' => [
          ':input[name="fields[' . $fieldName . '][settings_edit_form][settings][override_field_label]"]' => ['checked' => TRUE],
        ],
      ],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];
    $isOverride = $this->getSetting('override_field_label');
    $summary[] = $this->t('Override field label: @status', [
      '@status' => $isOverride ? $this->t('Yes') : $this->t('No'),
    ]);

    if ($isOverride) {
      $summary[] = $this->t('New title: @title', ['@title' => $this->getSetting('label')]);
    }

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function view(FieldItemListInterface $items, $langcode = NULL) {
    $elements = parent::view($items, $langcode);

    if ($this->getSetting('override_field_label')) {
      $elements['#title'] = $this->getSetting('label');
    }

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $element = [
      '#theme' => 'publication_pages',
    ];

    /** @var \Drupal\dpublication\Entity\PublicationPageInterface $entity */
    foreach ($this->getEntitiesToView($items, $langcode) as $delta => $entity) {
      if (!empty($entity->label())) {
        $element['#pages'][$delta] = [
          'title' => $entity->toLink(),
          'entity' => $entity,
        ];
      }
    }

    return [$element];
  }

  /**
   * {@inheritdoc}
   */
  public static function isApplicable(FieldDefinitionInterface $fieldDefinition) {
    return $fieldDefinition->getTargetEntityTypeId() === 'publication';
  }

}
