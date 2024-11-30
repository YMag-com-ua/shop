<?php

declare(strict_types=1);

namespace App\Service;

use Air\Cookie;
use Air\Core\Exception\ClassWasNotFound;
use Air\Model\Driver\CursorAbstract;
use Air\Model\Exception\CallUndefinedMethod;
use Air\Model\Exception\ConfigWasNotProvided;
use Air\Model\Exception\DriverClassDoesNotExists;
use Air\Model\Exception\DriverClassDoesNotExtendsFromDriverAbstract;
use App\Model\Product;
use App\Model\UserProductWishList;
use App\Service\User\Auth;
use App\Service\User\Exception\UserCookieIsWrong;
use App\Service\User\Exception\UserIsNotLoggedIn;

class WishList
{
  /**
   * @return array
   * @throws ClassWasNotFound
   */
  private static function getCookieProducts(): array
  {
    return Cookie::get('wishlist') ?? [];
  }

  /**
   * @param array $productUrls
   * @return void
   * @throws ClassWasNotFound
   */
  private static function setCookieProducts(array $productUrls): void
  {
    Cookie::set('wishlist', $productUrls);
  }

  /**
   * @param int $count
   * @param int $offset
   * @return array|Product[]
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   * @throws UserCookieIsWrong
   * @throws UserIsNotLoggedIn
   */
  public static function products(int $count = 10, int $offset = 0): array
  {
    $products = [];

    if (!Auth::isLogged()) {
      foreach (self::getCookieProducts() as $rawProduct) {
        $products[] = Product::one(['url' => $rawProduct]);
      }
    } else {
      foreach (UserProductWishList::all(['user' => Auth::getUser()], ['createdAt' => -1], $count, $offset) as $item) {
        $products[] = Product::one(['url' => $item->productUrl]);
      }
    }

    return $products;
  }

  /**
   * @param Product $product
   * @return void
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   * @throws UserCookieIsWrong
   * @throws UserIsNotLoggedIn
   */
  public static function add(Product $product): void
  {
    if (!Auth::isLogged()) {
      $products = self::getCookieProducts();
      $products[] = $product->url;
      self::setCookieProducts(array_values(array_unique($products)));

      return;
    }

    $user = Auth::getUser();
    $productUrl = $product->url;

    if (!UserProductWishList::quantity(['user' => $user, 'productUrl' => $productUrl])) {
      UserProductWishList::insert([
        'user' => $user,
        'productUrl' => $productUrl
      ]);
    }
  }

  /**
   * @param Product $product
   * @return void
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   * @throws UserCookieIsWrong
   * @throws UserIsNotLoggedIn
   */
  public static function remove(Product $product): void
  {
    if (!Auth::isLogged()) {
      $products = [];
      foreach (self::getCookieProducts() as $rawProduct) {
        if ($rawProduct !== $product->url) {
          $products[] = $rawProduct;
        }
      }
      self::setCookieProducts($products);

      return;
    }

    UserProductWishList::remove([
      'user' => Auth::getUser(),
      'productUrl' => $product->url
    ]);
  }

  /**
   * @param Product $product
   * @return bool
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   * @throws UserCookieIsWrong
   * @throws UserIsNotLoggedIn
   */
  public static function isIn(Product $product): bool
  {
    if (!Auth::isLogged()) {
      return in_array($product->url, self::getCookieProducts(), true);
    }

    return !!UserProductWishList::quantity([
      'user' => Auth::getUser(),
      'productUrl' => $product->url
    ]);
  }

  /**
   * @return int
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   * @throws UserCookieIsWrong
   * @throws UserIsNotLoggedIn
   */
  public static function count(): int
  {
    if (!Auth::isLogged()) {
      return count(self::getCookieProducts());
    }

    return UserProductWishList::quantity([
      'user' => Auth::getUser(),
    ]);
  }
}