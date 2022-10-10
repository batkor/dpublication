<?php

namespace Drupal\dpublication;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * The class provides permission for "dpublication" module.
 */
class PermissionsProvider implements ContainerInjectionInterface {

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
    $permissions = [];
    $publicationtypes = $this
      ->entityTypeManager
      ->getStorage('publication_type')
      ->loadMultiple();

    foreach ($publicationtypes as $publicationType) {
      $params = [
        '%name' => $publicationType->label(),
      ];
      // @todo Test every permission.
      $permissions = [
        "create {$publicationType->id()} publication" => [
          'title' => $this->t('%name: Create new publication', $params),
        ],
        "edit own {$publicationType->id()} publication" => [
          'title' => $this->t('%name: Edit own publication', $params),
        ],
        "edit any {$publicationType->id()} publication" => [
          'title' => $this->t('%name: Edit any publication', $params),
        ],
        "delete own {$publicationType->id()} publication" => [
          'title' => $this->t('%name: Delete own publication', $params),
        ],
        "delete any {$publicationType->id()} publication" => [
          'title' => $this->t('%name: Delete any publication', $params),
        ],
      ];
    }

    return $permissions;
  }

}
