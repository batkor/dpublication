<?php

/**
 * @file
 * Implements base preprocess for templates.
 */

declare(strict_types=1);

use Drupal\Component\Utility\Html;
use Drupal\Core\Render\Element;

/**
 * Implements hook_preprocess_HOOK().
 */
function template_preprocess_publication_pager(&$variables): void {
  $variables['id'] = Html::getId('publication_pager');
}

/**
 * Prepares variables for "publication.html.twig" template.
 *
 * @param array $variables
 *   The variables list.
 */
function template_preprocess_publication(array &$variables): void {
  _dpublication_default_prepare_template($variables, 'publication');

  if (!$variables['entity']->get('pages')->isEmpty()) {
    $variables['read_url'] = $variables['entity']
      ->get('pages')
      ->first()
      ->get('entity')
      ->getValue()
      ->toUrl();
  }
}

/**
 * Prepares variables for "publication.html.twig" template.
 *
 * @param array $variables
 *   The variables list.
 */
function template_preprocess_publication_page(array &$variables): void {
  _dpublication_default_prepare_template($variables, 'publication_page');
}

/**
 * The common handler for prepare templates.
 *
 * @param array $variables
 *   The template variables.
 * @param string $entityKey
 *   The entity key on variables list.
 */
function _dpublication_default_prepare_template(array &$variables, string $entityKey): void {
  $variables['entity'] = $variables['elements']["#{$entityKey}"];
  $variables['bundle'] = $variables['entity']->bundle();

  foreach (Element::children($variables['elements']) as $key) {
    $variables[$entityKey][$key] = $variables['elements'][$key];
  }

  // @see code/web/core/lib/Drupal/Core/Entity/Controller/EntityViewController.php:96
  if (array_key_exists('title', $variables[$entityKey])) {
    $variables[$entityKey]['title']['#access'] = TRUE;
  }
}
