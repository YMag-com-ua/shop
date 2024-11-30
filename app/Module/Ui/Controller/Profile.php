<?php

declare(strict_types=1);

namespace App\Module\Ui\Controller;

use Air\Core\Exception\ClassWasNotFound;
use Air\Core\Exception\DomainMustBeProvided;
use Air\Form\Exception\FilterClassWasNotFound;
use Air\Form\Exception\ValidatorClassWasNotFound;
use Air\Model\Exception\CallUndefinedMethod;
use Air\Model\Exception\ConfigWasNotProvided;
use Air\Model\Exception\DriverClassDoesNotExists;
use Air\Model\Exception\DriverClassDoesNotExtendsFromDriverAbstract;
use App\Helper\Route;
use App\Module\Ui\Form\Address;
use App\Module\Ui\Form\ChangeEmail;
use App\Module\Ui\Form\ChangePassword;
use App\Module\Ui\Form\PersonalData;
use App\Service\User\Auth;
use App\Service\User\Exception\InvalidEmailRequestCode;
use App\Service\User\Exception\UserCookieIsWrong;
use App\Service\User\Exception\UserIsNotLoggedIn;
use App\Service\User\User;
use App\Service\Viewed;
use App\Service\WishList;
use Exception;

class Profile extends AuthBase
{
  /**
   * @return void
   */
  public function index(): void
  {
  }

  /**
   * @param int $page
   * @return void
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   * @throws UserCookieIsWrong
   * @throws UserIsNotLoggedIn
   * @throws Exception
   */
  public function wishlist(int $page = 1): void
  {
    $show = 16;
    $offset = (($page ?? 1) - 1) * $show;

    $this->getView()->assign('products', WishList::products($show, $offset));
    $this->getView()->assign('count', WishList::count());

    $this->getView()->assign('page', $page);
    $this->getView()->assign('show', $show);
  }

  /**
   * @param int $page
   * @return void
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   * @throws UserCookieIsWrong
   * @throws UserIsNotLoggedIn
   * @throws Exception
   */
  public function viewedProducts(int $page = 1): void
  {
    $show = 16;
    $offset = (($page ?? 1) - 1) * $show;

    $this->getView()->assign('products', Viewed::products($show, $offset));
    $this->getView()->assign('count', Viewed::count());

    $this->getView()->assign('page', $page);
    $this->getView()->assign('show', $show);
  }

  /**
   * @return void|array
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   * @throws UserCookieIsWrong
   * @throws UserIsNotLoggedIn
   * @throws FilterClassWasNotFound
   * @throws ValidatorClassWasNotFound
   */
  public function edit()
  {
    if ($this->getRequest()->isPost()) {
      $this->getView()->setLayoutEnabled(false);
      $this->getView()->setAutoRender(false);

      $form = new PersonalData(Auth::getUser());

      if ($form->isValid($this->getParams())) {
        User::updatePersonalData($form->getValues());
        return;
      }
      $this->getResponse()->setStatusCode(400);
      return $form->getErrorFields();
    }
  }

  /**
   * @return array|void
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DomainMustBeProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   * @throws FilterClassWasNotFound
   * @throws UserCookieIsWrong
   * @throws UserIsNotLoggedIn
   * @throws ValidatorClassWasNotFound
   * @throws \PHPMailer\PHPMailer\Exception
   */
  public function changeEmailRequest()
  {
    $this->getView()->setLayoutEnabled(false);
    $this->getView()->setAutoRender(false);

    $form = new ChangeEmail(Auth::getUser());

    if ($form->isValid($this->getParams())) {
      User::requestEmailChange($form->getValues());
      return;
    }
    $this->getResponse()->setStatusCode(400);
    return $form->getErrorFields();
  }

  /**
   * @param string $code
   * @return void
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   * @throws UserCookieIsWrong
   * @throws UserIsNotLoggedIn
   * @throws InvalidEmailRequestCode
   * @throws Exception
   */
  public function changeEmail(string $code): void
  {
    User::changeEmail($code);
    $this->redirect(Route::profileEdit());
  }

  /**
   * @return array|void
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   * @throws FilterClassWasNotFound
   * @throws UserCookieIsWrong
   * @throws UserIsNotLoggedIn
   * @throws ValidatorClassWasNotFound
   */
  public function changePassword()
  {
    $this->getView()->setLayoutEnabled(false);
    $this->getView()->setAutoRender(false);

    $form = new ChangePassword(Auth::getUser());

    if ($form->isValid($this->getParams())) {
      User::changePassword($form->getValues());
      return;
    }

    $this->getResponse()->setStatusCode(400);
    return $form->getErrorFields();
  }

  /**
   * @param int|null $address
   * @return array|void
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   * @throws FilterClassWasNotFound
   * @throws UserCookieIsWrong
   * @throws UserIsNotLoggedIn
   * @throws ValidatorClassWasNotFound
   * @throws Exception
   */
  public function addresses(?int $address = -1)
  {
    if ($this->getRequest()->isPost()) {
      $this->getView()->setLayoutEnabled(false);
      $this->getView()->setAutoRender(false);

      $form = new Address();

      if ($form->isValid($this->getParams())) {
        User::address($form->getValues(), $address);
        return;
      }

      $this->getResponse()->setStatusCode(400);
      return $form->getErrorFields();
    }

    $this->getView()->assign('addressIndex', $address);
  }

  /**
   * @param int|null $address
   * @return void
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   * @throws UserCookieIsWrong
   * @throws UserIsNotLoggedIn
   */
  public function removeAddress(?int $address): void
  {
    $this->getView()->setLayoutEnabled(false);
    $this->getView()->setAutoRender(false);

    User::removeAddress($address);
  }

  /**
   * @return void
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   * @throws DomainMustBeProvided
   */
  public function logout(): void
  {
    Auth::forgetUser();
    $this->redirect(Route::home());
  }
}