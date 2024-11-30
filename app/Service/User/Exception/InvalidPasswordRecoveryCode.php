<?php

declare(strict_types=1);

namespace App\Service\User\Exception;

use Exception;

class InvalidPasswordRecoveryCode extends Exception
{
  /**
   * @param string $code
   */
  public function __construct(string $code)
  {
    parent::__construct('InvalidPasswordRecoveryCode: ' . $code, 400);
  }
}