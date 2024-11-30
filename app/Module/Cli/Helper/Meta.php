<?php

declare(strict_types=1);

namespace App\Module\Cli\Helper;

class Meta
{
  /**
   * @param array $product
   * @return array
   */
  public static function getMeta(array $product): array
  {
    return [
      'title' => $product['title'],
      'description' => $product['description'],
      'ogImage' => $product['image'],
      'ogTitle' => $product['title'],
      'ogDescription' => $product['description'],
      'useModelData' => false,
    ];
  }
}