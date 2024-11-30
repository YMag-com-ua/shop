<?php

declare(strict_types=1);

namespace App\Module\Admin\Controller;

use Air\Core\Exception\ClassWasNotFound;
use Air\Crud\Controller\Multiple;
use Air\Filter\Number;
use Air\Form\Element\Checkbox;
use Air\Form\Element\Select;
use Air\Form\Element\Text;
use Air\Form\Element\Textarea;
use Air\Form\Form;
use Air\Form\Generator;
use Air\Model\Meta\Exception\PropertyWasNotFound;

/**
 * @mod-manageable true
 *
 * @mod-controls {"type": "copy"}
 *
 * @mod-header {"title": "Назва", "by": "title", "sorting": true}
 * @mod-header {"title": "Увімкнено", "type": "bool", "by": "enabled"}
 *
 * @mod-filter {"type": "bool", "by": "enabled", "true": "Увімкнено", "false": "Вимкнено", "value": "true"}
 */
class DiscountCartBySum extends Multiple
{
  /**
   * @param \App\Model\DiscountCartBySum $model
   * @return Form
   * @throws PropertyWasNotFound
   * @throws ClassWasNotFound
   */
  protected function getForm($model = null): Form
  {
    return Generator::full($model, [
      'Загальні' => [
        new Textarea('description', [
          'description' => 'Використовуйте змінну "{sum}" - на її місці з\'явиться різниця між сумою кошика та мінімальною сумою на знижку.'
        ]),
        new Checkbox('allowedToBeUsedWithCoupons', [
          'label' => 'Дозволено використовувати разом із купонами'
        ]),
      ],
      'Розрахунки' => [
        new Text('minSum', [
          'label' => 'Сума',
          'filters' => [Number::class]
        ]),
        new Select('type', [
          'value' => $model->type,
          'label' => 'Тип',
          'hint' => 'Type',
          'options' => [
            ['title' => 'Фіксований', 'value' => \App\Model\DiscountCartBySum::TYPE_FIXED],
            ['title' => 'Відсотковий', 'value' => \App\Model\DiscountCartBySum::TYPE_PERCENTAGE],
          ],
        ]),
        new Text('value', [
          'label' => 'Значення',
          'filters' => [Number::class],
          'allowNull' => true
        ])
      ],
    ]);
  }
}
