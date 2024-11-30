<?php

declare(strict_types=1);

namespace App\Module\Cli\Helper;

use Air\Core\Exception\ClassWasNotFound;
use Air\Model\Exception\CallUndefinedMethod;
use Air\Model\Exception\ConfigWasNotProvided;
use Air\Model\Exception\DriverClassDoesNotExists;
use Air\Model\Exception\DriverClassDoesNotExtendsFromDriverAbstract;
use Air\Model\Meta\Exception\CollectionCantBeWithoutPrimary;
use Air\Model\Meta\Exception\CollectionCantBeWithoutProperties;
use Air\Model\Meta\Exception\CollectionNameDoesNotExists;
use Air\Model\Meta\Exception\PropertyIsSetIncorrectly;
use ReflectionException;

class Brand
{
  /**
   * @param string $title
   * @return \App\Model\Brand|null
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
  public static function create(string $title): ?\App\Model\Brand
  {
    $title = Str::normalize($title);

    if (!mb_strlen($title)) {
      return null;
    }

    $brand = \App\Model\Brand::fetchOne([
      'title' => $title
    ]);

    if (!$brand) {
      $brand = new \App\Model\Brand([
        'title' => $title,
        'url' => Str::slug($title),
        'enabled' => true,
      ]);

      $brand->save();
    }

    return $brand;
  }

  /**
   * @return \App\Model\Brand
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   */
  public static function getDefaultBrand(): \App\Model\Brand
  {
    return \App\Model\Brand::fetchOne([
      'id' => '674752fed83eb2ba2b004724'
    ]);
  }
}