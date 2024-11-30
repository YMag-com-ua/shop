<?php

declare(strict_types=1);

namespace App\Module\Ui\Controller;

use Air\Core\Exception\ClassWasNotFound;
use App\Model\Product;
use Exception;

class Wishlist extends Base
{
  /**
   * @return void
   * @throws Exception
   */
  public function index(): void
  {
    $this->getView()->assign('products', \App\Service\WishList::products());
  }

  /**
   * @param Product $product
   * @return int
   * @throws ClassWasNotFound
   */
  public function add(Product $product): int
  {
    \App\Service\WishList::add($product);
    return \App\Service\WishList::count();
  }

  /**
   * @param Product $product
   * @return int
   * @throws ClassWasNotFound
   */
  public function remove(Product $product): int
  {
    \App\Service\WishList::remove($product);
    return \App\Service\WishList::count();
  }
}