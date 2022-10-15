<?php

namespace Drupal\dpublication\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityPublishedTrait;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\user\EntityOwnerTrait;

/**
 * Defines the Publication page entity class.
 *
 * @ContentEntityType(
 *   id = "publication_page",
 *   label = @Translation("Publication page"),
 *   admin_permission = "administer publications",
 *   handlers = {
 *     "access" = "Drupal\dpublication\Access\PublicationPageAccessControlHandler",
 *     "list_builder" = "Drupal\dpublication\ListBuilder\PublicationPageListBuilder",
 *     "form" = {
 *       "default" = "Drupal\dpublication\Form\PublicationPage\PublicationPageForm",
 *       "delete" = "Drupal\dpublication\Form\PublicationPage\PublicationPageDeleteForm",
 *       "edit" = "Drupal\dpublication\Form\PublicationPage\PublicationPageForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\dpublication\Routing\PublicationPageRouteProvider",
 *     },
 *   },
 *   base_table = "publication_page",
 *   data_table = "publication_page_field_data",
 *   bundle_entity_type = "publication_page_type",
 *   translatable = TRUE,
 *   entity_keys = {
 *     "id" = "id",
 *     "uuid" = "uuid",
 *     "label" = "title",
 *     "bundle" = "type",
 *     "published" = "status",
 *     "owner" = "uid",
 *     "langcode" = "langcode",
 *   },
 *   enable_page_title_template = TRUE,
 *   field_ui_base_route = "entity.publication_page_type.edit_form",
 *   links = {
 *     "collection" = "/publication/{publication}/pages",
 *     "canonical" = "/publication/{publication}/page/{publication_page}",
 *     "delete-form" = "/publication/{publication}/page/{publication_page}/delete",
 *     "edit-form" = "/publication/{publication}/page/{publication_page}/edit",
 *     "add-form" = "/publication/{publication}/page/add",
 *   }
 * )
 */
class PublicationPage extends ContentEntityBase implements PublicationPageInterface {

  use EntityPublishedTrait;
  use EntityOwnerTrait;

  /**
   * {@inheritdoc}
   */
  protected function urlRouteParameters($rel) {
    $params = parent::urlRouteParameters($rel);
    $params['publication'] = $this->getPublicationEntity()->id();

    return $params;
  }

  /**
   * {@inheritdoc}
   */
  public function getPublicationEntity(): ?Publication {
    if ($this->get('publication')->isEmpty()) {
      return NULL;
    }

    return $this->get('publication')->first()->get('entity')->getValue();
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);
    $fields += static::publishedBaseFieldDefinitions($entity_type);
    $fields += static::ownerBaseFieldDefinitions($entity_type);

    $fields['title'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Title'))
      ->setRequired(FALSE)
      ->setTranslatable(TRUE)
      ->setSetting('max_length', 500)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'string',
        'weight' => -5,
      ])
      ->setDisplayOptions('form', [
        'type' => 'publication_page_title',
        'weight' => -5,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['publication'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Publication'))
      ->setDescription(t('The parent publication.'))
      ->setRequired(TRUE)
      ->setTranslatable(FALSE)
      ->setSetting('target_type', 'publication')
      ->setReadOnly(TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['status']
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'settings' => [
          'display_label' => TRUE,
        ],
        'weight' => 100,
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['uid']
      ->setLabel(t('Added by user'));

    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function postSave(EntityStorageInterface $storage, $update = TRUE) {
    parent::postSave($storage, $update);

    // Ensure there's a reference on the parent publication before save.
    $publication = $this->getPublicationEntity();

    if ($publication && !$publication->hasPage($this->id())) {
      $publication->addPage($this);
      $publication->save();
    }
  }

}
