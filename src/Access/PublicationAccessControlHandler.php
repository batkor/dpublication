<?php

namespace Drupal\dpublication\Access;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\dpublication\Entity\PublicationInterface;

/**
 * Defines the access control handler for "publication" entity.
 */
class PublicationAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    assert($entity instanceof PublicationInterface);

    if ($account->hasPermission($this->entityType->getAdminPermission())) {
      return AccessResult::allowed()->cachePerPermissions();
    }

    switch ($operation) {
      case 'view':
        return AccessResult::allowedIf($account->hasPermission('access content') && $entity->isPublished())
          ->cachePerPermissions()
          ->addCacheableDependency($entity);

      case 'update':
        if ($entity->getOwnerId() == $account->id()) {
          return AccessResult::allowedIfHasPermission($account, "edit own {$entity->bundle()} publication");
        }

        return AccessResult::allowedIfHasPermission($account, "edit any {$entity->bundle()} publication");

      case 'delete':
        if ($entity->getOwnerId() == $account->id()) {
          return AccessResult::allowedIfHasPermission($account, "delete own {$entity->bundle()} publication");
        }

        return AccessResult::allowedIfHasPermission($account, "delete any {$entity->bundle()} publication");

      default:
        return AccessResult::neutral()->cachePerPermissions();
    }
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    $permissions = [
      "create $entity_bundle publication",
      $this->entityType->getAdminPermission(),
    ];

    return AccessResult::allowedIfHasPermissions($account, $permissions, 'OR');
  }

}
