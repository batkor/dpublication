<?php

namespace Drupal\Tests\dpublication\Functional;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Url;
use Drupal\Tests\BrowserTestBase;
use Drupal\user\Entity\User;

/**
 * Provides tests permissions to entities.
 *
 * @group dpublication
 */
class PermissionsTest extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['dpublication', 'dpublication_test'];

  /**
   * A simple user for tests.
   */
  protected User $user;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->user = $this->drupalCreateUser([]);
  }

  /**
   * Tests a permissions an entity pages on create and canonical.
   */
  public function testPublicationCreate(): void {
    // Simple user not allow to create publication.
    $this->drupalLogin($this->user);
    $this->drupalGet(Url::fromRoute('entity.publication.add_form', [
      'publication_type' => 'test_book',
    ]));
    $this->assertSession()->statusCodeEquals(403);
    // Allow to create publication if user exist permissions.
    $checkUser = $this->drupalCreateUser([
      'create test_book publication',
      'access content',
    ]);
    $this->drupalLogin($checkUser);
    $this->drupalGet(Url::fromRoute('entity.publication.add_form', [
      'publication_type' => 'test_book',
    ]));
    $this->assertSession()->statusCodeEquals(200);
    $title = 'Test book title';
    $values = [
      'title[0][value]' => $title,
    ];
    $this->submitForm($values, 'Create');
    $publications = $this
      ->container
      ->get('entity_type.manager')
      ->getStorage('publication')
      ->loadByProperties([
        'title' => $title,
      ]);

    foreach ($publications as $publication) {
      $this->drupalGet($publication->toUrl());
      $this->assertSession()->statusCodeEquals(200);
      $this->assertSession()->pageTextContains($title);
    }
  }

  /**
   * Tests for edit publication entity permissions.
   */
  public function testPublicationEdit(): void {
    // Check "edit own publication" disallowed.
    $publication1 = $this
      ->container
      ->get('entity_type.manager')
      ->getStorage('publication')
      ->create([
        'title' => $this->randomString(),
        'type' => 'test_book',
        'uid' => $this->user->id(),
      ]);
    $publication1->save();
    $this->drupalLogin($this->user);
    $this->drupalGet(Url::fromRoute('entity.publication.edit_form', [
      'publication' => $publication1->id(),
    ]));
    $this->assertSession()->statusCodeEquals(403);

    // Check "edit own publication" allowed.
    $checkUser = $this->drupalCreateUser([
      'edit own test_book publication',
      'access content',
    ]);
    $publication2 = $this
      ->container
      ->get('entity_type.manager')
      ->getStorage('publication')
      ->create([
        'title' => $this->randomString(),
        'type' => 'test_book',
        'uid' => $checkUser->id(),
      ]);
    $publication2->save();
    $this->drupalLogin($checkUser);
    $url = Url::fromRoute('entity.publication.edit_form', [
      'publication' => $publication2->id(),
    ]);
    $title = 'New title';
    $values = [
      'title[0][value]' => $title,
    ];
    $this->editAndAssertUpdate($values, $publication2, $url, $title);

    // Check "edit any publication" disallowed.
    $this->drupalGet(Url::fromRoute('entity.publication.edit_form', [
      'publication' => $publication1->id(),
    ]));
    $this->assertSession()->statusCodeEquals(403);

    // Check "edit any publication" allowed.
    $checkUser = $this->drupalCreateUser([
      'edit any test_book publication',
      'access content',
    ]);
    $this->drupalLogin($checkUser);
    $url = Url::fromRoute('entity.publication.edit_form', [
      'publication' => $publication1->id(),
    ]);
    $title = 'Edit any publication';
    $values = [
      'title[0][value]' => $title,
    ];
    $this->editAndAssertUpdate($values, $publication1, $url, $title);
  }

  /**
   * Tests for delete permissions.
   */
  public function testPublicationDelete(): void {
    $publication1 = $this
      ->container
      ->get('entity_type.manager')
      ->getStorage('publication')
      ->create([
        'title' => $this->randomString(),
        'type' => 'test_book',
        'uid' => 1,
      ]);
    $publication1->save();

    // Check "delete own publication" disallowed.
    $checkUser = $this->drupalCreateUser([
      'access content',
    ]);
    $this->drupalLogin($checkUser);
    $this->drupalGet(Url::fromRoute('entity.publication.delete_form', [
      'publication' => $publication1->id(),
    ]));
    $this->assertSession()->statusCodeEquals(403);

    // Check "delete own publication" allowed.
    $checkUser = $this->drupalCreateUser([
      'delete own test_book publication',
      'access content',
    ]);
    $publication2 = $this
      ->container
      ->get('entity_type.manager')
      ->getStorage('publication')
      ->create([
        'title' => $this->randomString(),
        'type' => 'test_book',
        'uid' => $checkUser->id(),
      ]);
    $publication2->save();
    $this->drupalLogin($checkUser);
    $pid2 = $publication2->id();
    $this->drupalGet(Url::fromRoute('entity.publication.delete_form', [
      'publication' => $pid2,
    ]));
    $this->assertSession()->statusCodeEquals(200);
    $this->submitForm([], 'Delete');
    $publication2 = $this
      ->container
      ->get('entity_type.manager')
      ->getStorage('publication')
      ->load($pid2);
    $this->assertNull($publication2);

    // Check "delete any publication" allowed.
    $checkUser = $this->drupalCreateUser([
      'delete any test_book publication',
      'access content',
    ]);
    $this->drupalLogin($checkUser);
    $pid1 = $publication1->id();
    $this->drupalGet(Url::fromRoute('entity.publication.delete_form', [
      'publication' => $pid1,
    ]));
    $this->assertSession()->statusCodeEquals(200);
    $this->submitForm([], 'Delete');
    $publication1 = $this
      ->container
      ->get('entity_type.manager')
      ->getStorage('publication')
      ->load($pid1);
    $this->assertNull($publication1);
  }

  /**
   * Tests permissions for create "publication" entity.
   */
  public function testCreatePublicationPage(): void {
    // Check "create publication" permission.
    $publication1 = $this
      ->container
      ->get('entity_type.manager')
      ->getStorage('publication')
      ->create([
        'title' => $this->randomString(),
        'type' => 'test_book',
        'uid' => 1,
      ]);
    $publication1->save();
    $this->drupalLogin($this->user);
    $this->drupalGet(Url::fromRoute('entity.publication_page.add_form', [
      'publication' => $publication1->id(),
    ]));
    $this->assertSession()->statusCodeEquals(403);

    $checkUser = $this->drupalCreateUser([
      'create test_book publication',
      'access content',
    ]);
    $this->drupalLogin($checkUser);
    $this->drupalGet(Url::fromRoute('entity.publication_page.add_form', [
      'publication' => $publication1->id(),
    ]));
    $this->assertSession()->statusCodeEquals(200);
    $title = 'Test book page title';
    $values = [
      'title[0][value]' => $title,
    ];
    $this->submitForm($values, 'Create');
    $publicationPages = $this
      ->container
      ->get('entity_type.manager')
      ->getStorage('publication_page')
      ->loadByProperties([
        'publication' => $publication1->id(),
        'title' => $title,
      ]);
    $this->assertNotEmpty($publicationPages);
  }

  /**
   * Tests for edit "publication_page" entity permissions.
   */
  public function testPublicationPageEdit(): void {
    $checkUser = $this->drupalCreateUser([
      'edit own test_book publication',
      'access content',
    ]);
    $publication1 = $this
      ->container
      ->get('entity_type.manager')
      ->getStorage('publication')
      ->create([
        'title' => $this->randomString(),
        'type' => 'test_book',
        'uid' => $checkUser->id(),
      ]);
    $publication1->save();
    $publicationPage1 = $this
      ->container
      ->get('entity_type.manager')
      ->getStorage('publication_page')
      ->create([
        'title' => $this->randomString(),
        'type' => 'test_book_page',
        'publication' => $publication1->id(),
      ]);
    $publicationPage1->save();
    // Checked user not having permissions.
    $this->drupalLogin($this->user);
    $this->drupalGet(Url::fromRoute('entity.publication_page.edit_form', [
      'publication' => $publication1->id(),
      'publication_page' => $publicationPage1->id(),
    ]));
    $this->assertSession()->statusCodeEquals(403);

    $this->drupalLogin($checkUser);
    $url = Url::fromRoute('entity.publication_page.edit_form', [
      'publication' => $publication1->id(),
      'publication_page' => $publicationPage1->id(),
    ]);
    $this->assertSession()->statusCodeEquals(200);
    $title = 'New title';
    $values = [
      'title[0][value]' => $title,
    ];
    $this->editAndAssertUpdate($values, $publicationPage1, $url, $title);

    // Check "edit any publication" permission.
    $checkUser2 = $this->drupalCreateUser([
      'edit any test_book publication',
      'access content',
    ]);
    $this->drupalLogin($checkUser2);
    $title = 'New title 2';
    $values = [
      'title[0][value]' => $title,
    ];
    $url = Url::fromRoute('entity.publication_page.edit_form', [
      'publication' => $publication1->id(),
      'publication_page' => $publicationPage1->id(),
    ]);
    $this->editAndAssertUpdate($values, $publicationPage1, $url, $title);
  }

  /**
   * Tests for delete "publication_page" entity permissions.
   */
  public function testPublicationPageDelete(): void {
    $ownUser = $this->drupalCreateUser([
      'delete own test_book publication',
      'access content',
    ]);
    $publication1 = $this
      ->container
      ->get('entity_type.manager')
      ->getStorage('publication')
      ->create([
        'title' => $this->randomString(),
        'type' => 'test_book',
        'uid' => $ownUser->id(),
      ]);
    $publication1->save();
    $publicationPage1 = $this
      ->container
      ->get('entity_type.manager')
      ->getStorage('publication_page')
      ->create([
        'title' => $this->randomString(),
        'type' => 'test_book_page',
        'publication' => $publication1->id(),
      ]);
    $publicationPage1->save();

    // Checked user not having permissions.
    $this->drupalLogin($this->user);
    $this->drupalGet(Url::fromRoute('entity.publication_page.delete_form', [
      'publication' => $publication1->id(),
      'publication_page' => $publicationPage1->id(),
    ]));
    $this->assertSession()->statusCodeEquals(403);
    // Check "delete own publication" permission.
    $this->drupalLogin($ownUser);
    $pageId = $publicationPage1->id();
    $this->drupalGet(Url::fromRoute('entity.publication_page.delete_form', [
      'publication' => $publication1->id(),
      'publication_page' => $pageId,
    ]));
    $this->assertSession()->statusCodeEquals(200);
    $this->submitForm([], 'Delete');
    $publicationPage1 = $this
      ->container
      ->get('entity_type.manager')
      ->getStorage('publication_page')
      ->load($pageId);
    $this->assertNull($publicationPage1);

    // Check "delete any publication" permission.
    $publication1 = $this
      ->container
      ->get('entity_type.manager')
      ->getStorage('publication')
      ->create([
        'title' => $this->randomString(),
        'type' => 'test_book',
        'uid' => 1,
      ]);
    $publication1->save();
    $publicationPage1 = $this
      ->container
      ->get('entity_type.manager')
      ->getStorage('publication_page')
      ->create([
        'title' => $this->randomString(),
        'type' => 'test_book_page',
        'publication' => $publication1->id(),
      ]);
    $publicationPage1->save();
    $checkUser = $this->drupalCreateUser([
      'delete any test_book publication',
      'access content',
    ]);
    $this->drupalLogin($checkUser);
    $pageId = $publicationPage1->id();
    $this->drupalGet(Url::fromRoute('entity.publication_page.delete_form', [
      'publication' => $publication1->id(),
      'publication_page' => $pageId,
    ]));
    $this->assertSession()->statusCodeEquals(200);
    $this->submitForm([], 'Delete');
    $publicationPage1 = $this
      ->container
      ->get('entity_type.manager')
      ->getStorage('publication_page')
      ->load($pageId);
    $this->assertNull($publicationPage1);
  }

  /**
   * Go to edit for entity and check update on canonical route.
   *
   * @param array $values
   *   The new values.
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity.
   * @param \Drupal\Core\Url $url
   *   The url to edit form.
   * @param string $checkValue
   *   The text for check ib conanical route.
   */
  protected function editAndAssertUpdate(array $values, EntityInterface $entity, Url $url, string $checkValue): void {
    $this->drupalGet($url);
    $this->assertSession()->statusCodeEquals(200);
    $this->submitForm($values, 'Save');
    $this->drupalGet($entity->toUrl());
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->pageTextContains($checkValue);
  }

}
