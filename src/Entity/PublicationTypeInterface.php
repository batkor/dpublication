<?php

namespace Drupal\dpublication\Entity;

use Drupal\Core\Entity\EntityInterface;

/**
 * Defines a common interface for publication type entities.
 */
interface PublicationTypeInterface extends EntityInterface {

  /**
   * Returns entity description.
   */
  public function getDescription(): string;

  /**
   * Returns page type property.
   */
  public function getPageType(): string;

}
