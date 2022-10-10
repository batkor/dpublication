<?php

declare(strict_types=1);

namespace Drupal\dpublication\Routing;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\Routing\AdminHtmlRouteProvider;
use Drupal\dpublication\ListBuilder\PublicationPageListBuilder;
use Drupal\dpublication\TitleProvider;
use Symfony\Component\Routing\Route;

/**
 * Provides routes for the publication page entity.
 */
class PublicationPageRouteProvider extends AdminHtmlRouteProvider {

  /**
   * {@inheritdoc}
   */
  protected function getCanonicalRoute(EntityTypeInterface $entity_type) {
    $route = parent::getCanonicalRoute($entity_type);
    $route->setOption('parameters', [
      'publication' => [
        'type' => 'entity:publication',
      ],
      'publication_page' => [
        'type' => 'entity:publication_page',
      ],
    ]);

    return $route;
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditFormRoute(EntityTypeInterface $entity_type) {
    $route = parent::getEditFormRoute($entity_type);
    $route->setDefault('_entity_form', 'publication_page.edit');
    $route->setDefault('_title_callback', TitleProvider::class . '::publicationPageEditForm');
    $route->setOption('parameters', [
      'publication' => [
        'type' => 'entity:publication',
      ],
      'publication_page' => [
        'type' => 'entity:publication_page',
      ],
    ]);

    return $route;
  }

  /**
   * {@inheritdoc}
   */
  protected function getDeleteFormRoute(EntityTypeInterface $entity_type) {
    $route = parent::getDeleteFormRoute($entity_type);
    $route->setOption('parameters', [
      'publication' => [
        'type' => 'entity:publication',
      ],
      'publication_page' => [
        'type' => 'entity:publication_page',
      ],
    ]);

    return $route;
  }

  /**
   * {@inheritdoc}
   */
  protected function getAddFormRoute(EntityTypeInterface $entityType) {
    $route = parent::getAddFormRoute($entityType);
    $route
      ->setDefaults([
        '_entity_form' => 'publication_page.default',
        'entity_type_id' => 'publication_page',
      ])
      ->setRequirement('_custom_access', 'Drupal\dpublication\Access\PublicationPageAccessControlHandler::createAccessHandler')
      ->setOption('parameters', [
        'publication' => [
          'type' => 'entity:publication',
        ],
      ])
      ->setOption('_admin_route', TRUE);

    return $route;
  }

  /**
   * {@inheritdoc}
   */
  protected function getCollectionRoute(EntityTypeInterface $entityType) {
    $route = parent::getCollectionRoute($entityType);
    $route
      ->setDefault('_title_callback', TitleProvider::class . '::publicationPageCollection')
      ->setOption('parameters', [
        'publication' => [
          'type' => 'entity:publication',
        ],
      ])
      ->setOption('_admin_route', TRUE);

    return $route;
  }

}
