<?php

declare(strict_types=1);

namespace App\Module\Cli\Helper;

class Log
{
  /**
   * @param string $title
   * @param mixed|null $data
   * @return void
   */
  public static function l(string $title, mixed $data = null): void
  {
    echo $title;

    if ($data) {
      echo "\n";
      echo var_export($data, true);
    }

    echo "\n";
  }
}