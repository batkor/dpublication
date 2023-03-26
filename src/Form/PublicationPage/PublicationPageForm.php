<?php

declare(strict_types=1);

namespace Drupal\dpublication\Form\PublicationPage;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\dpublication\Entity\PublicationPage;
use Drupal\dpublication\Form\DpublicationBaseForm;

/**
 * Implements default form for publication page entity.
 */
class PublicationPageForm extends DpublicationBaseForm {

  /**
   * {@inheritdoc}
   */
  public function getEntityFromRouteMatch(RouteMatchInterface $route_match, $entity_type_id) {
    $entity = $route_match->getParameter('publication_page');

    if ($entity instanceof PublicationPage) {
      return $entity;
    }

    /** @var \Drupal\dpublication\Entity\Publication $publication */
    $publication = $route_match->getParameter('publication');
    $values = [
      'type' => $publication->getBundleEntity()->getPageType(),
      'publication' => $publication,
    ];

    return $this
      ->entityTypeManager
      ->getStorage('publication_page')
      ->create($values);
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $publication = $this->getEntity()->getPublicationEntity();
    if ($this->getEntity()->isNew()) {
      $form_state->setRedirect('entity.publication_page.collection', [
        'publication' => $publication->id(),
      ]);
    }
    else {
      $form_state->setRedirect('entity.publication_page.canonical', [
        'publication' => $publication->id(),
        'publication_page' => $this->getEntity()->id(),
      ]);
    }

    return $this->getEntity()->save();
  }

}
