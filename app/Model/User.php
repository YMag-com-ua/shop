<?php

declare(strict_types=1);

namespace App\Model;

use Air\Crud\Model\Language;
use Air\Model\ModelAbstract;
use App\Type\Checkout\Door;

/**
 * @collection User
 *
 * @property string $id
 *
 * @property string $firstName
 * @property string $secondName
 *
 * @property string $phone
 * @property string $email
 *
 * @property string $password
 *
 * @property boolean $isConfirmed
 * @property string $confirmCode
 * @property array $authIdentities
 *
 * @property string $requestChangeEmail
 * @property string $requestChangeEmailCode
 *
 * @property integer $createdAt
 * @property integer $updatedAt
 *
 * @property Door[] $addresses
 *
 * @property Language $preferLanguage
 */
class User extends ModelAbstract
{
  /**
   * @return string
   */
  public function getName(): string
  {
    return $this->firstName . ' ' . $this->secondName;
  }
}