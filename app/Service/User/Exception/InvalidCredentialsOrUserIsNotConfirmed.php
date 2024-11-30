<?php

declare(strict_types=1);

namespace App\Service\User\Exception;

use Exception;

class InvalidCredentialsOrUserIsNotConfirmed extends Exception
{
  /**
   * @param array $credentials
   */
  public function __construct(array $credentials)
  {
    parent::__construct('InvalidCredentialsOrUserIsNotConfirmed: ' . var_export($credentials, true), 400);
  }
}