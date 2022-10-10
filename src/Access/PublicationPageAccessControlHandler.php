<?php

namespace Drupal\dpublication\Access;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Language\LanguageInterface;
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
    $static = new static($container->get('entity_type.manager')->getDefinition('publication_page'));

    return $static;
  }

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    assert($entity instanceof PublicationPageInterface);

    return parent::checkAccess($entity->getPublicationEntity(), $operation, $account);
  }

  /**
   * Check permissions for create "publication_page" entity.
   *
   * @param \Drupal\Core\Routing\RouteMatchInterface $routeMatch
   *   The current route match.
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The current proxy user account.
   *
   * @return bool|\Drupal\Core\Access\AccessResultInterface
   *   The access result.
   *
   * @see \Drupal\dpublication\Routing\PublicationPageRouteProvider::getAddFormRoute
   */
  public function createAccessHandler(RouteMatchInterface $routeMatch, AccountInterface $account) {
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
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    $entity_bundle = $context['parent_entity_bundle'] ?? $entity_bundle;

    return parent::checkCreateAccess($account, $context, $entity_bundle);
  }

}
