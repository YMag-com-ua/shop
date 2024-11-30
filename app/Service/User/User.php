<?php

declare(strict_types=1);

namespace App\Service\User;

use Air\Core\Exception\ClassWasNotFound;
use Air\Core\Exception\DomainMustBeProvided;
use Air\Map;
use Air\Model\Exception\CallUndefinedMethod;
use Air\Model\Exception\ConfigWasNotProvided;
use Air\Model\Exception\DriverClassDoesNotExists;
use Air\Model\Exception\DriverClassDoesNotExtendsFromDriverAbstract;
use App\Helper\Mail;
use App\Service\User\Exception\InvalidEmailRequestCode;
use App\Service\User\Exception\UserCookieIsWrong;
use App\Service\User\Exception\UserIsNotLoggedIn;
use PHPMailer\PHPMailer\Exception;

class User
{
  /**
   * @param array $data
   * @return void
   * @throws UserCookieIsWrong
   * @throws UserIsNotLoggedIn
   * @throws ClassWasNotFound
   * @throws CallUndefinedMethod
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   */
  public static function updatePersonalData(array $data): void
  {
    $user = Auth::getUser();

    $user->populate(Map::executeAssoc($data, [
      'firstName',
      'secondName',
      'phone'
    ]));

    $user->save();
  }

  /**
   * @param array $data
   * @return void
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   * @throws Exception
   * @throws UserCookieIsWrong
   * @throws UserIsNotLoggedIn
   * @throws DomainMustBeProvided
   */
  public static function requestEmailChange(array $data): void
  {
    $user = Auth::getUser();

    $user->requestChangeEmail = $data['email'];
    $user->requestChangeEmailCode = uniqid();

    $user->save();

    Mail::sendRequestChangeEmail($user);
  }

  /**
   * @param string $code
   * @return void
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   * @throws InvalidEmailRequestCode
   * @throws UserCookieIsWrong
   * @throws UserIsNotLoggedIn
   */
  public static function changeEmail(string $code): void
  {
    $user = Auth::getUser();

    if ($user->requestChangeEmailCode !== $code) {
      throw new InvalidEmailRequestCode($code);
    }

    $user->email = $user->requestChangeEmail;

    $user->requestChangeEmail = null;
    $user->requestChangeEmailCode = null;

    $user->save();
  }

  /**
   * @param array $data
   * @return void
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   * @throws UserCookieIsWrong
   * @throws UserIsNotLoggedIn
   */
  public static function changePassword(array $data): void
  {
    $user = Auth::getUser();
    $user->password = md5($data['newPassword']);
    $user->save();
  }

  /**
   * @param array $data
   * @param int|null $addressIndex
   * @return void
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   * @throws UserCookieIsWrong
   * @throws UserIsNotLoggedIn
   */
  public static function address(array $data, ?int $addressIndex = -1): void
  {
    $user = Auth::getUser();

    $addresses = $user->addresses ?? [];

    if ($addressIndex !== -1) {
      $addresses[$addressIndex] = $data;
    } else {
      $addresses[] = $data;
    }

    $user->addresses = $addresses;
    $user->save();
  }

  /**
   * @param int $addressIndex
   * @return void
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   * @throws UserCookieIsWrong
   * @throws UserIsNotLoggedIn
   */
  public static function removeAddress(int $addressIndex): void
  {
    $user = Auth::getUser();

    $addresses = $user->addresses ?? [];
    unset($addresses[$addressIndex]);
    $user->addresses = array_values($addresses);

    $user->save();
  }
}