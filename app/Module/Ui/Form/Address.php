<?php

declare(strict_types=1);

namespace App\Module\Ui\Form;

use Air\Core\Exception\ClassWasNotFound;
use Air\Filter\Email;
use Air\Filter\HtmlSpecialChars;
use Air\Filter\Lowercase;
use Air\Filter\Number;
use Air\Filter\Phone;
use Air\Filter\Trim;
use Air\Form\Element\Text;
use Air\Form\Form;
use Air\Validator\StringLength;
use App\Model\User;
use App\Type\Checkout\Door;

final class Address extends Form
{
  /**
   * @throws ClassWasNotFound
   */
  public function __construct()
  {
    parent::__construct([], [
      new Text('region', [
        'filters' => [
          Trim::class,
          HtmlSpecialChars::class,
        ]
      ]),
      new Text('city', [
        'filters' => [
          Trim::class,
          HtmlSpecialChars::class,
        ]
      ]),
      new Text('line1', [
        'filters' => [
          Trim::class,
          HtmlSpecialChars::class,
        ]
      ]),
      new Text('line2', [
        'filters' => [
          Trim::class,
          HtmlSpecialChars::class,
        ]
      ]),
      new Text('postCode', [
        'filters' => [
          Trim::class,
          HtmlSpecialChars::class
        ]
      ]),
    ]);
  }
}