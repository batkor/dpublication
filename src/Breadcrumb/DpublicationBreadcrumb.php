<?php

namespace Drupal\dpublication\Breadcrumb;

use Drupal\Core\Breadcrumb\Breadcrumb;
use Drupal\Core\Breadcrumb\BreadcrumbBuilderInterface;
use Drupal\Core\Link;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\node\NodeInterface;

/**
 * Provides a breadcrumb builder for articles.
 */
class DpublicationBreadcrumb implements BreadcrumbBuilderInterface {

  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public function applies(RouteMatchInterface $routeMatch) {
    return array_key_exists($routeMatch->getRouteName(), $this->callbacksMap());
  }

  /**
   * {@inheritdoc}
   */
  public function build(RouteMatchInterface $routeMatch) {
    $method = $this->callbacksMap()[$routeMatch->getRouteName()];

    return call_user_func([$this, $method], $routeMatch);
  }

  /**
   * Build breadcrumb instance for "entity.publication_page.add_form" route.
   *
   * @param \Drupal\Core\Routing\RouteMatchInterface $routeMatch
   *   The route match instance.
   *
   * @return \Drupal\Core\Breadcrumb\Breadcrumb
   *   The new breadcrumb instance.
   */
  protected function publicationPage(RouteMatchInterface $routeMatch): Breadcrumb {
    $breadcrumb = new Breadcrumb();
    $links = $this->baseLinks();
    /** @var \Drupal\dpublication\Entity\Publication $publication */
    $publication = $routeMatch->getParameter('publication');
    $links[] = $publication->toLink();
    $links[] = Link::createFromRoute($this->t('Publication pages'), 'entity.publication_page.collection', [
      'publication' => $publication->id(),
    ]);

    if ($routeMatch->getRouteName() === 'entity.publication_page.edit_form') {
      $links[] = Link::createFromRoute($this->t('Edit publication page'), '<none>');
    }

    if ($routeMatch->getRouteName() === 'entity.publication_page.add_form') {
      $links[] = Link::createFromRoute($this->t('Create publication page'), '<none>');
    }

    if ($routeMatch->getRouteName() === 'entity.publication_page.content_translation_overview') {
      $links[] = Link::createFromRoute($this->t('Publication page translations'), '<none>');
    }

    return $breadcrumb->setLinks($links);
  }

  /**
   * Returns breadcrumb for publication routes.
   *
   * @param \Drupal\Core\Routing\RouteMatchInterface $routeMatch
   *   The current route match.
   *
   * @return \Drupal\Core\Breadcrumb\Breadcrumb
   *   The breadcrumb.
   */
  public function publication(RouteMatchInterface $routeMatch): Breadcrumb {
    $breadcrumb = new Breadcrumb();
    $links = $this->baseLinks();
    /** @var \Drupal\dpublication\Entity\Publication $publication */
    $publication = $routeMatch->getParameter('publication');
    $links[] = $publication->toLink();

    if ($routeMatch->getRouteName() === 'entity.publication.edit_form') {
      $links[] = Link::createFromRoute($this->t('Edit publication'), '<none>');
    }

    if ($routeMatch->getRouteName() === 'entity.publication.content_translation_overview') {
      $links[] = Link::createFromRoute($this->t('Publication translations'), '<none>');
    }

    return $breadcrumb->setLinks($links);
  }

  /**
   * Returns base links.
   *
   * @return \Drupal\Core\Link[]
   *   The links list.
   */
  protected function baseLinks(): array {
    return [
      Link::createFromRoute($this->t('Home'), '<front>'),
      Link::createFromRoute($this->t('Publications'), 'entity.publication.collection'),
    ];
  }

  /**
   * Returns callback methods for route names.
   *
   * @return string[]
   *   The callback methods.
   */
  protected function callbacksMap(): array {
    return [
      'entity.publication.canonical' => 'publication',
      'entity.publication.edit_form' => 'publication',
      'entity.publication.content_translation_overview' => 'publication',
      'entity.publication_page.content_translation_overview' => 'publicationPage',
      'entity.publication_page.collection' => 'publicationPage',
      'entity.publication_page.add_form' => 'publicationPage',
      'entity.publication_page.edit_form' => 'publicationPage',
    ];
  }

}
