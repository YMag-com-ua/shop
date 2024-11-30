<?php

return [
  [
    'title' => 'Загальні налаштування',
    'icon' => 'cogs',
    'items' => [
      [
        'title' => 'Загальні налаштування',
        'icon' => 'cogs',
        'url' => ['controller' => 'general']
      ],
      [
        'title' => 'Налаштування',
        'icon' => 'cogs',
        'url' => ['controller' => 'settings']
      ],
      [
        'title' => 'Шаблони Email',
        'icon' => 'envelope',
        'url' => ['controller' => 'mailTemplate']
      ],
    ]
  ],
  [
    'title' => 'Вітрина',
    'icon' => 'star',
    'items' => [
      [
        'title' => 'Головний повзунок',
        'icon' => 'sliders',
        'url' => ['controller' => 'showcaseSlider']
      ],
      [
        'title' => 'Найпопулярніші категорії',
        'icon' => 'list',
        'url' => ['controller' => 'showcaseTopCategories']
      ],
      [
        'title' => 'Товари',
        'icon' => 'star',
        'url' => ['controller' => 'showcaseProducts']
      ],
      [
        'title' => 'Блог',
        'icon' => 'blog',
        'url' => ['controller' => 'showcaseBlog']
      ],
      [
        'title' => 'Переваги',
        'icon' => 'star',
        'url' => ['controller' => 'showcaseAdv']
      ]
    ]
  ],
  [
    'title' => 'Каталог',
    'icon' => 'database',
    'items' => [
      [
        'title' => 'Налаштування',
        'icon' => 'cog',
        'url' => ['controller' => 'catalogSettings']
      ],
      [
        'title' => 'Категорії',
        'icon' => 'list',
        'url' => ['controller' => 'category']
      ],
      [
        'title' => 'Товари',
        'icon' => 'image',
        'url' => ['controller' => 'product']
      ],
      [
        'title' => 'Бренди',
        'icon' => 'star',
        'url' => ['controller' => 'brand']
      ],
      [
        'title' => 'Фільтри',
        'icon' => 'filter',
        'url' => ['controller' => 'filter']
      ],
      [
        'title' => 'Країни виробники',
        'icon' => 'globe',
        'url' => ['controller' => 'country']
      ],
    ]
  ],
  [
    'title' => 'Офіс',
    'icon' => 'briefcase',
    'items' => [
      [
        'title' => 'Налаштування',
        'icon' => 'cogs',
        'url' => [
          'controller' => 'shopSettings'
        ]
      ],
      [
        'title' => 'Замовлення',
        'icon' => 'list',
        'url' => ['controller' => 'order']
      ],
      [
        'title' => 'Звернення користувачів',
        'icon' => 'user',
        'url' => ['controller' => 'userRequest']
      ],
    ]
  ],
  [
    'title' => 'Знижки',
    'icon' => 'tags',
    'items' => [
      [
        'title' => 'Купони',
        'icon' => 'receipt',
        'url' => ['controller' => 'coupon']
      ],
      [
        'title' => 'Знижка в залежності від суми',
        'icon' => 'tag',
        'url' => ['controller' => 'discountCartBySum']
      ],
    ]
  ],
  [
    'title' => 'Сервісні сторінки',
    'icon' => 'bell-concierge',
    'items' => [
      [
        'title' => 'Оплата та доставка',
        'icon' => 'truck',
        'url' => ['controller' => 'paymentDelivery']
      ],
      [
        'title' => 'Про нас',
        'icon' => 'people-carry',
        'url' => ['controller' => 'about']
      ],
      [
        'title' => 'Контакти',
        'icon' => 'phone-volume',
        'url' => ['controller' => 'contact']
      ],
    ]
  ],
  [
    'title' => 'Блог',
    'icon' => 'blog',
    'items' => [
      [
        'title' => 'Налаштування блогу',
        'icon' => 'cogs',
        'url' => ['controller' => 'blogSettings']
      ],
      [
        'title' => 'Категорії',
        'icon' => 'list',
        'url' => ['controller' => 'articleCategory']
      ],
      [
        'title' => 'Теги',
        'icon' => 'tag',
        'url' => ['controller' => 'articleTag']
      ],
      [
        'title' => 'Статті',
        'icon' => 'newspaper',
        'url' => ['controller' => 'article']
      ],
    ]
  ],
  [
    'title' => 'Юридичні сторінки',
    'icon' => 'scale-balanced',
    'items' => [
      [
        'title' => 'Гарантія',
        'icon' => 'book',
        'url' => ['controller' => 'legalWarranty']
      ],
      [
        'title' => 'Правила користування сайтом',
        'icon' => 'book',
        'url' => ['controller' => 'legalRules']
      ],
      [
        'title' => 'Обмін і повернення товару',
        'icon' => 'book',
        'url' => ['controller' => 'legalRefund']
      ],
      [
        'title' => 'Політика конфіденційності',
        'icon' => 'book',
        'url' => ['controller' => 'legalPrivacyPolicy']
      ],
    ]
  ]
];