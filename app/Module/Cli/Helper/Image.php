<?php

declare(strict_types=1);

namespace App\Module\Cli\Helper;

use Air\Core\Exception\ClassWasNotFound;
use Air\Model\Exception\CallUndefinedMethod;
use Air\Model\Exception\ConfigWasNotProvided;
use Air\Model\Exception\DriverClassDoesNotExists;
use Air\Model\Exception\DriverClassDoesNotExtendsFromDriverAbstract;
use Air\Storage;
use App\Model\CatalogSettings;
use DOMElement;
use Throwable;

class Image
{
  /**
   * @param array $product
   * @param DOMElement $element
   * @return array
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   */
  public static function getImages(array $product, DOMElement $element): array
  {
    $productCopy = \App\Model\Product::fetchOne([
      'ymlId' => $product['ymlId'],
      'ymlSource' => $product['ymlSource']
    ]);

    if ($productCopy) {
      $images = [];
      foreach ($productCopy->images as $image) {
        $images[] = $image->toArray();
      }
      return $images;
    }

    $images = [];

    $category = \App\Model\Category::fetchOne([
      'id' => $product['category']
    ]);

    $folder = implode('/', [
      'catalog',
      $product['ymlSource'],
      $category?->url ?? 'no-category-' . $product['ymlCategoryId'],
      $product['url'],
    ]);

    Storage::createFolder('/', $folder, true);

    $sourceImages = Str::arrayValue($element, ['image', 'picture']);

    foreach ($sourceImages as $index => $image) {
      try {
        Log::l("Trying to upload image " . ($index + 1)  . ' / ' . $index);

        $images[] = Storage::uploadByUrl('/' . $folder, $image);
      } catch (Throwable) {
      }
    }

    if (!count($images)) {
      Log::l("Product does not have an images " . $product['ymlId']);

      $catalogSettings = CatalogSettings::singleOne();
      $images[] = $catalogSettings->noImage->toArray();
    }

    return $images;
  }
}