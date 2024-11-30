<?php

declare(strict_types=1);

namespace App\Module\Cli\Controller;

use Air\Core\Controller;
use Air\Core\Exception\ClassWasNotFound;
use Air\Model\Exception\CallUndefinedMethod;
use Air\Model\Exception\ConfigWasNotProvided;
use Air\Model\Exception\DriverClassDoesNotExists;
use Air\Model\Exception\DriverClassDoesNotExtendsFromDriverAbstract;
use Air\Model\Meta\Exception\CollectionCantBeWithoutPrimary;
use Air\Model\Meta\Exception\CollectionCantBeWithoutProperties;
use Air\Model\Meta\Exception\CollectionNameDoesNotExists;
use Air\Model\Meta\Exception\PropertyIsSetIncorrectly;
use Air\Model\Meta\Exception\PropertyWasNotFound;
use App\Model\Category;
use App\Model\Product;
use App\Module\Cli\Helper\Log;
use ReflectionException;
use Throwable;

class Ager extends Controller
{
  /**
   * @return void
   * @throws ClassWasNotFound
   * @throws CallUndefinedMethod
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   * @throws CollectionCantBeWithoutPrimary
   * @throws CollectionCantBeWithoutProperties
   * @throws CollectionNameDoesNotExists
   * @throws PropertyIsSetIncorrectly
   * @throws PropertyWasNotFound
   * @throws ReflectionException
   * @throws Throwable
   */
  public function index(): void
  {
    set_time_limit(0);

    Log::l('Starting upload Ager');

    $dom = new \DOMDocument();
    $dom->loadXML(file_get_contents('/var/www/shop/catalog/ager.xml'));

    \App\Module\Cli\Helper\Category::buildTree($dom, Category::SOURCE_AGER);
    \App\Module\Cli\Helper\Product::upload($dom, Product::SOURCE_AGER);

    Log::l('Start disableEmptyCategories ' . Category::SOURCE_AGER);
    \App\Module\Cli\Helper\Category::disableEmptyCategories(Category::SOURCE_AGER);

    Log::l('Start updateCategoryImages ' . Category::SOURCE_AGER);
    \App\Module\Cli\Helper\Category::updateCategoryImages(Category::SOURCE_AGER);
  }
}