<?php

namespace Drupal\dpublication\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldFormatter\EntityReferenceFormatterBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

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
class PagesFormatter extends EntityReferenceFormatterBase implements ContainerFactoryPluginInterface {

  /**
   * The current user proxy.
   */
  protected AccountProxyInterface $accountProxy;

  /**
   * Constructors.
   *
   * @param string $plugin_id
   *   The plugin_id for the formatter.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Field\FieldDefinitionInterface $field_definition
   *   The definition of the field to which the formatter is associated.
   * @param array $settings
   *   The formatter settings.
   * @param string $label
   *   The formatter label display setting.
   * @param string $view_mode
   *   The view mode.
   * @param array $third_party_settings
   *   Any third party settings.
   * @param \Drupal\Core\Session\AccountProxyInterface $accountProxy
   *   The current user proxy.
   */
  public function __construct(
    $plugin_id,
    $plugin_definition,
    FieldDefinitionInterface $field_definition,
    array $settings,
    $label,
    $view_mode,
    array $third_party_settings,
    AccountProxyInterface $accountProxy
  ) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $label, $view_mode, $third_party_settings);
    $this->accountProxy = $accountProxy;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['label'],
      $configuration['view_mode'],
      $configuration['third_party_settings'],
      $container->get('current_user')
    );
  }

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

    /** @var \Drupal\dpublication\Entity\PublicationInterface $publication */
    $publication = $items->getEntity();
    if ($this->accountProxy->hasPermission("edit own {$publication->id()} publication")) {
      $element['#actions'][] = Link::createFromRoute($this->t('Add page'), 'entity.publication_page.add_form', [
        'publication' => $publication->id(),
      ])->toRenderable();
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
