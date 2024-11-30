<?php

declare(strict_types=1);

namespace App\Module\Ui\Form;

use Air\Core\Exception\ClassWasNotFound;
use Air\Filter\Trim;
use Air\Form\Element\Text;
use Air\Form\Form;
use Air\Validator\StringLength;
use App\Model\User;

final class ChangePassword extends Form
{
  /**
   * @throws ClassWasNotFound
   */
  public function __construct(User $user)
  {
    parent::__construct([], [
      new Text('currentPassword', [
        'filters' => [
          Trim::class,
        ],
        'validators' => [
          StringLength::class => [
            'message' => '',
            'options' => ['min' => 6, 'max' => 50],
          ],
          [
            'message' => '',
            'isValid' => function (string $password) use ($user) {
              return $user->password === md5(Trim::clean($password));
            }
          ]
        ]
      ]),
      new Text('newPassword', [
        'filters' => [
          Trim::class,
        ],
        'validators' => [
          StringLength::class => [
            'message' => '',
            'options' => ['min' => 6, 'max' => 50],
          ],
        ]
      ]),
    ]);
  }
}