<?php

declare(strict_types=1);

namespace App\Model;

use Air\Crud\Model\Language;
use Air\Model\ModelAbstract;

/**
 * @collection Filter
 *
 * @property string $id
 * @property boolean $enabled
 * @property string $url
 *
 * @property string $title
 * @property array $values
 *
 * @property Language $language
 *
 * @property array $yug
 * @property integer $yugId
 */
class Filter extends ModelAbstract
{
}

