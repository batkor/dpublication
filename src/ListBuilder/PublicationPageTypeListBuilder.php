<?php

namespace Drupal\dpublication\ListBuilder;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;

class PublicationPageTypeListBuilder extends PublicationTypeListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['title'] = $this->t('Publication page type');

    return $header + parent::buildHeader();
  }

}
