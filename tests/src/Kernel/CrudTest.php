<?php

namespace Drupal\Tests\dpublication\Kernel;

use Drupal\dpublication\Entity\Publication;
use Drupal\dpublication\Entity\PublicationPage;
use Drupal\dpublication\Entity\PublicationPageType;
use Drupal\dpublication\Entity\PublicationType;
use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\RandomGeneratorTrait;

/**
 * Tests loading, saving, read and deleting entities.
 *
 * @group dpublication
 */
class CrudTest extends KernelTestBase {

  use RandomGeneratorTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'user',
    'dpublication',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->installEntitySchema('user');
    $this->installEntitySchema('publication_type');
    $this->installEntitySchema('publication');
    $this->installEntitySchema('publication_page_type');
    $this->installEntitySchema('publication_page');
  }

  /**
   * Test CRUD for "publication_type" entity.
   */
  public function testPublicationType() {
    $id = $this->randomMachineName();
    // Create.
    $publicationType = PublicationType::create([
      'id' => $id,
      'label' => $this->randomString(),
      'publicationPageType' => $this->randomString(),
    ]);
    $this->assertTrue($publicationType->isNew());
    // Save.
    $publicationType->save();
    $this->assertNotTrue($publicationType->isNew());
    // Load.
    $publicationType = PublicationType::load($id);
    $this->assertTrue($publicationType instanceof PublicationType);
    // Delete.
    $publicationType->delete();
    $query = \Drupal::entityQuery('publication_type')->accessCheck(FALSE)->count();
    $this->assertEquals(0, $query->execute());
  }

  /**
   * Test CRUD for "publication_page_type" entity.
   */
  public function testPublicationPageType() {
    $id = $this->randomMachineName();
    // Create.
    $publicationPageType = PublicationPageType::create([
      'id' => $id,
      'label' => $this->randomString(),
    ]);
    $this->assertTrue($publicationPageType->isNew());
    // Save.
    $publicationPageType->save();
    $this->assertNotTrue($publicationPageType->isNew());
    // Load.
    $publicationPageType = PublicationPageType::load($id);
    $this->assertTrue($publicationPageType instanceof PublicationPageType);
    // Delete.
    $publicationPageType->delete();
    $query = \Drupal::entityQuery('publication_page_type')
      ->accessCheck(FALSE)
      ->count();
    $this->assertEquals(0, $query->execute());
  }

  /**
   * Test CRUD for "publication_page" entity.
   */
  public function testPublicationPage() {
    $publicationPageType = PublicationPageType::create([
      'id' => $this->randomMachineName(),
      'label' => $this->randomString(),
    ]);
    $publicationPageType->save();

    // Create.
    $publicationPage = PublicationPage::create([
      'title' => $this->randomString(),
      'type' => $publicationPageType->id(),
    ]);
    $this->assertTrue($publicationPage->isNew());
    // Save.
    $publicationPage->save();
    $this->assertNotTrue($publicationPage->isNew());
    // Load.
    $publicationPages = PublicationPage::loadMultiple();
    $publicationPage = reset($publicationPages);

    $this->assertTrue($publicationPage instanceof PublicationPage);
    // Delete.
    $publicationPage->delete();
    $query = \Drupal::entityQuery('publication_page')
      ->accessCheck(FALSE)
      ->count();
    $this->assertEquals(0, $query->execute());
  }

  /**
   * Test CRUD for "publication" entity.
   */
  public function testPublication() {
    $publicationType = PublicationType::create([
      'id' => $this->randomMachineName(),
      'label' => $this->randomString(),
      'publicationPageType' => $this->randomString(),
    ]);
    $publicationType->save();
    // Create.
    $publication = Publication::create([
      'title' => $this->randomString(),
      'type' => $publicationType->id(),
    ]);

    // Save.
    $publication->save();
    $this->assertNotTrue($publication->isNew());
    // Load.
    $publication = Publication::load($publication->id());
    $this->assertTrue($publication instanceof Publication);
    // Delete.
    $publication->delete();
    $query = \Drupal::entityQuery('publication')
      ->accessCheck(FALSE)
      ->count();
    $this->assertEquals(0, $query->execute());
  }

}
