<?php

declare(strict_types=1);

namespace App\Model;

use Air\Model\ModelAbstract;

/**
 * @collection UserProductFavorites
 *
 * @property string $id
 *
 * @property User $user
 * @property string $productUrl
 *
 * @property integer $createdAt
 */
class UserProductWishList extends ModelAbstract
{
}