<?php

declare(strict_types=1);

namespace App\Helper;

use Air\Core\Exception\ClassWasNotFound;
use Air\Core\Exception\DomainMustBeProvided;
use Air\Model\Exception\CallUndefinedMethod;
use Air\Model\Exception\ConfigWasNotProvided;
use Air\Model\Exception\DriverClassDoesNotExists;
use Air\Model\Exception\DriverClassDoesNotExtendsFromDriverAbstract;
use App\Model\MailTemplate;
use App\Model\User;
use PHPMailer\PHPMailer\Exception;

class Mail
{
  /**
   * @param User $user
   * @return bool
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DomainMustBeProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   * @throws Exception
   */
  public static function sendConfirmationLink(User $user): bool
  {
    $template = MailTemplate::one([
      'url' => 'email-confirmation',
      'language' => $user->preferLanguage
    ]);

    if ($template) {
      return Factory::mail()->send(
        email: $user->email,
        subject: $template->subject,
        body: $template->body,
        name: $user->getName(),
        vars: [
          'name' => $user->getName(),
          'link' => Route::confirm($user->confirmCode)
        ]
      );
    }
    return false;
  }

  /**
   * @param User $user
   * @param string $password
   * @return bool
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   * @throws Exception
   */
  public static function sendNewPassword(User $user, string $password): bool
  {
    $template = MailTemplate::one([
      'url' => 'forgot-password',
      'language' => $user->preferLanguage
    ]);

    if ($template) {
      return Factory::mail()->send(
        email: $user->email,
        subject: $template->subject,
        body: $template->body,
        name: $user->getName(),
        vars: [
          'name' => $user->getName(),
          'password' => $password
        ]
      );
    }
    return false;
  }

  /**
   * @param User $user
   * @return bool
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DomainMustBeProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   * @throws Exception
   */
  public static function sendRequestChangeEmail(User $user): bool
  {
    $template = MailTemplate::one([
      'url' => 'change-email-request',
      'language' => $user->preferLanguage
    ]);

    if ($template) {
      return Factory::mail()->send(
        email: $user->email,
        subject: $template->subject,
        body: $template->body,
        name: $user->getName(),
        vars: [
          'name' => $user->getName(),
          'link' => Route::profileChangeEmailRequest($user->requestChangeEmailCode)
        ]
      );
    }
    return false;
  }
}