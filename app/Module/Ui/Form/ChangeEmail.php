<?php

declare(strict_types=1);

namespace App\Module\Ui\Form;

use Air\Core\Exception\ClassWasNotFound;
use Air\Filter\Email;
use Air\Filter\Lowercase;
use Air\Filter\Trim;
use Air\Form\Element\Text;
use Air\Form\Form;
use App\Model\User;
use MongoDB\BSON\ObjectId;

final class ChangeEmail extends Form
{
  /**
   * @throws ClassWasNotFound
   */
  public function __construct(User $user)
  {
    parent::__construct([], [
      new Text('email', [
        'filters' => [
          Trim::class,
          Email::class,
          Lowercase::class
        ],
        'validators' => [
          \Air\Validator\Email::class,
          [
            'message' => 'This email is already taken',
            'isValid' => function (string $email) use ($user) {
              $email = Trim::clean(Lowercase::clean(Email::clean($email)));
              return !User::count(['email' => $email, '_id' => ['$ne' => new ObjectId($user->id)]]);
            }
          ]
        ]
      ]),
    ]);
  }
}