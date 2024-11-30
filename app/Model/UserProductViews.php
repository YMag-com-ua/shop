<?php

declare(strict_types=1);

namespace App\Model;

use Air\Model\ModelAbstract;

/**
 * @collection UserProductViews
 *
 * @property string $id
 *
 * @property User $user
 * @property string $productUrl
 *
 * @property integer $createdAt
 */
class UserProductViews extends ModelAbstract
{
}