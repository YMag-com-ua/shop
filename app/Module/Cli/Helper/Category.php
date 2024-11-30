<?php

declare(strict_types=1);

namespace App\Module\Cli\Helper;

use Air\Core\Exception\ClassWasNotFound;
use Air\Crud\Model\Language;
use Air\Model\Exception\CallUndefinedMethod;
use Air\Model\Exception\ConfigWasNotProvided;
use Air\Model\Exception\DriverClassDoesNotExists;
use Air\Model\Exception\DriverClassDoesNotExtendsFromDriverAbstract;
use Air\Model\Meta\Exception\PropertyWasNotFound;
use Air\Type\FaIcon;
use Air\Type\Meta;
use DOMDocument;
use ReflectionException;
use Throwable;

class Category
{
  /**
   * @param DOMDocument $dom
   * @return array
   */
  protected static function getCategoriesMap(DOMDocument $dom): array
  {
    $categories = [];

    /** @var \DOMElement $category */
    foreach ($dom->getElementsByTagName('category') as $category) {

      $id = intval($category->getAttribute('id'));
      $parentId = intval($category->getAttribute('parentId'));
      $title = Str::normalize($category->nodeValue);

      $categories[$id] = [
        'parentId' => $parentId,
        'title' => $title,
        'id' => $id
      ];
    }

    return $categories;
  }

  /**
   * @param string $title
   * @param Language $language
   * @param int $ymlId
   * @param string $ymlSource
   * @param int|null $ymlParentId
   * @return \App\Model\Category
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   * @throws PropertyWasNotFound
   * @throws ReflectionException
   * @throws Throwable
   */
  protected static function create(
    string   $title,
    Language $language,
    int      $ymlId,
    string   $ymlSource,
    ?int     $ymlParentId = null,
  ): \App\Model\Category
  {
    $category = \App\Model\Category::fetchOne([
      'ymlId' => $ymlId,
      'ymlSource' => $ymlSource,
      'language' => $language,
    ]);

    if ($category) {
      return $category;
    }

    $category = new \App\Model\Category();

    $category->url = Str::slug($title);
    $category->title = $title;

    $category->ymlId = $ymlId;
    $category->ymlSource = $ymlSource;
    $category->ymlParentId = $ymlParentId;

    $category->faIcon = new FaIcon(['icon' => 'star']);
    $category->meta = new Meta(['useModelData' => true]);

    $category->enabled = true;
    $category->language = $language;

    $category->save();

    return $category;
  }

  /**
   * @param DOMDocument $dom
   * @param string $ymlSource
   * @return void
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   * @throws PropertyWasNotFound
   * @throws ReflectionException
   * @throws Throwable
   */
  public static function buildTree(DOMDocument $dom, string $ymlSource): void
  {
    Log::l('Removing categories for YML: ' . $ymlSource);
    \App\Model\Category::remove([
      'ymlSource' => $ymlSource
    ]);

    $categoryMap = self::getCategoriesMap($dom);

    foreach (Language::all() as $language) {
      foreach ($categoryMap as $ymlId => $categoryData) {
        self::create($categoryData['title'], $language, $ymlId, $ymlSource, $categoryData['parentId']);
        Log::l("Created category : " . $categoryData['title']);
      }
    }

    $categories = \App\Model\Category::fetchAll([
      'ymlSource' => $ymlSource,
      'ymlParentId' => ['$ne' => null]
    ]);

    foreach ($categories as $category) {

      $parent = \App\Model\Category::fetchOne([
        'ymlId' => $category->ymlParentId,
        'language' => $category->language
      ]);

      if ($parent) {
        $category->parent = $parent;
        $category->save();

        Log::l("Updated parent category for: " . $category->title);

      } else {
        Log::l("No parent category for", [
          'title' => $category->title,
          'ymlId' => $category->ymlId,
          'ymlParentId' => $category->ymlParentId,
          'ymlSource' => $category->ymlSource,
        ]);
      }
    }
  }

  /**
   * @param \App\Model\Category $category
   * @return array
   */
  public static function getCategoryBranchIds(\App\Model\Category $category): array
  {
    $ids = [$category->id];

    if ($category->parent) {
      $ids = array_merge($ids, self::getCategoryBranchIds($category->parent));
    }

    return $ids;
  }

  /**
   * @param string $ymlSource
   * @return void
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   */
  public static function disableEmptyCategories(string $ymlSource): void
  {
    $categories = \App\Model\Category::fetchAll([
      'ymlSource' => $ymlSource,
      'enabled' => true
    ]);

    $categoriesCount = count($categories);

    Log::l('Founded categories: ' . $categoriesCount);

    foreach ($categories as $index => $category) {
      Log::l('Processing category ' . ($index + 1) . ' / ' . $categoriesCount);

      if (!\App\Model\Product::count(['categories' => $category->id])) {
        $category->enabled = false;
        $category->save();
      }
    }
  }

  /**
   * @param string $ymlSource
   * @return void
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   */
  public static function updateCategoryImages(string $ymlSource): void
  {
    $categories = \App\Model\Category::fetchAll([
      'enabled' => true,
      'ymlSource' => $ymlSource
    ]);

    $categoriesCount = count($categories);

    Log::l('Founded categories: ' . $categoriesCount);

    foreach ($categories as $index => $category) {

      Log::l('Updating image for: ' . ($index + 1) . ' / ' . $categoriesCount);

      $product = \App\Model\Product::fetchOne([
        'categories' => $category->id
      ]);

      if ($product) {
        $category->image = $product->image;
        $category->save();
      }
    }
  }
}