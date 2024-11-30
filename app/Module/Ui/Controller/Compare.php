<?php

declare(strict_types=1);

namespace App\Module\Ui\Controller;

use Air\Core\Exception\ClassWasNotFound;
use Exception;
use App\Model\Product;

class Compare extends Base
{
  /**
   * @return void
   * @throws Exception
   */
  public function index(): void
  {
    $this->getView()->assign('products', \App\Helper\Compare::products());
  }

  /**
   * @param Product $product
   * @return int
   * @throws ClassWasNotFound
   */
  public function add(Product $product): int
  {
    \App\Helper\Compare::add($product);
    return \App\Helper\Compare::count();
  }

  /**
   * @param Product $product
   * @return int
   * @throws ClassWasNotFound
   */
  public function remove(Product $product): int
  {
    \App\Helper\Compare::remove($product);
    return \App\Helper\Compare::count();
  }
}