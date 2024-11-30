<?php

declare(strict_types=1);

namespace App\Module\Cli\Helper;

use Air\Core\Exception\ClassWasNotFound;
use Air\Crud\Model\Language;
use Air\Map;
use Air\Model\Exception\CallUndefinedMethod;
use Air\Model\Exception\ConfigWasNotProvided;
use Air\Model\Exception\DriverClassDoesNotExists;
use Air\Model\Exception\DriverClassDoesNotExtendsFromDriverAbstract;
use Air\Model\Meta\Exception\CollectionCantBeWithoutPrimary;
use Air\Model\Meta\Exception\CollectionCantBeWithoutProperties;
use Air\Model\Meta\Exception\CollectionNameDoesNotExists;
use Air\Model\Meta\Exception\PropertyIsSetIncorrectly;
use Air\Storage;
use Air\Type\RichContent;
use DOMDocument;
use DOMElement;
use ReflectionException;

class Product
{
  /**
   * @param DOMDocument $dom
   * @param Language $language
   * @param string $ymlSource
   * @return void
   * @throws ClassWasNotFound
   * @throws CallUndefinedMethod
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   * @throws CollectionCantBeWithoutPrimary
   * @throws CollectionCantBeWithoutProperties
   * @throws CollectionNameDoesNotExists
   * @throws PropertyIsSetIncorrectly
   * @throws ReflectionException
   */
  protected static function getProductsMap(DOMDocument $dom, Language $language, string $ymlSource): void
  {
    Log::l('Preparing map');
    $offers = $dom->getElementsByTagName('offer');

    if (!$offers->length) {
      $offers = $dom->getElementsByTagName('item');
    }

    Log::l('Received ' . $offers->length . ' offers');

    foreach ($offers as $index => $offer) {

      Log::l('Preparing product ' . ($index + 1) . ' / ' . $offers->length);

      $product = Map::executeSingle($offer, [

        'title' => function (DOMElement $element): string {
          return Str::normalize(Str::mixedValue($element, 'name'));
        },

        'description' => function (): string {
          return '';
        },

        'content' => function (): string {
          return '';
        },

        'richContent' => function (DOMElement $element): array {
          return [[
            'type' => RichContent::TYPE_HTML,
            'value' => Str::mixedValue($element, 'description')
          ]];
        },

        'previewSpecifications' => function (DOMElement $element): array {
          return Params::getParams($element);
        },

        'fullSpecifications' => function (DOMElement $element): array {
          return [[
            'title' => 'Характеристики',
            'fullSpecifications' => Params::getParams($element, 100)
          ]];
        },

        'brand' => function (DOMElement $element): string|null {
          $brand = Brand::create(Str::mixedValue($element, 'vendor', ''));
          if ($brand) {
            return $brand->id;
          }
          return Brand::getDefaultBrand()->id;
        },

        'category' => function (DOMElement $element) use ($language, $ymlSource): string|null {
          return \App\Model\Category::fetchOne([
            'ymlId' => intval(Str::mixedValue($element, 'categoryId')),
            'language' => $language,
            'ymlSource' => $ymlSource,
          ])?->id;
        },

        'categories' => function (DOMElement $element) use ($language, $ymlSource): array {
          $category = \App\Model\Category::fetchOne([
            'ymlId' => intval(Str::mixedValue($element, 'categoryId')),
            'language' => $language,
            'ymlSource' => $ymlSource,
          ]);
          if (!$category) {
            return [];
          }
          return Category::getCategoryBranchIds($category);
        },

        'oldPrice' => function (DOMElement $element): float {
          return floatval(Str::mixedValue($element, 'oldprice', 0));
        },

        'price' => function (DOMElement $element): float {
          return floatval(Str::mixedValue($element, ['price', 'priceuah'], 0));
        },

        'country' => function (DOMElement $element): string|null {
          $country = Country::create(Str::mixedValue($element, 'country_of_origin', ''));
          if ($country) {
            return $country->id;
          }
          return Country::getDefaultCountry()->id;
        },

        'vendorCode' => function (DOMElement $element): string {
          return (string)Str::mixedValue($element, 'vendorCode', '');
        },

        'barcode' => function (DOMElement $element): string {
          return (string)Str::mixedValue($element, ['sku', 'barcode'], '');
        },

        'quantity' => function (DOMElement $element): int {
          return intval(Str::mixedValue($element, 'quantity_in_stock', 0));
        },

        'availability' => function (DOMElement $element): int {
          return $element->getAttribute('available') === 'true'
            ? \App\Model\Product::AVAILABILITY_YES
            : \App\Model\Product::AVAILABILITY_NO;
        },

        'url' => function (DOMElement $element): string {
          return Str::slug(Str::mixedValue($element, 'name'));
        },

        'ymlId' => function (DOMElement $element): int {
          return Str::id($element->getAttribute('id'));
        },

        'ymlCategoryId' => function (DOMElement $element): int {
          return intval(Str::mixedValue($element, 'categoryId'));
        },
      ]);

      if (!$product['category']) {
        Log::l("Skipping product without category " . $product['ymlId']);
        continue;
      }

      $product = array_merge($product, [
        'ymlSource' => $ymlSource,
      ]);

      Log::l("Start uploading images");
      $images = Image::getImages($product, $offer);

      $image = $images[0];

      $product = array_merge($product, [
        'images' => $images,
        'image' => $image,
      ]);

      Log::l("Creating meta");
      $product = array_merge($product, [
        'meta' => Meta::getMeta($product),
      ]);

      Log::l("Setup defaults");
      $product = array_merge($product, [
        'language' => $language->id,
        'isNew' => false,
        'isSale' => false,
        'filters' => [],
        'recommendedProducts' => [],
        'alsoBuyProducts' => [],
        'popularity' => 0,
        'enabled' => true,
      ]);

      $product = new \App\Model\Product($product);
      $product->save();
    }
  }

  /**
   * @param DOMDocument $dom
   * @param string $ymlSource
   * @return void
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws CollectionCantBeWithoutPrimary
   * @throws CollectionCantBeWithoutProperties
   * @throws CollectionNameDoesNotExists
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   * @throws PropertyIsSetIncorrectly
   * @throws ReflectionException
   */
  public static function upload(DOMDocument $dom, string $ymlSource): void
  {
    Log::l('Removing products for YML: ' . $ymlSource);

    \App\Model\Product::remove([
      'ymlSource' => $ymlSource
    ]);

    Log::l('Removing images for: ' . $ymlSource);
    Storage::deleteFolder('/catalog/' . $ymlSource);

    foreach (Language::all() as $language) {
      self::getProductsMap($dom, $language, $ymlSource);
    }
  }
}