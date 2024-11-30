<?php

declare(strict_types=1);

namespace App\Type\Checkout;

use Air\Core\Exception\ClassWasNotFound;
use Air\Model\Exception\CallUndefinedMethod;
use Air\Model\Exception\ConfigWasNotProvided;
use Air\Model\Exception\DriverClassDoesNotExists;
use Air\Model\Exception\DriverClassDoesNotExtendsFromDriverAbstract;
use Air\Type\TypeAbstract;
use ReflectionException;
use Throwable;

class Data extends TypeAbstract
{
  /**
   * @var string
   */
  public string $firstName = '';

  /**
   * @var string
   */
  public string $lastName = '';

  /**
   * @var string
   */
  public string $phone = '';

  /**
   * @var string
   */
  public string $email = '';

  /**
   * @var string
   */
  public string $delivery = '';

  /**
   * @var string
   */
  public string $payment = '';

  /**
   * @var Door
   */
  public Door $door;

  /**
   * @var Warehouse
   */
  public Warehouse $warehouse;

  /**
   * @var BankIndividual
   */
  public BankIndividual $bankIndividual;

  /**
   * @var BankEntity
   */
  public BankEntity $bankEntity;

  /**
   * @param array|null $item
   * @throws ClassWasNotFound
   * @throws CallUndefinedMethod
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   * @throws ReflectionException
   * @throws Throwable
   */
  public function __construct(?array $item = [])
  {
    parent::__construct($item);

    if (empty($item['door'])) {
      $this->setDoor(new Door());
    }

    if (empty($item['warehouse'])) {
      $this->setWarehouse(new Warehouse());
    }

    if (empty($item['bankIndividual'])) {
      $this->setBankIndividual(new BankIndividual());
    }

    if (empty($item['bankEntity'])) {
      $this->setBankEntity(new BankEntity());
    }
  }

  /**
   * @return string
   */
  public function getFirstName(): string
  {
    return $this->firstName;
  }

  /**
   * @param string $firstName
   */
  public function setFirstName(string $firstName): void
  {
    $this->firstName = $firstName;
  }

  /**
   * @return string
   */
  public function getLastName(): string
  {
    return $this->lastName;
  }

  /**
   * @param string $lastName
   */
  public function setLastName(string $lastName): void
  {
    $this->lastName = $lastName;
  }

  /**
   * @return string
   */
  public function getPhone(): string
  {
    return $this->phone;
  }

  /**
   * @param string $phone
   */
  public function setPhone(string $phone): void
  {
    $this->phone = $phone;
  }

  /**
   * @return string
   */
  public function getEmail(): string
  {
    return $this->email;
  }

  /**
   * @param string $email
   */
  public function setEmail(string $email): void
  {
    $this->email = $email;
  }

  /**
   * @return string
   */
  public function getDelivery(): string
  {
    return $this->delivery;
  }

  /**
   * @param string $delivery
   */
  public function setDelivery(string $delivery): void
  {
    $this->delivery = $delivery;
  }

  /**
   * @return string
   */
  public function getPayment(): string
  {
    return $this->payment;
  }

  /**
   * @param string $payment
   */
  public function setPayment(string $payment): void
  {
    $this->payment = $payment;
  }

  /**
   * @return Door
   */
  public function getDoor(): Door
  {
    return $this->door;
  }

  /**
   * @param Door $door
   */
  public function setDoor(Door $door): void
  {
    $this->door = $door;
  }

  /**
   * @return Warehouse
   */
  public function getWarehouse(): Warehouse
  {
    return $this->warehouse;
  }

  /**
   * @param Warehouse $warehouse
   */
  public function setWarehouse(Warehouse $warehouse): void
  {
    $this->warehouse = $warehouse;
  }

  /**
   * @return BankIndividual
   */
  public function getBankIndividual(): BankIndividual
  {
    return $this->bankIndividual;
  }

  /**
   * @param BankIndividual $bankIndividual
   */
  public function setBankIndividual(BankIndividual $bankIndividual): void
  {
    $this->bankIndividual = $bankIndividual;
  }

  /**
   * @return BankEntity
   */
  public function getBankEntity(): BankEntity
  {
    return $this->bankEntity;
  }

  /**
   * @param BankEntity $bankEntity
   */
  public function setBankEntity(BankEntity $bankEntity): void
  {
    $this->bankEntity = $bankEntity;
  }
}