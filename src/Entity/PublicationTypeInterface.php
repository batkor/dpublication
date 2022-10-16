<?php

namespace Drupal\dpublication\Entity;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Defines a common interface for publication type entities.
 */
interface PublicationTypeInterface extends ConfigEntityInterface {

  /**
   * Returns entity description.
   */
  public function getDescription(): string;

  /**
   * Returns page type property.
   */
  public function getPageType(): string;

}
