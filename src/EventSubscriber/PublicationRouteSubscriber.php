<?php

namespace Drupal\dpublication\EventSubscriber;

use Drupal\Core\Routing\RouteSubscriberBase;
use Drupal\Core\Routing\RoutingEvents;
use Symfony\Component\Routing\RouteCollection;

/**
 * Route subscriber.
 */
class PublicationRouteSubscriber extends RouteSubscriberBase {

  /**
   * {@inheritdoc}
   */
  protected function alterRoutes(RouteCollection $collection) {
    $contentTranslationRoutes = [
      'entity.publication_page.content_translation_overview',
      'entity.publication_page.content_translation_add',
      'entity.publication_page.content_translation_edit',
      'entity.publication_page.content_translation_delete',
    ];

    foreach ($contentTranslationRoutes as $name) {
      $route = $collection->get($name);

      if (empty($route)) {
        continue;
      }

      $params = $route->getOption('parameters');
      $params['publication'] = [
        'type' => 'entity:publication',
      ];
      $params['publication_page'] = [
        'type' => 'entity:publication_page',
      ];
      $route->setOption('parameters', $params);
    }
  }

  /**
   * {@inheritdoc}
   *
   * Run after ContentTranslationRouteSubscriber and
   * before ParamConverterSubscriber.
   *
   * @see \Drupal\content_translation\Routing\ContentTranslationRouteSubscriber::getSubscribedEvents
   * @see \Drupal\Core\EventSubscriber\ParamConverterSubscriber::getSubscribedEvents
   */
  public static function getSubscribedEvents():array {
    return [
      RoutingEvents::ALTER => ['onAlterRoutes', -211],
    ];
  }

}
