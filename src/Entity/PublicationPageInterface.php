<?php

namespace Drupal\dpublication\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Defines a common interface for publication page entities.
 */
interface PublicationPageInterface extends ContentEntityInterface, EntityPublishedInterface, EntityOwnerInterface {

  /**
   * Returns parent publication entity.
   */
  public function getPublicationEntity(): ?Publication;

}
