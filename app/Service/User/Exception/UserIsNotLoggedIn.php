<?php

declare(strict_types=1);

namespace App\Service\User\Exception;

use Exception;

class UserIsNotLoggedIn extends Exception
{
  public function __construct()
  {
    parent::__construct('UserIsNotLoggedIn', 400);
  }
}