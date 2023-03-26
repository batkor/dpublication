<?php

namespace Drupal\dpublication;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\BundlePermissionHandlerTrait;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\dpublication\Entity\PublicationTypeInterface;
use Drupal\node\Entity\NodeType;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * The class provides permission for "dpublication" module.
 */
class PermissionsProvider implements ContainerInjectionInterface {

  use BundlePermissionHandlerTrait;
  use StringTranslationTrait;

  /**
   * The entity type manager.
   */
  protected EntityTypeManagerInterface $entityTypeManager;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $static = new static();
    $static->entityTypeManager = $container->get('entity_type.manager');

    return $static;
  }

  /**
   * Returns permissions list for publication types.
   *
   * @return array
   *   The permissions list.
   */
  public function list(): array {
    $publicationTypes = $this
      ->entityTypeManager
      ->getStorage('publication_type')
      ->loadMultiple();

    return $this
      ->generatePermissions($publicationTypes, [$this, 'buildBundlePermissions']);
  }

  /**
   * Returns a list of publication permissions for a given publication type.
   *
   * @param \Drupal\dpublication\Entity\PublicationTypeInterface $type
   *   The publication type.
   *
   * @return array
   *   An associative array of permission names and descriptions.
   */
  protected function buildBundlePermissions(PublicationTypeInterface $type): array {
    $params = [
      '%name' => $type->label(),
    ];

    return [
      "create {$type->id()} publication" => [
        'title' => $this->t('%name: Create new publication', $params),
      ],
      "edit own {$type->id()} publication" => [
        'title' => $this->t('%name: Edit own publication', $params),
      ],
      "edit any {$type->id()} publication" => [
        'title' => $this->t('%name: Edit any publication', $params),
      ],
      "delete own {$type->id()} publication" => [
        'title' => $this->t('%name: Delete own publication', $params),
      ],
      "delete any {$type->id()} publication" => [
        'title' => $this->t('%name: Delete any publication', $params),
      ],
    ];
  }

}
