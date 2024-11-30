<?php

declare(strict_types=1);

namespace App\Helper;

use Air\Core\Exception\ClassWasNotFound;
use Air\Http\Request;
use Air\Map;
use Air\Model\Exception\CallUndefinedMethod;
use Air\Model\Exception\ConfigWasNotProvided;
use Air\Model\Exception\DriverClassDoesNotExists;
use Air\Model\Exception\DriverClassDoesNotExtendsFromDriverAbstract;
use Air\Model\Meta\Exception\CollectionCantBeWithoutPrimary;
use Air\Model\Meta\Exception\CollectionCantBeWithoutProperties;
use Air\Model\Meta\Exception\CollectionNameDoesNotExists;
use Air\Model\Meta\Exception\PropertyIsSetIncorrectly;
use App\Model\Order;
use App\Model\ShopSettings;
use Exception;
use ReflectionException;

class Checkout
{
  /**
   * @param array $data
   * @return Order
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
  public static function create(array $data): Order
  {
    $order = new Order($data);

    $order->createAt = time();
    $order->number = (Order::one([], ['number' => -1])->number ?? 0) + 1;

    $order->coupon = Cart::getCoupon();
    $order->priceWithoutDiscount = Cart::price();
    $order->price = Cart::priceWithSale();
    $order->count = Cart::count();

    $order->products = Cart::items();

    $order->status = Order::PAYMENT_STATUS_SUCCESS;

    // TODO: Online payments
//    if ($order->paymentMethod === Order::PAYMENT_ONLINE) {
//      $order->status = Order::PAYMENT_STATUS_IN_PROGRESS;
//    } else {
//      $order->status = Order::PAYMENT_STATUS_SUCCESS;
//    }

    $order->save();
    return $order;
  }

  /**
   * @param Order $order
   * @return array
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   * @throws Exception
   */
  public static function onlineCheckout(Order $order): array
  {
    $monoUrl = 'https://api.monobank.ua/api/merchant/invoice/create';
    $monoKey = ShopSettings::one()->monoKey;

    $data = [
      "amount" => $order->price * 100,
      "redirectUrl" => Route::checkoutThankYou($order),
      "webHookUrl" => Route::checkoutCallback($order),
    ];

    $mono = (new Request())
      ->headers(['X-Token' => $monoKey])
      ->type('json')
      ->method(Request::POST)
      ->url($monoUrl)
      ->body($data)
      ->do();

    $order->acquiring = ['checkout' => $mono->body];
    $order->save();

    return $mono->body;
  }

  /**
   * @param Order $order
   * @return array
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   * @throws Exception
   */
  public static function info(Order $order): array
  {
    $monoKey = ShopSettings::one()->monoKey;

    $results = (new Request())
      ->headers(['X-Token' => $monoKey])
      ->url('https://api.monobank.ua/api/merchant/invoice/status')
      ->get(['invoiceId' => $order->acquiring['checkout']['invoiceId']])
      ->do();

    if (!$results->isOk()) {
      throw new Exception('Error getting invoice status by invoiceId . ' . $order->acquiring['checkout']['invoiceId']);
    }

    return $results->body;
  }

  /**
   * @param Order $order
   * @return void
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   */
  public static function update(Order $order): void
  {
    if ($order->paymentMethod === Order::PAYMENT_ONLINE) {

      $data = self::info($order);

      $acquiring = $order->acquiring;
      $acquiring['info'] = $data;

      $order->acquiring = $acquiring;
      $order->status = $data['status'];

      $order->save();
    }
  }
}