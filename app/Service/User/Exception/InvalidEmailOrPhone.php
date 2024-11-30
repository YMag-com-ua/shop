<?php

declare(strict_types=1);

namespace App\Service\User\Exception;

use Exception;

class InvalidEmailOrPhone extends Exception
{
  /**
   * @param string $login
   */
  public function __construct(string $login)
  {
    parent::__construct('InvalidEmailOrPhone: ' . $login, 400);
  }
}