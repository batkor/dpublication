<?php

namespace Drupal\dpublication\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;

/**
 * Defines the Publication type configuration entity.
 *
 * @ConfigEntityType(
 *   id = "publication_type",
 *   label = @Translation("Publication type"),
 *   label_collection = @Translation("Publication types"),
 *   label_singular = @Translation("publication type"),
 *   label_plural = @Translation("publication types"),
 *   label_count = @PluralTranslation(
 *     singular = "@count publication type",
 *     plural = "@count publication types",
 *   ),
 *   handlers = {
 *     "form" = {
 *       "default" = "Drupal\dpublication\Form\Publication\PublicationTypeForm",
 *       "edit" = "Drupal\dpublication\Form\Publication\PublicationTypeForm",
 *       "delete" = "Drupal\Core\Entity\EntityDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider",
 *       "permissions" = "Drupal\user\Entity\EntityPermissionsRouteProvider",
 *     },
 *     "list_builder" = "Drupal\dpublication\ListBuilder\PublicationTypeListBuilder",
 *   },
 *   admin_permission = "administer publication types",
 *   config_prefix = "publication_type",
 *   bundle_of = "publication",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label"
 *   },
 *   links = {
 *     "add-form" = "/admin/structure/publications/publication_types/add",
 *     "edit-form" = "/admin/structure/publications/publication_types/{publication_type}",
 *     "delete-form" = "/admin/structure/publications/publication_types/{publication_type}/delete",
 *     "collection" = "/admin/structure/publications/publication_types",
 *     "entity-permissions-form" = "/admin/structure/publications/publication_types/{publication_type}/permissions",
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "description",
 *     "publicationPageType",
 *   }
 * )
 */
class PublicationType extends ConfigEntityBundleBase implements PublicationTypeInterface {

  /**
   * The machine name of this publication type.
   */
  protected string $id;

  /**
   * The human-readable name of the publication type.
   */
  protected string $label;

  /**
   * A brief description of this publication type.
   */
  protected string $description = '';

  /**
   * The publication page type.
   */
  protected string $publicationPageType = '';

  /**
   * {@inheritdoc}
   */
  public function getDescription(): string {
    return $this->description ?? '';
  }

  /**
   * {@inheritdoc}
   */
  public function getPageType(): string {
    return $this->publicationPageType;
  }

}
