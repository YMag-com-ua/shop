<?php

declare(strict_types=1);

namespace App\Module\Ui\Form;

use Air\Core\Exception\ClassWasNotFound;
use Air\Filter\HtmlSpecialChars;
use Air\Filter\Phone;
use Air\Filter\Trim;
use Air\Form\Element\Text;
use Air\Form\Form;
use App\Model\User;

final class PersonalData extends Form
{
  /**
   * @throws ClassWasNotFound
   */
  public function __construct(User $user)
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
            'message' => 'This phone is already taken',
            'isValid' => function (string $phone) use ($user) {
              $phone = Trim::clean(Phone::clean($phone));
              return !User::count(['phone' => $phone, 'email' => ['$ne' => $user->email]]);
            }
          ]
        ]
      ]),
    ]);
  }
}