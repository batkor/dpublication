<?php

namespace Drupal\dpublication\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Defines a common interface for publication entities.
 */
interface PublicationInterface extends ContentEntityInterface, EntityPublishedInterface, EntityOwnerInterface {

  /**
   * Returns bundle entity.
   */
  public function getBundleEntity(): PublicationType;

  /**
   * Returns publication pages entity list.
   *
   * @return \Drupal\dpublication\Entity\PublicationPageInterface[]
   *   The entities list.
   */
  public function getPages(): array;

  /**
   * Returns publication page entity IDs.
   */
  public function getPageIds(): array;

  /**
   * Add publication page entity to current instance.
   *
   * @param \Drupal\dpublication\Entity\PublicationPageInterface $page
   *   The publication page instance.
   */
  public function addPage(PublicationPageInterface $page): PublicationInterface;

  /**
   * Check if a publication page ID in publication pages list.
   *
   * @param string $pageId
   *   The publication page ID.
   */
  public function hasPage(string $pageId): bool;

}
