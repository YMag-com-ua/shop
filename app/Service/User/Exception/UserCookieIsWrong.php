<?php

declare(strict_types=1);

namespace App\Service\User\Exception;

use Exception;

class UserCookieIsWrong extends Exception
{
  public function __construct()
  {
    parent::__construct('UserCookieIsWrong', 400);
  }
}