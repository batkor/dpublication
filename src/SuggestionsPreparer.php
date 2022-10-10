<?php

namespace Drupal\dpublication;

use Drupal\Core\Routing\CurrentRouteMatch;

/**
 * The service for generate theme suggestions list.
 */
class SuggestionsPreparer {

  /**
   * The current route match.
   */
  protected CurrentRouteMatch $routeMatch;

  /**
   * Constructor.
   *
   * @param \Drupal\Core\Routing\CurrentRouteMatch $routeMatch
   *   The current route match.
   */
  public function __construct(CurrentRouteMatch $routeMatch) {
    $this->routeMatch = $routeMatch;
  }

  /**
   * Generate theme suggestions for entity templates.
   *
   * @param string $entityTypeId
   *   The entity type id.
   *
   * @return array
   *   The suggestions list.
   */
  public function entityTemplate(string $entityTypeId): array {
    $suggestions = [];

    /** @var \Drupal\dpublication\Entity\PublicationInterface $entity */
    if ($entity = $this->routeMatch->getParameter($entityTypeId)) {
      $bundleSuggestion = "{$entityTypeId}__{$entity->bundle()}";

      $suggestions[] = $bundleSuggestion;
      $suggestions[] = "{$bundleSuggestion}__{$entity->id()}";
    }

    return $suggestions;
  }

  /**
   * Generate theme suggestions list by theme name.
   *
   * @param string $baseHook
   *   The base hook name.
   *
   * @return array
   *   The suggestions list.
   */
  public function theme(string $baseHook): array {
    $suggestions = [];

    $allowRoutes = [
      'entity.publication.canonical',
      'entity.publication_page.canonical',
      'entity.publication_page.edit_form',
    ];

    if (!in_array($this->routeMatch->getRouteName(), $allowRoutes)) {
      return $suggestions;
    }

    $entityTypeIds = [
      'publication',
      'publication_page',
    ];

    foreach ($entityTypeIds as $entityTypeId) {
      $suggestions[] = "{$baseHook}__{$entityTypeId}";

      /** @var \Drupal\dpublication\Entity\PublicationInterface $entity */
      if ($entity = $this->routeMatch->getParameter($entityTypeId)) {
        $bundleSuggestion = "{$baseHook}__{$entityTypeId}__{$entity->bundle()}";

        $suggestions[] = $bundleSuggestion;
        $suggestions[] = "{$bundleSuggestion}__{$entity->id()}";
      }
    }

    return $suggestions;
  }

}
