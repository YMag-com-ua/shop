<?php

declare(strict_types=1);

namespace App\Module\Ui\Form;

use Air\Core\Exception\ClassWasNotFound;
use Air\Filter\Email;
use Air\Filter\HtmlSpecialChars;
use Air\Filter\Lowercase;
use Air\Filter\Phone;
use Air\Filter\Trim;
use Air\Form\Element\Text;
use Air\Form\Form;
use Air\Validator\StringLength;
use App\Model\User;

final class Register extends Form
{
  /**
   * @throws ClassWasNotFound
   */
  public function __construct()
  {
    parent::__construct([], [
      new Text('firstName', [
        'filters' => [
          Trim::class,
          HtmlSpecialChars::class,
        ]
      ]),
      new Text('secondName', [
        'filters' => [
          Trim::class,
          HtmlSpecialChars::class,
        ]
      ]),
      new Text('phone', [
        'filters' => [
          Trim::class,
          Phone::class
        ],
        'validators' => [
          \Air\Validator\Phone::class,
          [
            'message' => '',
            'isValid' => function (string $phone) {
              $phone = Trim::clean(Phone::clean($phone));
              return !User::count(['phone' => $phone]);
            }
          ]
        ]
      ]),
      new Text('email', [
        'filters' => [
          Trim::class,
          Email::class,
          Lowercase::class
        ],
        'validators' => [
          \Air\Validator\Email::class,
          [
            'message' => '',
            'isValid' => function (string $email) {
              $email = Trim::clean(Lowercase::clean(Email::clean($email)));
              return !User::count(['email' => $email]);
            }
          ]
        ]
      ]),
      new Text('password', [
        'validators' => [
          StringLength::class => [
            'options' => ['min' => 6, 'max' => 50],
            'message' => ''
          ],
        ]
      ])
    ]);
  }
}