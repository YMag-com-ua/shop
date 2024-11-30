<?php

declare(strict_types=1);

namespace App\Module\Ui\Form;

use Air\Core\Exception\ClassWasNotFound;
use Air\Filter\Email;
use Air\Filter\Lowercase;
use Air\Filter\Phone;
use Air\Filter\Trim;
use Air\Form\Element\Text;
use Air\Form\Form;

final class Auth extends Form
{
  /**
   * @throws ClassWasNotFound
   */
  public function __construct()
  {
    parent::__construct([], [

      new Text('login', [
        'filters' => [
          Trim::class,
          Lowercase::class,
          function (string $login) {
            if (str_contains($login, '@')) {
              return Email::clean($login);
            }
            return Phone::clean($login);
          }
        ]
      ]),
      new Text('password', [
        'filters' => [
          Trim::class
        ]
      ])
    ]);
  }
}