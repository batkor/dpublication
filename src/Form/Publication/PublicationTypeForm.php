<?php
declare(strict_types=1);

namespace Drupal\dpublication\Form\Publication;

use Drupal\Core\Entity\BundleEntityFormBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;

/**
 * Implements default form for the publication type entity.
 */
final class PublicationTypeForm extends BundleEntityFormBase {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $form['label'] = [
      '#title' => $this->t('Label'),
      '#type' => 'textfield',
      '#default_value' => $this->getEntity()->label(),
      '#description' => $this->t('The human-readable name of this publication type'),
      '#required' => TRUE,
      '#size' => 30,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $this->getEntity()->id(),
      '#maxlength' => EntityTypeInterface::BUNDLE_MAX_LENGTH,
      '#disabled' => !$this->getEntity()->isNew(),
      '#machine_name' => [
        'exists' => ['Drupal\dpublication\Entity\PublicationType', 'load'],
        'source' => ['label'],
      ],
      '#description' => $this->t('A unique machine-readable name for this publication type.'),
    ];

    $form['description'] = [
      '#title' => $this->t('Description'),
      '#type' => 'textarea',
      '#default_value' => $this->getEntity()->getDescription(),
      '#description' => $this->t('Administrative description.'),
    ];

    $entityTypeLabel = $this
      ->entityTypeManager
      ->getDefinition('publication_page_type')
      ->getSingularLabel();
    $linkText = $this->t('Add a new @entity_type.', ['@entity_type' => $entityTypeLabel]);
    $addLink = Link::createFromRoute($linkText, 'entity.publication_page_type.add_form', [], [
      'query' => [
        'destination' => Url::createFromRequest($this->getRequest())->toString(),
      ],
    ]);

    $form['publicationPageType'] = [
      '#title' => $this->t('Publication page type'),
      '#type' => 'select',
      '#required' => TRUE,
      '#options' => $this->getPageTypesOptions(),
      '#default_value' => $this->getEntity()->getPageType(),
      '#description' => $this->t('Choose publication page type on this publication type. @add_link.', [
        '@add_link' => $addLink->toString(),
      ]),
    ];

    return $this->protectBundleIdElement($form);
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $form_state->setRedirect('entity.publication_type.collection');

    return parent::save($form, $form_state);
  }

  /**
   * Returns publication type list for form element.
   *
   * @return array
   *   The publication page types list.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  protected function getPageTypesOptions(): array {
    $options = [];
    $pageTypes = $this
      ->entityTypeManager
      ->getStorage('publication_page_type')
      ->loadMultiple();

    foreach ($pageTypes as $id => $pageType) {
      $options[$id] = $pageType->label();
    }

    return $options;
  }

}
