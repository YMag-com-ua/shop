<?php

declare(strict_types=1);

namespace App\Module\Ui\Controller;

use Air\Core\Exception\ClassWasNotFound;
use Air\Core\Exception\DomainMustBeProvided;
use Air\Core\Front;
use Air\Crud\Model\Language;
use Air\Form\Exception\FilterClassWasNotFound;
use Air\Form\Exception\ValidatorClassWasNotFound;
use Air\Model\Exception\CallUndefinedMethod;
use Air\Model\Exception\ConfigWasNotProvided;
use Air\Model\Exception\DriverClassDoesNotExists;
use Air\Model\Exception\DriverClassDoesNotExtendsFromDriverAbstract;
use Air\Model\Meta\Exception\CollectionCantBeWithoutPrimary;
use Air\Model\Meta\Exception\CollectionCantBeWithoutProperties;
use Air\Model\Meta\Exception\CollectionNameDoesNotExists;
use Air\Model\Meta\Exception\PropertyIsSetIncorrectly;
use Air\ThirdParty\GoogleOAuth\Exception\InvalidCode;
use Air\ThirdParty\GoogleOAuth\Exception\UnableToGetUserByAccessToken;
use App\Helper\Factory;
use App\Helper\Route;
use App\Module\Ui\Form\ForgotPassword;
use App\Module\Ui\Form\Register;
use App\Service\User\Exception\InvalidEmailOrPhone;
use App\Service\User\Exception\InvalidPasswordRecoveryCode;
use PHPMailer\PHPMailer\Exception;
use ReflectionException;
use Throwable;

class Auth extends Base
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
    if (\App\Service\User\Auth::isLogged()) {
      $this->redirect(Route::profile());
    }
  }

  /**
   * @return void
   */
  public function index(): void
  {
    if ($this->getRequest()->isPost()) {
      $this->getView()->setLayoutEnabled(false);
      $this->getView()->setAutoRender(false);

      $form = new \App\Module\Ui\Form\Auth();

      try {
        if (!$form->isValid($this->getParams())) {
          throw new \Exception();
        }
        \App\Service\User\Auth::auth($form->getValues());

      } catch (Throwable) {
        $this->getResponse()->setStatusCode(400);
      }
    }
  }

  /**
   * @return array|void
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws CollectionCantBeWithoutPrimary
   * @throws CollectionCantBeWithoutProperties
   * @throws CollectionNameDoesNotExists
   * @throws ConfigWasNotProvided
   * @throws DomainMustBeProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   * @throws FilterClassWasNotFound
   * @throws PropertyIsSetIncorrectly
   * @throws ReflectionException
   * @throws ValidatorClassWasNotFound
   * @throws Exception
   */
  public function register()
  {
    if ($this->getRequest()->isPost()) {
      $this->getView()->setLayoutEnabled(false);
      $this->getView()->setAutoRender(false);

      $form = new Register();

      if ($form->isValid($this->getParams())) {
        \App\Service\User\Auth::register($form->getValues(), Language::getLanguage());
        return;
      }

      $this->getResponse()->setStatusCode(400);
      return $form->getErrorFields();
    }
  }

  /**
   * @param string $code
   * @return void
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   * @throws \Exception
   */
  public function confirm(string $code): void
  {
    if (!\App\Service\User\Auth::confirm($code)) {
      throw new \Exception('Confirmation code is invalid');
    }
  }

  /**
   * @param string $code
   * @return void
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws CollectionCantBeWithoutPrimary
   * @throws CollectionCantBeWithoutProperties
   * @throws CollectionNameDoesNotExists
   * @throws ConfigWasNotProvided
   * @throws DomainMustBeProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   * @throws InvalidCode
   * @throws PropertyIsSetIncorrectly
   * @throws ReflectionException
   * @throws UnableToGetUserByAccessToken
   */
  public function google(string $code): void
  {
    \App\Service\User\Auth::google($code, Language::getLanguage());
    $this->redirect(Route::profile());
  }

  /**
   * @return void
   */
  public function forgotPassword(): void
  {
    if ($this->getRequest()->isPost()) {
      $this->getView()->setLayoutEnabled(false);
      $this->getView()->setAutoRender(false);

      $form = new ForgotPassword();

      try {
        if (!$form->isValid($this->getParams())) {
          throw new \Exception();
        }
        \App\Service\User\Auth::generateNewPassword($form->getValues()['login']);

      } catch (Throwable) {
        $this->getResponse()->setStatusCode(400);
      }
    }
  }
}