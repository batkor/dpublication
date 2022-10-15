<?php

declare(strict_types=1);

namespace Drupal\dpublication\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * The base form for publication entity types.
 */
class DpublicationBaseForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  protected function actions(array $form, FormStateInterface $form_state) {
    $actions = parent::actions($form, $form_state);
    $actions['#weight'] = 200;

    if ($this->getEntity()->isNew()) {
      $actions['submit']['#value'] = $this->t('Create');
    }

    return $actions;
  }

}
