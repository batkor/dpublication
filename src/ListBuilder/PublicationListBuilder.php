<?php

declare(strict_types=1);

namespace Drupal\dpublication\ListBuilder;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Url;

/**
 * Implements publication list controller.
 */
class PublicationListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['title'] = $this->t('Title');
    $header['type'] = $this->t('Publication type');

    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    $row['title'] = ['data' => $entity->toLink()->toRenderable()];
    $row['type'] = $entity->bundle();

    return $row + parent::buildRow($entity);
  }

  /**
   * {@inheritdoc}
   */
  public function getDefaultOperations(EntityInterface $entity) {
    $operations = parent::getDefaultOperations($entity);
    $operations['pages_collection'] = [
      'title' => $this->t('Pages'),
      'weight' => 10,
      'url' => Url::fromRoute('entity.publication_page.collection', [
        'publication' => $entity->id(),
      ]),
    ];

    return $operations;
  }

}
