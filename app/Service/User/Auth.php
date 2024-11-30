<?php

declare(strict_types=1);

namespace App\Service\User;

use Air\Cookie;
use Air\Core\Exception\ClassWasNotFound;
use Air\Core\Exception\DomainMustBeProvided;
use Air\Crud\Model\Language;
use Air\Map;
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
use App\Helper\Mail;
use App\Model\User;
use App\Service\User\Exception\InvalidCredentialsOrUserIsNotConfirmed;
use App\Service\User\Exception\InvalidEmailOrPhone;
use App\Service\User\Exception\UserCookieIsWrong;
use App\Service\User\Exception\UserIsNotLoggedIn;
use PHPMailer\PHPMailer\Exception;
use ReflectionException;
use Throwable;

class Auth
{
  /**
   * @return bool
   */
  public static function isLogged(): bool
  {
    try {
      return !!self::getUser();
    } catch (Throwable) {
      return false;
    }
  }

  /**
   * @return User
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   * @throws UserCookieIsWrong
   * @throws UserIsNotLoggedIn
   */
  public static function getUser(): User
  {
    if (!($authIdentity = Cookie::get('user'))) {
      throw new UserIsNotLoggedIn();
    }

    $user = User::one(['authIdentities' => $authIdentity]);

    if ($user) {
      return $user;
    }

    self::forgetUser();
    throw new UserCookieIsWrong();
  }

  /**
   * @param User $user
   * @return void
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   */
  public static function setUser(User $user): void
  {
    $authIdentity = uniqid();

    $authIdentities = $user->authIdentities ?? [];
    $authIdentities[] = $authIdentity;
    $user->authIdentities = $authIdentities;
    $user->save();

    Cookie::set('user', $authIdentity);
  }

  /**
   * @return void
   * @throws ClassWasNotFound
   */
  public static function forgetUser(): void
  {
    Cookie::remove('user');
  }

  /**
   * @param array $data
   * @param Language $language
   * @return User
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws CollectionCantBeWithoutPrimary
   * @throws CollectionCantBeWithoutProperties
   * @throws CollectionNameDoesNotExists
   * @throws ConfigWasNotProvided
   * @throws DomainMustBeProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   * @throws Exception
   * @throws PropertyIsSetIncorrectly
   * @throws ReflectionException
   */
  public static function register(array $data, Language $language): User
  {
    $user = new User(Map::executeAssoc($data, [
      'firstName',
      'secondName',
      'phone',
      'email',
    ]));

    $user->preferLanguage = $language;
    $user->isConfirmed = false;
    $user->password = md5($data['password']);
    $user->confirmCode = uniqid();

    $user->save();

    Mail::sendConfirmationLink($user);
    return $user;
  }

  /**
   * @param array $data
   * @return User
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   * @throws InvalidCredentialsOrUserIsNotConfirmed
   */
  public static function auth(array $data): User
  {
    $user = User::one([
      'isConfirmed' => true,
      'password' => md5($data['password']),
      '$or' => [
        ['email' => $data['login']],
        ['phone' => $data['login']],
      ],
    ]);

    if (!$user) {
      throw new InvalidCredentialsOrUserIsNotConfirmed($data);
    }

    self::setUser($user);
    return $user;
  }

  /**
   * @param string $code
   * @return bool
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   */
  public static function confirm(string $code): bool
  {
    $user = User::one([
      '$and' => [
        ['confirmCode' => $code],
        ['confirmCode' => ['$ne' => '']]
      ]
    ]);

    if (!$user) {
      return false;
    }

    $user->confirmCode = null;
    $user->isConfirmed = true;

    $user->save();
    self::setUser($user);

    return true;
  }

  /**
   * @param string $code
   * @param Language $language
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
   * @throws PropertyIsSetIncorrectly
   * @throws ReflectionException
   * @throws InvalidCode
   * @throws UnableToGetUserByAccessToken
   */
  public static function google(string $code, Language $language): void
  {
    $googleOAuth = Factory::googleOAuth();
    $profile = $googleOAuth->auth($code);

    $user = User::one([
      'email' => $profile->getEmail()
    ]);

    if (!$user) {
      $user = new User($profile->toArray());
      $user->preferLanguage = $language;
    }

    $user->isConfirmed = true;
    $user->confirmCode = null;

    $user->save();

    self::setUser($user);
  }

  /**
   * @param string $login
   * @return void
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   * @throws Exception
   * @throws InvalidEmailOrPhone
   */
  public static function generateNewPassword(string $login): void
  {
    $user = User::one([
      '$or' => [
        ['email' => $login],
        ['phone' => $login],
      ],
    ]);

    if (!$user) {
      throw new InvalidEmailOrPhone($login);
    }

    $password = uniqid();
    $user->password = md5($password);
    $user->save();

    Mail::sendNewPassword($user, $password);
  }
}