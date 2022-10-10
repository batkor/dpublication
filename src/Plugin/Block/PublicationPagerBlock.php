<?php

namespace Drupal\dpublication\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Routing\CurrentRouteMatch;
use Drupal\Core\Session\AccountInterface;
use Drupal\dpublication\Entity\PublicationPageInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a publication pager block.
 *
 * @Block(
 *   id = "dpublication_pager",
 *   admin_label = @Translation("Publication pager"),
 *   category = @Translation("Publications")
 * )
 */
class PublicationPagerBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The current route match.
   */
  protected CurrentRouteMatch $routeMatch;

  /**
   * The entity type manager.
   */
  protected EntityTypeManagerInterface $entityTypeManager;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $static = new static($configuration, $plugin_id, $plugin_definition);
    $static->routeMatch = $container->get('current_route_match');
    $static->entityTypeManager = $container->get('entity_type.manager');

    return $static;
  }

  /**
   * {@inheritdoc}
   */
  public function build() {

    $publicationPage = $this->routeMatch->getParameter('publication_page');

    if (!$publicationPage instanceof PublicationPageInterface) {
      return [];
    }

    $pageIds = $publicationPage->getPublicationEntity()->getPageIds();
    $current = array_search($publicationPage->id(), $pageIds);
    $list = [
      'previous' => $pageIds[$current - 1] ?? NULL,
      'next' => $pageIds[$current + 1] ?? NULL,
    ];
    $storage = $this->entityTypeManager->getStorage('publication_page');

    foreach ($list as $key => $id) {
      if ($id && $entity = $storage->load($id)) {
        $list[$key] = $entity->toUrl();
      }
    }

    $build = [
      '#theme' => 'publication_pager',
      '#previous' => $list['previous'] ?? NULL,
      '#next' => $list['next'] ?? NULL,
    ];

    return ['content' => $build];
  }

}
