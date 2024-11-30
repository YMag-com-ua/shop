<?php

declare(strict_types=1);

namespace App\Type;

use Air\Type\TypeAbstract;

class Address extends TypeAbstract
{
  /**
   * @var string|null
   */
  public ?string $region = null;

  /**
   * @var string|null
   */
  public ?string $city = null;

  /**
   * @var string|null
   */
  public ?string $address = null;

  /**
   * @var string|null
   */
  public ?string $apartments = null;

  /**
   * @var string|null
   */
  public ?string $zipCode = null;

  /**
   * @return string|null
   */
  public function getRegion(): ?string
  {
    return $this->region;
  }

  /**
   * @param string|null $region
   */
  public function setRegion(?string $region): void
  {
    $this->region = $region;
  }

  /**
   * @return string|null
   */
  public function getCity(): ?string
  {
    return $this->city;
  }

  /**
   * @param string|null $city
   */
  public function setCity(?string $city): void
  {
    $this->city = $city;
  }

  /**
   * @return string|null
   */
  public function getAddress(): ?string
  {
    return $this->address;
  }

  /**
   * @param string|null $address
   */
  public function setAddress(?string $address): void
  {
    $this->address = $address;
  }

  /**
   * @return string|null
   */
  public function getApartments(): ?string
  {
    return $this->apartments;
  }

  /**
   * @param string|null $apartments
   */
  public function setApartments(?string $apartments): void
  {
    $this->apartments = $apartments;
  }

  /**
   * @return string|null
   */
  public function getZipCode(): ?string
  {
    return $this->zipCode;
  }

  /**
   * @param string|null $zipCode
   */
  public function setZipCode(?string $zipCode): void
  {
    $this->zipCode = $zipCode;
  }
}