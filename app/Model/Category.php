<?php

declare(strict_types=1);

namespace App\Model;

use Air\Crud\Model\Language;
use Air\Model\ModelAbstract;
use Air\Type\FaIcon;
use Air\Type\File;
use Air\Type\Meta;
use App\Type\Banner;

/**
 * @collection Category
 *
 * @property string $id
 *
 * @property string $title
 * @property string $description
 * @property FaIcon $faIcon
 * @property File $image
 * @property Category $parent
 * @property Filter[] $filters
 * @property Brand[] $brands
 * @property Country[] $countries
 *
 * @property Meta $meta
 * @property string $url
 *
 * @property boolean $enabled
 * @property integer $position
 *
 * @property Banner[] $banners
 *
 * @property Language $language
 *
 * @property array $yug
 * @property integer $yugId
 * @property integer $yugParentId
 */
class Category extends ModelAbstract
{
}

