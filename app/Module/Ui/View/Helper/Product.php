<?php

declare(strict_types=1);

namespace App\Module\Ui\View\Helper;

use Air\Core\Exception\ClassWasNotFound;
use Air\Core\Exception\DomainMustBeProvided;
use Air\Crud\Model\Phrase;
use Air\Model\Exception\CallUndefinedMethod;
use Air\Model\Exception\ConfigWasNotProvided;
use Air\Model\Exception\DriverClassDoesNotExists;
use Air\Model\Exception\DriverClassDoesNotExtendsFromDriverAbstract;
use Air\Model\Meta\Exception\CollectionCantBeWithoutPrimary;
use Air\Model\Meta\Exception\CollectionCantBeWithoutProperties;
use Air\Model\Meta\Exception\CollectionNameDoesNotExists;
use Air\Model\Meta\Exception\PropertyIsSetIncorrectly;
use Air\Type\FaIcon;
use App\Helper\Route;
use ReflectionException;
use Throwable;

class Product
{
  private static function quickViewButton(\App\Model\Product $product): string
  {
    return div(
      class: 'p-a z-1 b-m10 r-0 br-circle sc-primary o-70 o-100-hover an-2 d-f ai-c jc-c w-26 h-26 cp',
      data: ['popup-href' => Route::productQuickView($product), 'title' => Phrase::t('Швидкий перегяд?')],
      content: faIcon('magnifying-glass', FaIcon::STYLE_LIGHT, class: 'fs-12')
    );
  }

  private static function cartRemoveButton(\App\Model\Product $product): string
  {
    return div(
      class: 'p-a z-1 t-m10 l-0 br-circle sc-danger o-70 o-100-hover an-2 d-f ai-c jc-c w-26 h-26 cp',
      data: ['cart-page-remove' => $product->url, 'title' => Phrase::t('Видалити з кошика?')],
      content: faIcon('xmark', FaIcon::STYLE_LIGHT, class: 'fs-12')
    );
  }

  private static function priceWithSale(\App\Model\Product $product): string
  {
    return div(class: 'mt-5', content: [
      span(class: 'fw-bold fs-14', content: Common::price($product->price)),
      $product->oldPrice ? span(class: 'ml-10 o-70 td-lt fs-12', content: Common::price($product->oldPrice)) : null,
    ]);
  }

  public static function priceWithCount(\App\Model\Product $product, int $count): string
  {
    return flex(class: 'fs-12 gp-6 fw-3', content: [
      div(class: 'fw-bold', content: Common::price($product->price)),
      'x',
      div(class: 'fw-bold', content: (string)$count),
      '=',
      div(class: 'fw-bold', content: Common::price($product->price * $count)),
    ]);
  }

  public static function small(
    \App\Model\Product $product,
    int                $count = 0,
    bool               $quickView = true,
    bool               $cartRemove = true,
  ): string
  {
    return flex(
      class: 'ai-s',
      content: [
        div(
          class: 'p-r',
          content: [
            img($product->getImage(), class: 'ar-1-1 p-10 bg-white br-3 sd-soft of-con w-70 p-r of-h d-b'),
            $cartRemove ? self::cartRemoveButton($product) : null,
            $quickView ? self::quickViewButton($product) : null,
          ]),
        flex(
          class: 'px-12 f-c gt-10',
          content: [
            a(href: Route::product($product), class: 'fw-bold lm-2', content: $product->title),
            a(Route::catalog($product->category), $product->category->title, 'c-secondary c-secondary-hover fw-bold lm-1'),
            $count ? self::priceWithCount($product, $count) : self::priceWithSale($product)
          ])
      ]);
  }

  /**
   * @param \App\Model\Product $product
   * @return string
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DomainMustBeProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   * @throws CollectionCantBeWithoutPrimary
   * @throws CollectionCantBeWithoutProperties
   * @throws CollectionNameDoesNotExists
   * @throws PropertyIsSetIncorrectly
   * @throws ReflectionException
   * @throws Throwable
   */
  public static function compare(\App\Model\Product $product): string
  {
    return flex(
      class: 'ai-s',
      content: [
        div(
          class: 'p-r',
          content: [
            img($product->getImage(), class: 'ar-1-1 p-10 bg-white br-3 sd-soft of-con w-70 p-r of-h d-b'),
            self::quickViewButton($product),
            div(
              class: 'p-a z-1 t-m10 l-0 br-circle sc-danger o-70 o-100-hover an-2 d-f ai-c jc-c w-26 h-26 cp',
              data: ['compare-remove' => $product->url, 'title' => Phrase::t('Прибрати з порівняння')],
              content: faIcon('xmark', FaIcon::STYLE_LIGHT, class: 'fs-12')
            ),
          ]),
        flex(
          class: 'px-12 f-c gt-10',
          content: [
            a(href: Route::product($product), class: 'fw-bold lm-2', content: $product->title),
            a(Route::catalog($product->category), $product->category->title, 'c-secondary c-secondary-hover fw-bold lm-1'),
            self::priceWithSale($product)
          ])
      ]);
  }
}