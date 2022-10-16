<?php

namespace Drupal\dpublication\Entity;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Defines a common interface for publication type entities.
 */
interface PublicationPageTypeInterface extends ConfigEntityInterface {

  /**
   * Returns entity description.
   */
  public function getDescription(): string;

}
