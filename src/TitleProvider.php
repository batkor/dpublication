<?php

namespace Drupal\dpublication;

use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\dpublication\Entity\PublicationInterface;
use Drupal\dpublication\Entity\PublicationPageInterface;
use Drupal\dpublication\Entity\PublicationType;

/**
 * The page title provider.
 */
class TitleProvider {

  /**
   * Returns page title for "entity.publication_page.edit_form" route.
   *
   * @param \Drupal\Core\Routing\RouteMatchInterface $routeMatch
   *   The current route match.
   *
   * @return \Drupal\Core\StringTranslation\TranslatableMarkup
   *   The page title.
   */
  public function publicationPageEditForm(RouteMatchInterface $routeMatch): TranslatableMarkup {
    $publicationPage = $routeMatch->getParameter('publication_page');
    assert($publicationPage instanceof PublicationPageInterface);
    $publication = $publicationPage->getPublicationEntity();

    return new TranslatableMarkup('Edit <em>@page</em> page on <em>@publication</em> (@type)', [
      '@page' => array_search($publicationPage->id(), $publication->getPageIds()) + 1,
      '@publication' => $publication->label(),
      '@type' => $publication->getBundleEntity()->label(),
    ]);
  }

  /**
   * Returns page title for "entity.publication.add_form" route.
   *
   * @param \Drupal\Core\Routing\RouteMatchInterface $routeMatch
   *   The current route match.
   *
   * @return \Drupal\Core\StringTranslation\TranslatableMarkup
   *   The page title.
   */
  public function publicationAddForm(RouteMatchInterface $routeMatch): TranslatableMarkup {
    $publicationType = $routeMatch->getParameter('publication_type');
    assert($publicationType instanceof PublicationType);

    return new TranslatableMarkup('Add @type', ['@type' => $publicationType->label()]);
  }

  /**
   * Returns page title for "entity.publication_page.collection" route.
   *
   * @param \Drupal\Core\Routing\RouteMatchInterface $routeMatch
   *   The current route match.
   *
   * @return \Drupal\Core\StringTranslation\TranslatableMarkup
   *   The page title.
   */
  public function publicationPageCollection(RouteMatchInterface $routeMatch): TranslatableMarkup {
    $publication = $routeMatch->getParameter('publication');
    assert($publication instanceof PublicationInterface);

    return new TranslatableMarkup('Publication pages of ":label"', [':label' => $publication->label()]);
  }

}
