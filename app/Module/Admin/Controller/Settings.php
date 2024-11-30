<?php

declare(strict_types=1);

namespace App\Module\Admin\Controller;

use Air\Core\Exception\ClassWasNotFound;
use Air\Crud\Controller\Single;
use Air\Filter\Number;
use Air\Form\Element\Checkbox;
use Air\Form\Element\Select;
use Air\Form\Element\Text;
use Air\Form\Form;
use Air\Form\Generator;
use Air\Model\Meta\Exception\PropertyWasNotFound;

class Settings extends Single
{
  /**
   * @param \App\Model\Settings $model
   * @return Form
   * @throws PropertyWasNotFound
   * @throws ClassWasNotFound
   */
  protected function getForm($model = null): Form
  {
    return Generator::full($model, [
      'Установки Google OAuth' => [
        new Checkbox('googleOAuthEnabled', [
          'label' => 'Увімкнено Google OAuth'
        ]),
        new Text('googleOAuthClientId', [
          'label' => 'Google OAuth ClientId'
        ]),
        new Text('googleOAuthClientSecret', [
          'label' => 'Google OAuth ClientSecret'
        ]),
      ],
      'Установки SMTP' => [
        new Select('smtpCharSet', [
          'label' => 'Кодування тіла E-mail',
          'hint' => 'Кодування тіла E-mail',
          'options' => [
            ['title' => 'US-ASCII', 'value' => \App\Model\Settings::SMTP_CHARSET_ASCII],
            ['title' => 'ISO-8859-1', 'value' => \App\Model\Settings::SMTP_CHARSET_ISO88591],
            ['title' => 'UTF-8', 'value' => \App\Model\Settings::SMTP_CHARSET_UTF8],
          ],
        ]),
        new Select('smtpEncryption', [
          'label' => 'Шифрування',
          'hint' => 'Шифрування',
          'options' => [
            ['title' => 'TLS', 'value' => \App\Model\Settings::SMTP_ENCRYPTION_TLS],
            ['title' => 'SSL', 'value' => \App\Model\Settings::SMTP_ENCRYPTION_SSL],
          ],
        ]),
        new Text('smtpHost', [
          'label' => 'Сервер',
        ]),
        new Text('smtpPort', [
          'label' => 'Порт',
          'filters' => [Number::class]
        ]),
        new Text('smtpUsername', [
          'label' => 'Ім\'я користувача',
        ]),
        new Text('smtpPassword', [
          'label' => 'Пароль',
        ]),
        new Text('smtpFromName',[
          'label' => 'Надіслати від імені',
        ]),
        new Text('smtpFromEmail', [
          'label' => 'Надіслати від E-mail',
        ]),
        new Checkbox('smtpLogEnabled', [
          'label' => 'Увімкнути логування SMTP-клієнта'
        ])
      ]
    ]);
  }
}
