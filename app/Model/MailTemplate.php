<?php

declare(strict_types=1);

namespace App\Model;

use Air\Crud\Model\Language;
use Air\Model\ModelAbstract;

/**
 * @collection MailTemplate
 *
 * @property string $id
 *
 * @property string $title
 *
 * @property string $url
 * @property string $subject
 * @property string $body
 *
 * @property Language $language
 */
class MailTemplate extends ModelAbstract
{
}