<?php

declare(strict_types=1);

namespace App\Module\Admin\Controller;

use Air\Core\Exception\ClassWasNotFound;
use Air\Crud\Controller\Multiple;
use Air\Form\Element\Checkbox;
use Air\Form\Element\DateTime;
use Air\Form\Element\MultipleGroup;
use Air\Form\Element\Select;
use Air\Form\Element\Text;
use Air\Form\Form;
use Air\Form\Generator;
use Air\Model\Meta\Exception\PropertyWasNotFound;

/**
 * @mod-manageable true
 *
 * @mod-controls {"type": "copy"}
 *
 * @mod-header {"title": "Код", "by": "code"}
 * @mod-header {"title": "Є множинним", "type": "bool", "by": "isMultiple"}
 * @mod-header {"title": "Увімкнено", "type": "bool", "by": "enabled"}
 *
 * @mod-filter {"type": "search", "by": ["code"]}
 * @mod-filter {"type": "bool", "by": "enabled", "true": "Увімкнено", "false": "Вимкнено", "value": "true"}
 */
class Coupon extends Multiple
{
  /**
   * @param $model
   * @return Form
   * @throws PropertyWasNotFound
   * @throws ClassWasNotFound
   */
  protected function getForm($model = null): Form
  {
    return Generator::full($model, [
      'Загальні' => [
        new Checkbox('enabled'),
        new Checkbox('isMultiple', [
          'label' => 'Є множинним',
          'description' => 'Якщо ця опція включена - купон можна використовувати кілька разів'
        ]),
        new Text('code', [
          'label' => 'Код'
        ]),
      ],
      'Розрахунки' => [
        new Select('type', [
          'value' => $model->type,
          'label' => 'Тип',
          'hint' => 'Type',
          'options' => [
            ['title' => 'Інфо', 'value' => \App\Model\Coupon::TYPE_INFO],
            ['title' => 'Фіксований', 'value' => \App\Model\Coupon::TYPE_FIXED],
            ['title' => 'Відсотковий', 'value' => \App\Model\Coupon::TYPE_PERCENTAGE],
          ],
        ]),
        new Text('value', [
          'label' => 'Значення',
          'allowNull' => true
        ])
      ],
      'Використання' => [
        new MultipleGroup('usages', [
          'allowNull' => true,
          'label' => 'Використання',
          'elements' => [
            new DateTime('when', [
              'label' => 'Коли'
            ]),
          ]
        ])
      ]
    ]);
  }
}
