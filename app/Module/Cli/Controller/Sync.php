<?php

declare(strict_types=1);

namespace App\Module\Cli\Controller;

use Air\Core\Controller;
use Air\Core\Exception\ClassWasNotFound;
use Air\Model\Exception\CallUndefinedMethod;
use Air\Model\Exception\ConfigWasNotProvided;
use Air\Model\Exception\DriverClassDoesNotExists;
use Air\Model\Exception\DriverClassDoesNotExtendsFromDriverAbstract;
use Air\Model\Meta\Exception\CollectionCantBeWithoutPrimary;
use Air\Model\Meta\Exception\CollectionCantBeWithoutProperties;
use Air\Model\Meta\Exception\CollectionNameDoesNotExists;
use Air\Model\Meta\Exception\PropertyIsSetIncorrectly;
use Closure;
use ReflectionException;
use Throwable;

class Sync extends Controller
{
  /**
   * @return void
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
   * @throws Throwable
   */
  public function all(): void
  {
    \App\Service\Sync\Sync::setLogger(self::getLogger());
    \App\Service\Sync\Sync::syncAll();
  }

  /**
   * @return void
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
   * @throws Throwable
   */
  public function prices(): void
  {
    \App\Service\Sync\Sync::setLogger(self::getLogger());
    \App\Service\Sync\Sync::syncPrices();
  }

  /**
   * @return Closure
   */
  private function getLogger(): Closure
  {
    return function (mixed $title, array $data = []) {
      echo "-------------------\n";

      if (is_string($title)) {
        echo $title;
      } else {
        echo implode(' / ', $title);
      }

      if (count($data)) {
        echo "\n" . implode(' | ', $data);
      }
      echo "\n";
    };
  }
}