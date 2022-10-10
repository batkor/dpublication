<?php

namespace Drupal\dpublication\ContextProvider;

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Config\Entity\ConfigEntityBundleBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\Context\Context;
use Drupal\Core\Plugin\Context\ContextProviderInterface;
use Drupal\Core\Plugin\Context\EntityContext;
use Drupal\Core\Plugin\Context\EntityContextDefinition;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Sets the current publication, publication_page as a context on routes.
 */
class RouteContextProvider implements ContextProviderInterface {

  /**
   * The route match object.
   */
  protected RouteMatchInterface $routeMatch;

  /**
   * The entity type manager.
   */
  protected EntityTypeManagerInterface $entityTypeManager;

  /**
   * Constructs a new NodeRouteContext.
   *
   * @param \Drupal\Core\Routing\RouteMatchInterface $routeMatch
   *   The route match object.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   */
  public function __construct(RouteMatchInterface $routeMatch, EntityTypeManagerInterface $entityTypeManager) {
    $this->routeMatch = $routeMatch;
    $this->entityTypeManager = $entityTypeManager;
  }

  /**
   * {@inheritdoc}
   */
  public function getRuntimeContexts(array $unqualifiedContextIds) {
    $result = [];

    foreach (array_keys($this->supportedEntityTypes()) as $entityTypeId) {
      $value = NULL;
      $contextDefinition = EntityContextDefinition::create($entityTypeId)
        ->setRequired(FALSE);

      if ($entity = $this->routeMatch->getParameter($entityTypeId)) {
        $value = $entity;
      }
      else {
        $entityDefinition = $this
          ->entityTypeManager
          ->getDefinition($entityTypeId);
        $entityBundleTypeId = $entityDefinition->getBundleEntityType();

        if ($entityBundleType = $this->routeMatch->getParameter($entityBundleTypeId)) {
          $storage = $this
            ->entityTypeManager
            ->getStorage($entityTypeId);
          $bundleId = $entityBundleType instanceof ConfigEntityBundleBase ? $entityBundleType->id() : $entityBundleType;
          $value = $storage->create([
            $entityDefinition->getKey('bundle') => $bundleId,
          ]);
        }
      }

      $cacheability = new CacheableMetadata();
      $cacheability->setCacheContexts(['route']);
      $context = new Context($contextDefinition, $value);
      $context->addCacheableDependency($cacheability);
      $result[$entityTypeId] = $context;
    }

    return $result;
  }

  /**
   * {@inheritdoc}
   */
  public function getAvailableContexts() {
    $contexts = [];

    foreach ($this->supportedEntityTypes() as $entityTypeId => $label) {
      $contexts[$entityTypeId] = EntityContext::fromEntityTypeId($entityTypeId, $label);
    }

    return $contexts;
  }

  /**
   * Returns entity types used in current context provider.
   *
   * @return \Drupal\Core\StringTranslation\TranslatableMarkup[]
   *   The entity types list.
   */
  protected function supportedEntityTypes(): array {
    return [
      'publication' => new TranslatableMarkup('Publication entity from URL'),
      'publication_page' => new TranslatableMarkup('Publication page entity from URL'),
    ];
  }

}
