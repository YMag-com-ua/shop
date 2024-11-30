<?php

declare(strict_types=1);

namespace App\Module\Ui\View\Helper;

use Air\Core\Exception\ClassWasNotFound;
use Air\Model\Exception\CallUndefinedMethod;
use Air\Model\Exception\ConfigWasNotProvided;
use Air\Model\Exception\DriverClassDoesNotExists;
use Air\Model\Exception\DriverClassDoesNotExtendsFromDriverAbstract;
use Air\Type\FaIcon;
use ReflectionException;
use Throwable;

class Input
{
  public static function text(
    string       $name = null,
    string       $value = null,
    string       $label = null,
    bool         $required = true,
    string       $placeholder = null,
    bool         $hasError = false,
    string       $error = null,
    string|array $containerClass = [],
    string|array $labelClass = [],
    string|array $inputClass = [],
    string|array $errorClass = [],
    array        $containerAttributes = [],
    array        $labelAttributes = [],
    array        $inputAttributes = [],
    array        $errorAttributes = [],
    string       $description = null,
  ): string
  {
    return div(
      attributes: $containerAttributes,
      class: [
        'd-f f-c gp-5 form-input',
        ...(array)$containerClass,
        $hasError ? 'has-error' : null
      ],
      content: [
        $label ? label(
          content: $label,
          required: $required,
          attributes: $labelAttributes,
          class: $labelClass
        ) : null,
        $description ? div(
          class: 'fs-12 c-gray',
          content: $description
        ) : null,
        text(
          name: $name,
          value: $value,
          class: [
            'p-10 bw-2 bc-level-3-bg br-5 w-f bg-body-bg',
            ...(array)$inputClass
          ],
          placeholder: $placeholder,
          attributes: $inputAttributes
        ),
        $error ? div(
          class: ['c-danger fs-12 error', ...(array)$errorClass],
          content: $error,
          attributes: $errorAttributes
        ) : null
      ]
    );
  }

  public static function number(
    string       $name = null,
    string       $value = null,
    string       $label = null,
    bool         $required = true,
    string       $placeholder = null,
    bool         $hasError = false,
    string       $error = null,
    string|array $containerClass = [],
    string|array $labelClass = [],
    string|array $inputClass = [],
    string|array $errorClass = [],
    array        $containerAttributes = [],
    array        $labelAttributes = [],
    array        $inputAttributes = [],
    array        $errorAttributes = [],
  ): string
  {
    $inputAttributes['type'] = 'number';

    return self::text(
      $name,
      $value,
      $label,
      $required,
      $placeholder,
      $hasError,
      $error,
      $containerClass,
      $labelClass,
      $inputClass,
      $errorClass,
      $containerAttributes,
      $labelAttributes,
      $inputAttributes,
      $errorAttributes
    );
  }

  public static function password(
    string       $name = null,
    string       $value = null,
    string       $label = null,
    bool         $required = true,
    string       $placeholder = null,
    bool         $hasError = false,
    string       $error = null,
    string|array $containerClass = [],
    string|array $labelClass = [],
    string|array $inputClass = [],
    string|array $errorClass = [],
    array        $containerAttributes = [],
    array        $labelAttributes = [],
    array        $inputAttributes = [],
    array        $errorAttributes = [],
  ): string
  {
    $inputAttributes['type'] = 'password';

    return self::text(
      $name,
      $value,
      $label,
      $required,
      $placeholder,
      $hasError,
      $error,
      $containerClass,
      $labelClass,
      $inputClass,
      $errorClass,
      $containerAttributes,
      $labelAttributes,
      $inputAttributes,
      $errorAttributes
    );
  }

  public static function select(
    string       $name = null,
    string       $value = null,
    string       $label = null,
    bool         $required = true,
    array        $options = [],
    bool         $hasError = false,
    string       $error = null,
    string|array $containerClass = [],
    string|array $labelClass = [],
    string|array $inputClass = [],
    string|array $errorClass = [],
    array        $containerAttributes = [],
    array        $labelAttributes = [],
    array        $inputAttributes = [],
    array        $errorAttributes = [],
  ): string
  {
    return div(
      attributes: $containerAttributes,
      class: [
        'd-f f-c gp-5 form-input',
        ...(array)$containerClass,
        $hasError ? 'has-error' : null
      ],
      content: [
        $label ? label(
          content: $label,
          required: $required,
          attributes: $labelAttributes,
          class: $labelClass
        ) : null,
        select(
          name: $name,
          value: $value,
          options: $options,
          attributes: $inputAttributes,
          class: [
            'p-10 bw-2 bc-level-3-bg br-5 w-f bg-body-bg',
            ...(array)$inputClass
          ],
        ),
        $error ? div(
          class: ['c-danger fs-12 error', ...(array)$errorClass],
          content: $error,
          attributes: $errorAttributes
        ) : null
      ]
    );
  }

  /**
   * @param string $icon
   * @param string $iconStyle
   * @param string|array $iconClass
   * @param string $style
   * @param string|null $hover
   * @param string|null $title
   * @param string $titlePlacement
   * @param string|array $class
   * @param string|array $data
   * @param string|array $attributes
   * @return string
   * @throws ClassWasNotFound
   * @throws CallUndefinedMethod
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   * @throws ReflectionException
   * @throws Throwable
   */
  public static function pillIcon(
    string       $icon,
    string       $iconStyle = FaIcon::STYLE_REGULAR,
    string|array $iconClass = [],
    string       $style = 'secondary',
    string|null  $hover = null,
    string|null  $title = null,
    string       $titlePlacement = 'bottom',
    string|array $class = [],
    string|array $data = [],
    string|array $attributes = [],
  ): string
  {
    $class = (array)$class;
    $class[] = 'br-circle sd-10 w-36 h-36 fw-bold d-if ai-c jc-c cp';

    $class[] = 'sc-' . $style;

    if ($hover) {
      $class[] = 'sc-' . $hover . '-hover';
    }

    if ($title || $hover) {
      $class[] = 'cp';
    }

    if ($title) {
      $data = (array)$data;
      $data['title'] = $title;
      $data['title-placement'] = $titlePlacement;
    }

    return tag(
      tagName: 'div',
      class: $class,
      attributes: $attributes,
      data: $data,
      content: faIcon(
        icon: $icon,
        style: $iconStyle,
        class: array_merge(['fs-12'], (array)($iconClass ?? []))
      )
    );
  }

  public static function pillIconSwiperNav(string|array $class = []): string
  {
    return div(class: ['d-f gp-5', $class], content: [
      self::pillIcon(icon: 'chevron-left', iconStyle: FaIcon::STYLE_SOLID, data: 'swiper-nav-prev'),
      self::pillIcon('chevron-right', iconStyle: FaIcon::STYLE_SOLID, data: 'swiper-nav-next')
    ]);
  }

  public static function stickIconSwiperNav(string|array $class = []): string
  {
    return div(class: ['d-f gp-5', $class], content: [
      div(
        data: 'swiper-nav-prev',
        class: 'p-a z-1 t-0 l-0 h-f w-20 sc-secondary d-f ai-c jc-c cp o-70 o-80-hover an-2',
        content: faIcon('chevron-left', style: FaIcon::STYLE_SOLID, class: 'fs-10')
      ),
      div(
        data: 'swiper-nav-next',
        class: 'p-a z-1 t-0 r-0 h-f w-20 sc-secondary d-f ai-c jc-c cp o-70 o-80-hover an-2',
        content: faIcon('chevron-right', style: FaIcon::STYLE_SOLID, class: 'fs-10')
      )
    ]);
  }
}