<?php

declare(strict_types=1);

namespace Drupal\dpublication\Form\Publication;

use Drupal\Core\Form\FormStateInterface;
use Drupal\dpublication\Form\DpublicationBaseForm;

/**
 * Implements default form for publication entity.
 */
class PublicationForm extends DpublicationBaseForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    if ($this->operation == 'edit') {
      $form['#title'] = $this->t('<em>Edit @type</em> @title', [
        '@type' => $this->getEntity()->getBundleEntity()->label(),
        '@title' => $this->getEntity()->label(),
      ]);
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  protected function actions(array $form, FormStateInterface $form_state) {
    $actions = parent::actions($form, $form_state);
    $actions['delete']['#title'] = $this->t('Remove publication');

    return $actions;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    if ($this->getEntity()->isNew()) {
      $form_state->setRedirect('entity.publication.collection');
    }
    else {
      $form_state->setRedirect('entity.publication.canonical', [
        'publication' => $this->getEntity()->id(),
      ]);
    }

    return $this->getEntity()->save();
  }

}
