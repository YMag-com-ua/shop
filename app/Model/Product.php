<?php

declare(strict_types=1);

namespace App\Model;

use Air\Core\Exception\ClassWasNotFound;
use Air\Crud\Model\Language;
use Air\Model\Exception\CallUndefinedMethod;
use Air\Model\Exception\ConfigWasNotProvided;
use Air\Model\Exception\DriverClassDoesNotExists;
use Air\Model\Exception\DriverClassDoesNotExtendsFromDriverAbstract;
use Air\Model\ModelAbstract;
use Air\Type\File;
use Air\Type\Meta;
use Air\Type\RichContent;

/**
 * @collection Product
 *
 * @property string $id
 *
 * @property string $title
 * @property string $description
 * @property string $content
 * @property RichContent[] $richContent
 * @property File $image
 *
 * @property array $previewSpecifications
 * @property array $fullSpecifications
 *
 * @property boolean $isNew
 * @property boolean $isSale
 *
 * @property File[] $images
 * @property array $filters
 *
 * @property Brand $brand
 * @property Category $category
 * @property Category[] $categories
 * @property Product[] $recommendedProducts
 * @property Product[] $alsoBuyProducts
 *
 * @property Country $country
 *
 * @property double $oldPrice
 * @property double $price
 *
 * @property string $vendorCode
 * @property string $barcode
 *
 * @property integer $quantity
 * @property integer $availability
 *
 * @property integer $popularity
 *
 * @property Meta $meta
 * @property string $url
 * @property boolean $enabled
 * @property Language $language
 *
 * @property array $yug
 * @property integer $yugId
 * @property integer $yugCategoryId
 * @property integer $yugPriceInitial
 * @property integer $yugPriceRrp
 * @property string $yugSyncId
 */
class Product extends ModelAbstract
{
  const int AVAILABILITY_NO = 0;
  const int AVAILABILITY_YES = 1;
  const int AVAILABILITY_RESERVED = 2;
  const int AVAILABILITY_WAITING = 3;
  const int AVAILABILITY_UPON_REQUEST = 4;

  /**
   * @return File
   * @throws ClassWasNotFound
   * @throws CallUndefinedMethod
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   */
  public function getImage(): File
  {
    $image = $this->image;
    if (!$image) {
      $catalogSettings = CatalogSettings::singleOne();
      $image = $catalogSettings->noImage;
      $image->title = $this->title;
      $image->alt = $this->description ?? $this->title;
    }

    return $image;
  }

  /**
   * @return array|File[]
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   */
  public function getImages(): array
  {
    return array_filter([...[$this->getImage()], ...$this->images]);
  }

  /**
   * @return bool
   */
  public function isImageAvailable(): bool
  {
    return !!$this->image;
  }

  /**
   * @param int $term
   * @return void
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   */
  public function addPopularity(int $term = 1): void
  {
    $this->popularity = $this->popularity + $term;
    $this->save();
  }
}