<?php

declare(strict_types=1);

namespace App\Model;

use Air\Crud\Model\Language;
use Air\Model\ModelAbstract;
use Air\Type\Meta;
use Air\Type\RichContent;

/**
 * @collection LegalRules
 *
 * @property string $id
 *
 * @property string $title
 * @property RichContent[] $richContent
 *
 * @property Meta $meta
 *
 * @property Language $language
 */
class LegalRules extends ModelAbstract
{
}