<?php

namespace Drupal\dpublication\Access;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Access\AccessResultInterface;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\dpublication\Entity\PublicationInterface;
use Drupal\dpublication\Entity\PublicationPageInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines the access control handler for "publication_page" entity.
 */
class PublicationPageAccessControlHandler extends PublicationAccessControlHandler implements ContainerInjectionInterface {

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static($container->get('entity_type.manager')->getDefinition('publication_page'));
  }

  /**
   * Check permissions for create "publication_page" entity.
   *
   * @param \Drupal\Core\Routing\RouteMatchInterface $routeMatch
   *   The current route match.
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The current proxy user account.
   *
   * @return \Drupal\Core\Access\AccessResultInterface
   *   The access result.
   *
   * @see \Drupal\dpublication\Routing\PublicationPageRouteProvider::getAddFormRoute
   */
  public function createAccessHandler(RouteMatchInterface $routeMatch, AccountInterface $account): AccessResultInterface {
    $publication = $routeMatch->getParameter('publication');

    if (!$publication instanceof PublicationInterface) {
      return AccessResult::forbidden();
    }

    $pageType = $publication->getBundleEntity()->getPageType();

    $context = [
      'parent_entity_bundle' => $publication->bundle(),
    ];

    return $this->createAccess($pageType, $account, $context, TRUE);
  }

  /**
   * Check permissions for "entity.publication_page.collection" route.
   *
   * @param \Drupal\Core\Routing\RouteMatchInterface $routeMatch
   *   The current route match.
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The current proxy user account.
   *
   * @return \Drupal\Core\Access\AccessResultInterface
   *   The access result.
   *
   * @see \Drupal\dpublication\Routing\PublicationPageRouteProvider::getCollectionRoute
   */
  public function collectionAccessHandler(RouteMatchInterface $routeMatch, AccountInterface $account): AccessResultInterface {
    $publication = $routeMatch->getParameter('publication');

    if (!$publication instanceof PublicationInterface) {
      return AccessResult::forbidden();
    }

    return parent::checkAccess($publication, 'update', $account);
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    $entity_bundle = $context['parent_entity_bundle'] ?? $entity_bundle;

    return parent::checkCreateAccess($account, $context, $entity_bundle);
  }

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    assert($entity instanceof PublicationPageInterface);

    return parent::checkAccess($entity->getPublicationEntity(), $operation, $account);
  }

}
