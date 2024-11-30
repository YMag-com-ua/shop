<?php

declare(strict_types=1);

namespace App\Service;

use Air\Cookie;
use Air\Core\Exception\ClassWasNotFound;
use Air\Model\Exception\CallUndefinedMethod;
use Air\Model\Exception\ConfigWasNotProvided;
use Air\Model\Exception\DriverClassDoesNotExists;
use Air\Model\Exception\DriverClassDoesNotExtendsFromDriverAbstract;
use App\Model\Product;
use App\Model\UserProductViews;
use App\Service\User\Auth;
use App\Service\User\Exception\UserCookieIsWrong;
use App\Service\User\Exception\UserIsNotLoggedIn;

class Viewed
{
  /**
   * @return array
   * @throws ClassWasNotFound
   */
  private static function getCookieProducts(): array
  {
    return Cookie::get('viewed') ?? [];
  }

  /**
   * @param array $productUrls
   * @return void
   * @throws ClassWasNotFound
   */
  private static function setCookieProducts(array $productUrls): void
  {
    Cookie::set('viewed', $productUrls);
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
      foreach (UserProductViews::all(['user' => Auth::getUser()], ['createdAt' => -1], $count, $offset) as $item) {
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

    if (!UserProductViews::quantity(['user' => $user, 'productUrl' => $productUrl])) {
      UserProductViews::insert([
        'user' => $user,
        'productUrl' => $productUrl
      ]);
    }
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

    return UserProductViews::quantity([
      'user' => Auth::getUser()
    ]);
  }
}