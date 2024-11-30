<?php

declare(strict_types=1);

namespace App\Module\Cli\Helper;

use DOMElement;

class Params
{
  /**
   * @param DOMElement $element
   * @param int $limit
   * @return array
   */
  public static function getParams(DOMElement $element, int $limit = 5): array
  {
    $params = [];

    /** @var DOMElement $param */
    foreach ($element->getElementsByTagName('param') as $index => $param) {
      if ($index === $limit) {
        break;
      }
      $params[] = [
        'key' => $param->getAttribute('name'),
        'value' => trim($param->nodeValue)
      ];
    }
    return $params;
  }
}