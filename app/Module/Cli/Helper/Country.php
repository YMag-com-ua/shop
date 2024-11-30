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

class Country
{
  /**
   * @param string $title
   * @return \App\Model\Country|null
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
  public static function create(string $title): ?\App\Model\Country
  {
    $title = Str::normalize($title);

    if (!mb_strlen($title)) {
      return null;
    }

    $country = \App\Model\Country::fetchOne([
      'title' => $title
    ]);

    if (!$country) {
      $country = new \App\Model\Country([
        'title' => $title,
        'url' => Str::slug($title),
        'enabled' => true,
      ]);

      $country->save();
    }

    return $country;
  }

  /**
   * @return \App\Model\Country
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   */
  public static function getDefaultCountry(): \App\Model\Country
  {
    return \App\Model\Country::singleOne([
      'id' => '67475330d91f238c6d06b55e'
    ]);
  }
}