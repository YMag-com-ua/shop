<?php

declare(strict_types=1);

namespace App\Module\Admin\Controller;

use Air\Core\Exception\ClassWasNotFound;
use Air\Crud\Controller\Multiple;
use Air\Form\Element\Checkbox;
use Air\Form\Element\Model;
use Air\Form\Element\MultipleGroup;
use Air\Form\Element\MultipleKeyValue;
use Air\Form\Element\MultipleModel;
use Air\Form\Element\RichContent;
use Air\Form\Element\Select;
use Air\Form\Element\Storage;
use Air\Form\Element\Text;
use Air\Form\Element\Tiny;
use Air\Form\Form;
use Air\Form\Generator;
use Air\Model\Meta\Exception\PropertyWasNotFound;
use Exception;

/**
 * @mod-manageable true
 * @mod-sortable title
 * @mod-items-per-page 50
 *
 * @mod-controls {"type": "copy"}
 *
 * @mod-header {"title": "Зобр.", "by": "image", "type": "image"}
 * @mod-header {"title": "Назва", "by": "title"}
 * @mod-header {"title": "Категорія", "by": "categories", "type": "modelTree", "field": "title"}
 * @mod-header {"title": "Мова", "by": "language", "type": "model", "field": "title"}
 * @mod-header {"title": "Увімкнено", "by": "enabled", "type": "bool"}
 *
 * @mod-filter {"type": "search", "by": ["title", "description", "content", "barcode", "url"]}
 * @mod-filter {"type": "bool", "by": "enabled", "true": "Увімкнено", "false": "Вимкнено", "value": "true"}
 * @mod-filter {"type": "model", "title": "Категорія", "by": "categories", "field": "title", "model": "\\App\\Model\\Category"}
 * @mod-filter {"type": "model", "title": "Мова", "by": "language", "field": "title", "model": "\\Air\\Crud\\Model\\Language"}
 */
class Product extends Multiple
{
  /**
   * @return array
   * @throws Exception
   */
  protected function getHeader(): array
  {
    $header = parent::getHeader();
    $header['price'] = [
      'title' => 'Ціна',
      'source' => function (\App\Model\Product $product) {
        return self::multiple([
          self::badge($product->price . ' грн', self::INFO),
          self::badge($product->yugPriceInitial . ' грн'),
        ]);
      }
    ];
    $header['ean'] = [
      'title' => 'Коди',
      'source' => function (\App\Model\Product $product) {
        return self::multiple([
          self::badge($product->barcode, self::DARK),
          self::badge($product->vendorCode, self::DARK),
        ]);
      }
    ];
    $header['quantity'] = [
      'title' => 'Кількість',
      'source' => function (\App\Model\Product $product) {
        return self::multiple([
          self::badge($product->quantity, self::SECONDARY),
          self::badge($product->availability, self::SECONDARY),
        ]);
      }
    ];
    return $header;
  }

  /**
   * @param \App\Model\Product $model
   * @return Form
   * @throws PropertyWasNotFound
   * @throws ClassWasNotFound
   */
  protected function getForm($model = null): Form
  {
    return Generator::fullRequired($model, [
      'Загальні' => [
        new Checkbox('isNew', ['label' => 'Новинка']),
        new Checkbox('isSale', ['label' => 'Розпродаж']),
        new Model('brand', ['label' => 'Бренд']),
        new Model('country', ['label' => 'Країна']),
        new Model('category', ['label' => 'Категорія']),
        new MultipleModel('categories', ['label' => 'Категорії']),
        new Storage('image'),
      ],
      'Зміст' => [
        new Tiny('content', ['allowNull' => true]),
        new Storage('images'),
      ],
      'Склад' => [
        new Text('vendorCode', ['label' => 'Код постачальника']),
        new Text('barcode', ['label' => 'Штрих-код']),
        new Text('price', ['label' => 'Ціна']),
        new Text('oldPrice', ['label' => 'Стара ціна']),
        new Text('quantity', ['label' => 'Кількість', 'allowNull' => true]),
        new Select('availability', [
          'label' => 'Доступність',
          'options' => [
            ['title' => 'Недоступний', 'value' => \App\Model\Product::AVAILABILITY_NO],
            ['title' => 'В наявності', 'value' => \App\Model\Product::AVAILABILITY_YES],
            ['title' => 'Зарезервований', 'value' => \App\Model\Product::AVAILABILITY_RESERVED],
            ['title' => 'За запитом', 'value' => \App\Model\Product::AVAILABILITY_UPON_REQUEST],
            ['title' => 'Очікування', 'value' => \App\Model\Product::AVAILABILITY_WAITING],
          ]
        ]),
      ],
      'Фільтри' => [
        new MultipleGroup('filters', [
          'label' => 'Фільтри',
          'allowNull' => true,
          'elements' => [
            new Model('filter', [
              'label' => 'Фільтр',
              'model' => \App\Model\Filter::class
            ]),
            new Text('value', ['label' => 'Значення']),
          ]
        ])
      ],
      'Специфікації' => [
        new MultipleKeyValue('previewSpecifications', [
          'allowNull' => true,
          'label' => 'Специфікації',
          'description' => 'Для швидкого перегляду',
          'keyLabel' => 'Назва',
          'valueLabel' => 'Значення'
        ]),
        new MultipleGroup('fullSpecifications', [
          'allowNull' => true,
          'label' => 'Специфікації',
          'description' => 'Полный список спецификаций',
          'elements' => [
            new Text('title', ['label' => 'Назва групи']),
            new MultipleKeyValue('fullSpecifications', [
              'label' => 'Специфікації',
              'keyLabel' => 'Назва',
              'valueLabel' => 'Значення'
            ]),
          ]
        ]),
      ],
      'Доповнений опис' => [
        new RichContent('richContent', [
          'allowNull' => true
        ]),
      ],
      'Додаткові товари' => [
        new MultipleModel('recommendedProducts', [
          'allowNull' => true,
          'label' => 'Рекомендовані товари'
        ]),
        new MultipleModel('alsoBuyProducts', [
          'allowNull' => true,
          'label' => 'З цим товаром купують'
        ]),
      ]
    ]);
  }
}
