<?php

namespace Drupal\dpublication\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;

/**
 * Defines the Publication page type configuration entity.
 *
 * @ConfigEntityType(
 *   id = "publication_page_type",
 *   label = @Translation("Publication page type"),
 *   label_collection = @Translation("Publication page types"),
 *   label_singular = @Translation("publication page type"),
 *   label_plural = @Translation("publication page types"),
 *   label_count = @PluralTranslation(
 *     singular = "@count publication page type",
 *     plural = "@count publication page types",
 *   ),
 *   handlers = {
 *     "form" = {
 *       "default" = "Drupal\dpublication\Form\PublicationPage\PublicationPageTypeForm",
 *       "edit" = "Drupal\dpublication\Form\PublicationPage\PublicationPageTypeForm",
 *       "delete" = "Drupal\Core\Entity\EntityDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider",
 *     },
 *     "list_builder" = "Drupal\dpublication\ListBuilder\PublicationPageTypeListBuilder",
 *   },
 *   admin_permission = "administer publications",
 *   config_prefix = "publication_page_type",
 *   bundle_of = "publication_page",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label"
 *   },
 *   links = {
 *     "add-form" = "/admin/structure/publications/publication_page_type/add",
 *     "edit-form" = "/admin/structure/publications/publication_page_type/{publication_page_type}",
 *     "delete-form" = "/admin/structure/publications/publication_page_type/{publication_page_type}/delete",
 *     "collection" = "/admin/structure/publications/publication_page_types",
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "description",
 *   }
 * )
 */
class PublicationPageType extends ConfigEntityBundleBase {

  /**
   * The machine name of this publication page type.
   */
  protected string $id;

  /**
   * The human-readable name of the publication page type.
   */
  protected string $label;

  /**
   * A brief description of this publication page type.
   */
  protected ?string $description;

  public function getDescription(): ?string {
    return $this->description ?? '';
  }

}
