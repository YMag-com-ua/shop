<?php

declare(strict_types=1);

namespace App\Service\User\Exception;

use Exception;

class InvalidEmailRequestCode extends Exception
{
  /**
   * @param string $code
   */
  public function __construct(string $code)
  {
    parent::__construct('InvalidEmailRequestCode: ' . $code, 400);
  }
}