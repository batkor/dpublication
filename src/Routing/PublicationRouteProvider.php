<?php

namespace Drupal\dpublication\Routing;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\Routing\AdminHtmlRouteProvider;
use Drupal\dpublication\TitleProvider;

/**
 * Provides routes for the publication entity.
 */
class PublicationRouteProvider extends AdminHtmlRouteProvider {

  /**
   * {@inheritdoc}
   */
  protected function getAddFormRoute(EntityTypeInterface $entity_type) {
    $route = parent::getAddFormRoute($entity_type);
    $route->setDefault('_title_callback', TitleProvider::class . '::publicationAddForm');

    return $route;
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditFormRoute(EntityTypeInterface $entity_type) {
    $route = parent::getEditFormRoute($entity_type);
    $route->setDefault('_entity_form', 'publication.edit');

    return $route;
  }

}
