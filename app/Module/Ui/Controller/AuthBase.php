<?php

declare(strict_types=1);

namespace App\Module\Ui\Controller;

use Air\Core\Exception\ClassWasNotFound;
use Air\Core\Exception\DomainMustBeProvided;
use Air\Model\Exception\CallUndefinedMethod;
use Air\Model\Exception\ConfigWasNotProvided;
use Air\Model\Exception\DriverClassDoesNotExists;
use Air\Model\Exception\DriverClassDoesNotExtendsFromDriverAbstract;
use App\Helper\Route;

abstract class AuthBase extends Base
{
  /**
   * @return void
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DomainMustBeProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   */
  public function init(): void
  {
    parent::init();

    if (!\App\Service\User\Auth::isLogged()) {
      $this->redirect(Route::auth());
    }
  }
}