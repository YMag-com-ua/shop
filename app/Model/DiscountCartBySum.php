<?php

declare(strict_types=1);

namespace App\Model;

use Air\Crud\Model\Language;
use Air\Model\ModelAbstract;

/**
 * @collection DiscountCartBySum
 *
 * @property string $id
 * @property boolean $enabled
 * @property string $title
 * @property string $description
 *
 * @property boolean $allowedToBeUsedWithCoupons
 *
 * @property integer $minSum
 * @property string $type
 * @property float $value
 *
 * @property Language $language
 */
class DiscountCartBySum extends ModelAbstract
{
  const string TYPE_FIXED = 'fixed';
  const string TYPE_PERCENTAGE = 'percentage';
}