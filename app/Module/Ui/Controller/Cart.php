<?php

declare(strict_types=1);

namespace App\Module\Ui\Controller;

use Air\Cache;
use Air\Core\Exception\ClassWasNotFound;
use Air\Core\Exception\DomainMustBeProvided;
use Air\Http\Request;
use Air\Model\Exception\CallUndefinedMethod;
use Air\Model\Exception\ConfigWasNotProvided;
use Air\Model\Exception\DriverClassDoesNotExists;
use Air\Model\Exception\DriverClassDoesNotExtendsFromDriverAbstract;
use Air\Model\Meta\Exception\CollectionCantBeWithoutPrimary;
use Air\Model\Meta\Exception\CollectionCantBeWithoutProperties;
use Air\Model\Meta\Exception\CollectionNameDoesNotExists;
use Air\Model\Meta\Exception\PropertyIsSetIncorrectly;
use App\Helper\Checkout;
use App\Helper\Route;
use App\Model\Coupon;
use App\Model\Order;
use App\Model\Product;
use App\Model\ShopSettings;
use Exception;
use ReflectionException;
use Throwable;

class Cart extends Base
{
  /**
   * @return array
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   * @throws ReflectionException
   * @throws Throwable
   */
  public function preview(): array
  {
    $items = \App\Helper\Cart::items();
    $count = \App\Helper\Cart::count();
    $price = \App\Helper\Cart::price();
    $coupon = \App\Helper\Cart::getCoupon();
    $priceWithCoupon = \App\Helper\Cart::priceWithSale();

    return [
      'count' => $count,
      'preview' => $this->getView()->render('cart/preview', [
        'count' => $count,
        'items' => $items,
        'price' => $price,
        'coupon' => $coupon,
        'priceWithCoupon' => $priceWithCoupon,
      ]),
    ];
  }

  /**
   * @param Product $product
   * @param int $count
   * @return void
   * @throws ClassWasNotFound
   */
  public function add(Product $product, int $count = 1): void
  {
    $this->getView()->setAutoRender(false);
    $this->getView()->setLayoutEnabled(false);

    \App\Helper\Cart::add($product, $count);
  }

  /**
   * @param Product $product
   * @param string $sign
   * @return array
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   * @throws ReflectionException
   * @throws Throwable
   */
  public function minusPlus(Product $product, string $sign): array
  {
    if ($sign === 'minus') {
      \App\Helper\Cart::minus($product);

    } elseif ($sign === 'plus') {
      \App\Helper\Cart::plus($product);
    }

    return [
      'info' => $this->getView()->render('cart/partial/info'),
      'line' => $this->getView()->render('cart/cart/line', [
        'item' => \App\Helper\Cart::lineItem($product)
      ])
    ];
  }

  /**
   * @param Product $product
   * @return array
   * @throws ClassWasNotFound
   */
  public function remove(Product $product): array
  {
    $this->getView()->setAutoRender(false);
    $this->getView()->setLayoutEnabled(false);

    \App\Helper\Cart::remove($product);

    return [
      'count' => \App\Helper\Cart::count(),
      'info' => $this->getView()->render('cart/partial/info')
    ];
  }

  /**
   * @param Coupon $coupon | code
   * @return void
   * @throws Exception
   */
  public function coupon(Coupon $coupon): void
  {
    $this->getView()->setLayoutEnabled(false);
    $this->getView()->setAutoRender(false);

    \App\Helper\Cart::setCoupon($coupon);
  }

  /**
   * @return void
   * @throws ClassWasNotFound
   */
  public function removeCoupon(): void
  {
    $this->getView()->setLayoutEnabled(false);
    $this->getView()->setAutoRender(false);

    \App\Helper\Cart::removeCoupon();
  }

  /**
   * @param string $city
   * @return array
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   */
  public function warehouses(string $city): array
  {
    return Cache::fast(['new-post-warehouses-city', $city], function () use ($city) {
      $newPostKey = ShopSettings::one()->newPostKey;
      $client = Request::postJson('https://api.novaposhta.ua/v2.0/json/', [
        'apiKey' => $newPostKey,
        'modelName' => 'AddressGeneral',
        'calledMethod' => 'getWarehouses',
        'methodProperties' => [
          'CityName' => $city
        ]
      ]);
      $warehouses = [];
      foreach ($client->body['data'] as $warehouse) {
        // if ($warehouse['TypeOfWarehouse'] === '9a68df70-0267-42a8-bb5c-37f427e36ee4') {}
        $warehouses[] = $warehouse['Description'];
      }
      return $warehouses;
    });
  }

  /**
   * @return void
   * @throws ClassWasNotFound
   */
  public function saveOrderData(): void
  {
    $this->getView()->setLayoutEnabled(false);
    $this->getView()->setAutoRender(false);

    \App\Helper\Cart::setCheckoutData($this->getParams());
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
  public function checkoutCallback(Order $order): void
  {
    $this->getView()->setLayoutEnabled(false);
    $this->getView()->setAutoRender(false);

    Checkout::update($order);
  }

  /**
   * @return void
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   * @throws Exception
   */
  public function index(): void
  {
    $this->getView()->setMeta(ShopSettings::singleOne()->cartMeta);

    $count = \App\Helper\Cart::count();

    if (!$count) {
      $this->getView()->setScript('cart/partial/empty');
      return;
    }

    $items = \App\Helper\Cart::items();
    $price = \App\Helper\Cart::price();
    $coupon = \App\Helper\Cart::getCoupon();
    $priceWithCoupon = \App\Helper\Cart::priceWithSale();

    $this->getView()->setVars([
      'count' => $count,
      'items' => $items,
      'price' => $price,
      'coupon' => $coupon,
      'priceWithCoupon' => $priceWithCoupon,
    ]);
  }

  /**
   * @return array|void
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
   * @throws DomainMustBeProvided
   * @throws Exception
   */
  public function checkout()
  {
    if (!\App\Helper\Cart::count()) {
      $this->getView()->setScript('cart/partial/empty');
      return;
    }

    if ($this->getRequest()->isPost()) {
      $form = new \App\Module\Ui\Form\Order();

      if ($form->isValid($this->getParams())) {
        $order = Checkout::create($form->getValues());

        \App\Helper\Cart::clearProduct();

//        if ($order->paymentMethod === Order::PAYMENT_ONLINE) {
//          return Checkout::onlineCheckout($order);
//        }

        return ['pageUrl' => Route::checkoutThankYou($order, true)];
      }
      $this->getResponse()->setStatusCode(400);
      return $form->getErrors();
    }

    $this->getView()->setMeta(ShopSettings::singleOne()->checkoutMeta);
  }

  /**
   * @param Order $order
   * @return void
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   * @throws Exception
   */
  public function checkoutThankYou(Order $order): void
  {
    $this->getView()->setMeta(ShopSettings::singleOne()->thankYouMeta);
    $this->getView()->assign('order', $order);

    Checkout::update($order);
  }
}