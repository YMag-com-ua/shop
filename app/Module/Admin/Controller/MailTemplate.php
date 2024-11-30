<?php

declare(strict_types=1);

namespace App\Module\Admin\Controller;

use Air\Core\Exception\ClassWasNotFound;
use Air\Crud\Controller\Multiple;
use Air\Form\Element\Text;
use Air\Form\Element\Tiny;
use Air\Form\Form;
use Air\Form\Generator;
use Air\Model\Meta\Exception\PropertyWasNotFound;

/**
 * @mod-controls {"title": "Редагувати запис", "url": {"controller": "mailTemplate", "action": "manage"}, "icon": "edit"}
 *
 * @mod-header {"title": "Title", "by": "title", "sorting": true}
 * @mod-header {"title": "Language", "by": "language", "type": "model", "field": "title"}
 */
class MailTemplate extends Multiple
{
  /**
   * @param \App\Model\MailTemplate $model
   * @return Form
   * @throws PropertyWasNotFound
   * @throws ClassWasNotFound
   */
  protected function getForm($model = null): Form
  {
    return Generator::full($model, [
      'Шаблон' => [
        new Text('subject', [
          'label' => 'Тема листа',
        ]),
        new Tiny('body', [
          'label' => 'Текст листа',
        ])
      ]
    ]);
  }
}
