<?php

declare(strict_types=1);

namespace Drupal\dpublication\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityPublishedTrait;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\user\EntityOwnerTrait;

/**
 * Defines the Publication entity class.
 *
 * @ContentEntityType(
 *   id = "publication",
 *   label = @Translation("Publication"),
 *   label_collection = @Translation("Publications"),
 *   label_singular = @Translation("publication"),
 *   label_plural = @Translation("publications"),
 *   admin_permission = "administer publications",
 *   handlers = {
 *     "access" = "Drupal\dpublication\Access\PublicationAccessControlHandler",
 *     "list_builder" = "Drupal\dpublication\ListBuilder\PublicationListBuilder",
 *     "form" = {
 *       "default" = "Drupal\dpublication\Form\Publication\PublicationForm",
 *       "delete" = "Drupal\dpublication\Form\Publication\PublicationDeleteForm",
 *       "edit" = "Drupal\dpublication\Form\Publication\PublicationForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\dpublication\Routing\PublicationRouteProvider",
 *     },
 *   },
 *   base_table = "publication",
 *   data_table = "publication_field_data",
 *   bundle_entity_type = "publication_type",
 *   translatable = TRUE,
 *   entity_keys = {
 *     "id" = "bid",
 *     "uuid" = "uuid",
 *     "label" = "title",
 *     "bundle" = "type",
 *     "published" = "status",
 *     "owner" = "uid",
 *     "langcode" = "langcode",
 *   },
 *   enable_page_title_template = TRUE,
 *   field_ui_base_route = "entity.publication_type.edit_form",
 *   links = {
 *     "collection" = "/admin/publications",
 *     "canonical" = "/publication/{publication}",
 *     "delete-form" = "/publication/{publication}/delete",
 *     "edit-form" = "/publication/{publication}/edit",
 *     "add-form" = "/publication/create/{publication_type}",
 *     "add-page" = "/publication/create",
 *   }
 * )
 */
class Publication extends ContentEntityBase implements PublicationInterface {

  use EntityOwnerTrait;
  use EntityPublishedTrait;

  /**
   * {@inheritdoc}
   */
  public function getBundleEntity(): PublicationTypeInterface {
    return $this->get('type')->first()->get('entity')->getValue();
  }

  /**
   * {@inheritdoc}
   */
  public function getPages(): array {
    return $this->get('pages')->referencedEntities();
  }

  /**
   * {@inheritdoc}
   */
  public function getPageIds(): array {
    $pageIds = [];

    foreach ($this->get('pages') as $fieldItem) {
      $pageIds[] = $fieldItem->get('target_id')->getValue();
    }

    return $pageIds;
  }

  /**
   * {@inheritdoc}
   */
  public function addPage(PublicationPageInterface $page): PublicationInterface {
    if (!$this->hasPage($page->id())) {
      $this->get('pages')->appendItem($page);
    }

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function hasPage(string $pageId): bool {
    return in_array($pageId, $this->getPageIds());
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
      ->setRequired(TRUE)
      ->setTranslatable(TRUE)
      ->setSetting('max_length', 255)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'string',
        'weight' => -5,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -5,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['pages'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Pages'))
      ->setDescription(t('The book pages.'))
      ->setTranslatable(FALSE)
      ->setCardinality(BaseFieldDefinition::CARDINALITY_UNLIMITED)
      ->setSetting('target_type', 'publication_page')
      ->setSetting('handler', 'default')
      ->setDisplayOptions('view', [
        'type' => 'entity_reference_label',
      ])
      ->setDisplayConfigurable('form', TRUE)
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
  public static function postDelete(EntityStorageInterface $storage, array $entities) {
    parent::postDelete($storage, $entities);
    // Delete the pages of a deleted product.
    $pages = [];

    /** @var \Drupal\dpublication\Entity\Publication $entity */
    foreach ($entities as $entity) {
      $pages = array_merge($pages, $entity->getPages());
    }

    $pageStorage = \Drupal::service('entity_type.manager')
      ->getStorage('publication_page');
    $pageStorage->delete($pages);

  }

}
