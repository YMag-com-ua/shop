<?php

declare(strict_types=1);

namespace App\Module\Ui\Controller;

use Exception;

class Product extends Base
{
  /**
   * @param \App\Model\Product $product
   * @return void
   * @throws Exception
   */
  public function index(\App\Model\Product $product): void
  {
    $product->addPopularity();

    $this->getView()->setMeta($product->meta);
    $this->getView()->assign('product', $product);
  }

  /**
   * @param \App\Model\Product $product
   * @return void
   * @throws Exception
   */
  public function quickView(\App\Model\Product $product): void
  {
    $product->addPopularity();
    $this->getView()->assign('product', $product);
  }
}