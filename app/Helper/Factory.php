<?php

declare(strict_types=1);

namespace App\Helper;

use Air\Core\Exception\ClassWasNotFound;
use Air\Core\Exception\DomainMustBeProvided;
use Air\Core\Front;
use Air\Log;
use Air\Mail;
use Air\Model\Exception\CallUndefinedMethod;
use Air\Model\Exception\ConfigWasNotProvided;
use Air\Model\Exception\DriverClassDoesNotExists;
use Air\Model\Exception\DriverClassDoesNotExtendsFromDriverAbstract;
use Air\ThirdParty\GoogleOAuth\GoogleOAuth;
use App\Model\Settings;

class Factory
{
  /**
   * @return GoogleOAuth|null
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DomainMustBeProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   */
  public static function googleOAuth(): ?GoogleOAuth
  {
    $settings = Settings::singleOne();

    if (!$settings || empty($settings->googleOAuthClientId) || empty($settings->googleOAuthClientSecret)) {
      return null;
    }

    return new GoogleOAuth(
      clientId: $settings->googleOAuthClientId,
      clientSecret: $settings->googleOAuthClientSecret,
      redirectUrl: Front::getInstance()->getRouter()->assemble([
        'controller' => 'auth',
        'action' => 'google'
      ])
    );
  }

  /**
   * @return Mail
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   */
  public static function mail(): Mail
  {
    $settings = Settings::singleOne();

    $debugOutput = match ($settings->smtpLogEnabled) {
      true => function (string $log) {
        Log::info('MAIL', array_filter(explode("\n", $log)));
      },
      false => null
    };

    return new Mail(
      charSet: $settings->smtpCharSet,
      encryption: $settings->smtpEncryption,
      port: $settings->smtpPort,
      host: $settings->smtpHost,
      username: $settings->smtpUsername,
      password: $settings->smtpPassword,
      fromName: $settings->smtpFromName,
      fromEmail: $settings->smtpFromEmail,
      debugOutput: $debugOutput
    );
  }
}