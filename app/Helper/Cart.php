<?php

declare(strict_types=1);

namespace App\Helper;

use Air\Cookie;
use Air\Core\Exception\ClassWasNotFound;
use Air\Model\Exception\CallUndefinedMethod;
use Air\Model\Exception\ConfigWasNotProvided;
use Air\Model\Exception\DriverClassDoesNotExists;
use Air\Model\Exception\DriverClassDoesNotExtendsFromDriverAbstract;
use App\Model\Coupon;
use App\Model\DiscountCartBySum;
use App\Model\Product;
use App\Type\CartItem;
use App\Type\Checkout\Data;
use Exception;
use ReflectionException;
use Throwable;

class Cart
{
  /**
   * @var array|null
   */
  private static ?array $items = null;

  /**
   * @return array
   * @throws ClassWasNotFound
   */
  public static function getRawItems(): array
  {
    return (Cookie::get('cart-items') ?? []);
  }

  /**
   * @param array $items
   * @return void
   * @throws ClassWasNotFound
   */
  public static function setRawItems(array $items): void
  {
    self::$items = null;
    Cookie::set('cart-items', $items);
  }

  /**
   * @return array|CartItem[]
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   * @throws ReflectionException
   * @throws Throwable
   */
  public static function items(): array
  {
    if (self::$items) {
      return self::$items;
    }

    $rawItems = self::getRawItems();
    $items = [];
    $productUrls = [];

    foreach ($rawItems as $rawItem) {
      $productUrls[] = $rawItem['product'];
    }

    $products = [];
    foreach (Product::singleAll(['url' => ['$in' => $productUrls]], [], count($rawItems)) as $product) {
      $products[$product->url] = $product;
    }

    foreach ($rawItems as $item) {
      if (!empty($products[$item['product']])) {
        $items[] = new CartItem([
          'product' => $products[$item['product']],
          'count' => $item['count'],
        ]);
      }
    }
    self::$items = $items;
    return $items;
  }

  /**
   * @return float
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   * @throws ReflectionException
   * @throws Throwable
   */
  public static function price(): float
  {
    $price = 0;
    foreach (self::items() as $item) {
      $price = $price + ($item->getProduct()->price * $item->getCount());
    }
    return $price;
  }

  /**
   * @return float
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   * @throws ReflectionException
   * @throws Throwable
   */
  public static function priceWithSale(): float
  {
    $price = self::price();
    $coupon = self::getCoupon();

    if ($coupon) {
      if ($coupon->type === Coupon::TYPE_PERCENTAGE) {
        $price = $price - ($price / 100) * $coupon->value;
      }
      else if ($coupon->type === Coupon::TYPE_FIXED) {
        $price = $price - $coupon->value;
      }
    }

    $discountBySum = self::getAppliedDiscountCartBySum();

    if ($discountBySum) {
      if ($coupon && !$discountBySum->allowedToBeUsedWithCoupons) {
        return $price;
      }

      if ($discountBySum->type === DiscountCartBySum::TYPE_PERCENTAGE) {
        $price = $price - ($price / 100) * $discountBySum->value;
      }
      else if ($discountBySum->type === DiscountCartBySum::TYPE_FIXED) {
        $price = $price - $discountBySum->value;
      }
    }

    return $price;
  }

  /**
   * @return int
   * @throws ClassWasNotFound
   */
  public static function count(): int
  {
    $count = 0;
    foreach (self::getRawItems() as $item) {
      $count = $count + $item['count'];
    }
    return $count;
  }

  /**
   * @param Product $product
   * @param int $count
   * @return void
   * @throws ClassWasNotFound
   */
  public static function add(Product $product, int $count = 1): void
  {
    $items = self::getRawItems();
    $added = false;

    foreach ($items as $index => $item) {
      if ($item['product'] === $product->url) {
        $added = true;
        $item['count'] = $item['count'] + $count;
        $items[$index] = $item;
        break;
      }
    }

    if (!$added) {
      $items[] = [
        'product' => $product->url,
        'count' => $count
      ];
    }

    self::setRawItems($items);
  }

  /**
   * @param Product $product
   * @return void
   * @throws ClassWasNotFound
   */
  public static function remove(Product $product): void
  {
    $items = self::getRawItems();
    foreach ($items as $index => $item) {
      if ($item['product'] === $product->url) {
        unset($items[$index]);
        self::setRawItems(array_values($items));
        return;
      }
    }
  }

  /**
   * @param Product $product
   * @return void
   * @throws ClassWasNotFound
   */
  public static function plus(Product $product): void
  {
    $items = self::getRawItems();
    foreach ($items as $index => $item) {
      if ($item['product'] === $product->url) {
        $item['count'] = $item['count'] + 1;
        $items[$index] = $item;
        self::setRawItems($items);
        return;
      }
    }
  }

  /**
   * @param Product $product
   * @return void
   * @throws ClassWasNotFound
   */
  public static function minus(Product $product): void
  {
    $items = self::getRawItems();
    foreach ($items as $index => $item) {
      if ($item['product'] === $product->url) {
        if ($item['count'] > 1) {
          $item['count'] = $item['count'] - 1;
          $items[$index] = $item;
          self::setRawItems($items);
        }
        return;
      }
    }
  }

  /**
   * @param Product $product
   * @return CartItem|null
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   * @throws ReflectionException
   * @throws Throwable
   */
  public static function lineItem(Product $product): CartItem|null
  {
    $items = self::getRawItems();

    foreach ($items as $item) {
      if ($item['product'] === $product->url) {
        return new CartItem([
          'product' => $product,
          'count' => $item['count'],
        ]);
      }
    }

    return null;
  }

  /**
   * @param Coupon $coupon
   * @return void
   * @throws Exception
   */
  public static function setCoupon(Coupon $coupon): void
  {
    if (!$coupon->canItBeUsed()) {
      throw new Exception('Coupon can not be used');
    }

    Cookie::set('cart-coupon', $coupon->id);
  }

  /**
   * @return Coupon|null
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   * @throws Exception
   */
  public static function getCoupon(): Coupon|null
  {
    $couponId = Cookie::get('cart-coupon');
    if (!$couponId) {
      return null;
    }

    $coupon = Coupon::one(['id' => $couponId]);

    if (!$coupon || !$coupon->canItBeUsed()) {
      self::removeCoupon();
      return null;
    }
    return $coupon;
  }

  /**
   * @return void
   * @throws ClassWasNotFound
   */
  public static function removeCoupon(): void
  {
    Cookie::remove('cart-coupon');
  }

  /**
   * @param array $data
   * @return void
   * @throws ClassWasNotFound
   */
  public static function setCheckoutData(array $data): void
  {
    Cookie::set('cart-checkout', $data);
  }

  /**
   * @return Data
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   * @throws ReflectionException
   * @throws Throwable
   */
  public static function getCheckoutData(): Data
  {
    return new Data(Cookie::get('cart-checkout') ?? []);
  }

  /**
   * @return void
   * @throws ClassWasNotFound
   */
  public static function clearProduct(): void
  {
    self::removeCoupon();
    self::setRawItems([]);
  }

  /**
   * @return DiscountCartBySum|null
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   * @throws ReflectionException
   * @throws Throwable
   */
  public static function getNearestDiscountCartBySum(): DiscountCartBySum|null
  {
    if (!self::count()) {
      return null;
    }

    $cond = ['minSum' => ['$gt' => self::price()]];
    if (self::getCoupon()) {
      $cond['allowedToBeUsedWithCoupons'] = true;
    }
    return DiscountCartBySum::one($cond, ['minSum' => 1]);
  }

  /**
   * @return DiscountCartBySum|null
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   * @throws ReflectionException
   * @throws Throwable
   */
  public static function getAppliedDiscountCartBySum(): DiscountCartBySum|null
  {
    if (!self::count()) {
      return null;
    }

    $cond = ['minSum' => ['$lte' => self::price()]];
    if (self::getCoupon()) {
      $cond['allowedToBeUsedWithCoupons'] = true;
    }
    return DiscountCartBySum::one($cond, ['minSum' => -1]);
  }
}