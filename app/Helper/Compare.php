<?php

declare(strict_types=1);

namespace App\Helper;

use Air\Cookie;
use Air\Core\Exception\ClassWasNotFound;
use Air\Model\Exception\CallUndefinedMethod;
use Air\Model\Exception\ConfigWasNotProvided;
use Air\Model\Exception\DriverClassDoesNotExists;
use Air\Model\Exception\DriverClassDoesNotExtendsFromDriverAbstract;
use App\Model\Product;

class Compare
{
  /**
   * @return array
   * @throws ClassWasNotFound
   */
  private static function getRawProducts(): array
  {
    return Cookie::get('compare') ?? [];
  }

  /**
   * @param array $productUrls
   * @return void
   * @throws ClassWasNotFound
   */
  private static function setRawProducts(array $productUrls): void
  {
    Cookie::set('compare', $productUrls);
  }

  /**
   * @return array|Product[]
   * @throws ClassWasNotFound
   * @throws CallUndefinedMethod
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   */
  public static function products(): array
  {
    $products = [];
    foreach (self::getRawProducts() as $rawProduct) {
      $products[] = Product::one(['url' => $rawProduct]);
    }
    return $products;
  }

  /**
   * @param Product $product
   * @return void
   * @throws ClassWasNotFound
   */
  public static function add(Product $product): void
  {
    $products = self::getRawProducts();
    $products[] = $product->url;
    self::setRawProducts(array_values(array_unique($products)));
  }

  /**
   * @param Product $product
   * @return void
   * @throws ClassWasNotFound
   */
  public static function remove(Product $product): void
  {
    $products = [];
    foreach (self::getRawProducts() as $rawProduct) {
      if ($rawProduct !== $product->url) {
        $products[] = $rawProduct;
      }
    }
    self::setRawProducts($products);
  }

  /**
   * @param Product $product
   * @return bool
   * @throws ClassWasNotFound
   */
  public static function isIn(Product $product): bool
  {
    return in_array($product->url, self::getRawProducts(), true);
  }

  /**
   * @return int
   * @throws ClassWasNotFound
   */
  public static function count(): int
  {
    return count(self::getRawProducts());
  }

  /**
   * @return array
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   */
  public static function specificationMatrix(): array
  {
    $matrix = [];
    $products = self::products();

    foreach ($products as $product) {

      foreach ($product->fullSpecifications as $specificationGroup) {
        $matrix[$specificationGroup['title']] = $matrix[$specificationGroup['title']] ?? [];

        foreach ($specificationGroup['fullSpecifications'] as $specification) {
          $matrix[$specificationGroup['title']][$specification['key']] =
            $matrix[$specificationGroup['title']][$specification['key']] ?? [];

          $matrix[$specificationGroup['title']][$specification['key']][$product->url] = $specification['value'];
        }
      }
    }

    return $matrix;
  }
}