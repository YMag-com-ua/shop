<?php

declare(strict_types=1);

namespace App\Helper;

use Air\Core\Exception\ClassWasNotFound;
use Air\Core\Exception\DomainMustBeProvided;
use Air\Core\Front;
use Air\Crud\Model\Language;
use Air\Model\Exception\CallUndefinedMethod;
use Air\Model\Exception\ConfigWasNotProvided;
use Air\Model\Exception\DriverClassDoesNotExists;
use Air\Model\Exception\DriverClassDoesNotExtendsFromDriverAbstract;
use App\Model\Article;
use App\Model\ArticleCategory;
use App\Model\ArticleTag;
use App\Model\Category;
use App\Model\Order;
use App\Model\Product;

class Route
{
  /**
   * @param array $route
   * @param array $params
   * @param bool $onlyUri
   * @return string
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DomainMustBeProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   */
  public static function assemble(array $route = [], array $params = [], bool $onlyUri = false): string
  {
    if (!isset($params['language'])) {
      $language = Language::getLanguage();
      if (!$language->isDefault) {
        $params['language'] = $language->key;
      }
    }

    $url = trim(Front::getInstance()->getRouter()->assemble($route, $params, true, $onlyUri));

    if (str_ends_with($url, '?')) {
      $url = substr($url, 0, strlen($url) - 1);
    }
    return $url;
  }

  /**
   * @return array
   * @throws ClassWasNotFound
   */
  public static function currentRoute(): array
  {
    return [
      'route' => [
        'controller' => Front::getInstance()->getRouter()->getController(),
        'action' => Front::getInstance()->getRouter()->getAction(),
      ],
      'params' => [
        ...Front::getInstance()->getRouter()->getUrlParams(),
        ...Front::getInstance()->getRouter()->getRequest()->getParams()
      ]
    ];
  }

  /**
   * @param Language|null $language
   * @return string
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   */
  public static function getLanguageUrlPrefix(?Language $language = null): string
  {
    if (!$language) {
      $language = Language::getLanguage();
    }
    if ($language->isDefault) {
      return '';
    }
    return '/' . $language->key;
  }

  /**
   * @param Language $language
   * @return string
   * @throws ClassWasNotFound
   * @throws DomainMustBeProvided
   */
  public static function currentUrlToLanguage(Language $language): string
  {
    $route = self::currentRoute();

    if ($language->isDefault) {
      unset($route['params']['language']);
    } else {
      $route['params']['language'] = $language->key;
    }

    return Front::getInstance()->getRouter()->assemble(
      $route['route'],
      $route['params'],
      true
    );
  }

  /**
   * @return string
   * @throws ClassWasNotFound
   * @throws DomainMustBeProvided
   */
  public static function currentUrl(): string
  {
    $route = self::currentRoute();

    return Front::getInstance()->getRouter()->assemble(
      $route['route'],
      $route['params'],
      true
    );
  }

  /**
   * @return string
   * @throws ClassWasNotFound
   * @throws DomainMustBeProvided
   */
  public static function currentRouteId(): string
  {
    return md5(self::currentUrl());
  }

  /**
   * @param string $phone
   * @return string
   */
  public static function tel(string $phone): string
  {
    return "tel:" . $phone;
  }

  /**
   * @param string $email
   * @return string
   */
  public static function email(string $email): string
  {
    return "tel:" . $email;
  }

  /**
   * @return string
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DomainMustBeProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   */
  public static function home(): string
  {
    return self::assemble();
  }

  /**
   * @return string
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DomainMustBeProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   */
  public static function tariff(): string
  {
    return self::assemble(['action' => 'tariff']);
  }

  /**
   * @return string
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DomainMustBeProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   */
  public static function asyncCategories(): string
  {
    return self::assemble(
      ['controller' => 'async', 'action' => 'categories'],
    );
  }

  /**
   * @return string
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DomainMustBeProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   */
  public static function asyncMobileMenu(): string
  {
    return self::assemble(
      ['controller' => 'async', 'action' => 'mobileMenu'],
    );
  }

  /**
   * @param Category|null $category
   * @param string|null $search
   * @param array $brands
   * @param array $filters
   * @param array $countries
   * @param bool $isNew
   * @param bool $isSale
   * @param int $page
   * @return string
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DomainMustBeProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   */
  public static function catalog(
    ?Category $category = null,
    ?string   $search = null,
    array     $brands = [],
    array     $filters = [],
    array     $countries = [],
    bool      $isNew = false,
    bool      $isSale = false,
    int       $page = 1
  ): string
  {
    $parts = [];
    if ($category) {
      $parts['category'] = $category->url;
    }

    if ($search) {
      $parts['search'] = $search;
    }

    if (count($filters)) {
      $parts['filters'] = $filters;
    }

    if (count($brands)) {
      $parts['brands'] = [];
      foreach ($brands as $brand) {
        $parts['brands'][] = $brand->url;
      }
    }

    if (count($countries)) {
      $parts['countries'] = [];
      foreach ($countries as $country) {
        $parts['countries'][] = $country->url;
      }
    }

    if ($isNew) {
      $parts['new'] = 1;
    }

    if ($isSale) {
      $parts['sale'] = 1;
    }

    if ($page > 1) {
      $parts['page'] = $page;
    }

    return self::assemble(['controller' => 'catalog'], $parts);
  }

  /**
   * @param Product $product
   * @return string
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DomainMustBeProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   */
  public static function product(Product $product): string
  {
    return self::assemble(
      ['controller' => 'product'],
      ['product' => $product->url]
    );
  }

  /**
   * @param Product $product
   * @return string
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DomainMustBeProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   */
  public static function productQuickView(Product $product): string
  {
    return self::assemble(
      ['controller' => 'product', 'action' => 'quickView'],
      ['product' => $product->url]
    );
  }

  /**
   * @return string
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DomainMustBeProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   */
  public static function cart(): string
  {
    return self::assemble(['controller' => 'cart']);
  }

  /**
   * @return string
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DomainMustBeProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   */
  public static function checkout(): string
  {
    return self::assemble(['controller' => 'cart', 'action' => 'checkout']);
  }

  /**
   * @param Order $order
   * @return string
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DomainMustBeProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   */
  public static function checkoutCallback(Order $order): string
  {
    return self::assemble(
      ['controller' => 'cart', 'action' => 'checkoutCallback'],
      ['order' => $order->id]
    );
  }

  /**
   * @param Order $order
   * @param bool $onlyUri
   * @return string
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DomainMustBeProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   */
  public static function checkoutThankYou(Order $order, bool $onlyUri = false): string
  {
    return self::assemble(
      ['controller' => 'cart', 'action' => 'checkoutThankYou'],
      ['order' => $order->id],
      $onlyUri
    );
  }

  /**
   * @param ArticleCategory|null $category
   * @param ArticleTag|null $tag
   * @param int $page
   * @return string
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DomainMustBeProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   */
  public static function blog(
    ?ArticleCategory $category = null,
    ?ArticleTag      $tag = null,
    int              $page = 1
  ): string
  {
    $params = [];

    if ($category) {
      $params['category'] = $category->url;
    }

    if ($tag) {
      $params['tag'] = $tag->url;
    }

    if ($page > 1) {
      $params['page'] = $page;
    }

    return self::assemble(['controller' => 'blog'], $params);
  }

  /**
   * @param Article $article
   * @return string
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DomainMustBeProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   */
  public static function article(Article $article): string
  {
    return self::assemble(
      ['controller' => 'blog', 'action' => 'article'],
      ['article' => $article->url]
    );
  }

  /**
   * @return string
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DomainMustBeProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   */
  public static function paymentDelivery(): string
  {
    return self::assemble(['action' => 'paymentDelivery']);
  }

  /**
   * @return string
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DomainMustBeProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   */
  public static function about(): string
  {
    return self::assemble(['action' => 'about']);
  }

  /**
   * @return string
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DomainMustBeProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   */
  public static function contact(): string
  {
    return self::assemble(['action' => 'contact']);
  }
}