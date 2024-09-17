<?php

declare(strict_types=1);

namespace App\Module\Ui\Controller;

use Air\Core\Exception\ClassWasNotFound;
use Air\Model\Exception\CallUndefinedMethod;
use Air\Model\Exception\ConfigWasNotProvided;
use Air\Model\Exception\DriverClassDoesNotExists;
use Air\Model\Exception\DriverClassDoesNotExtendsFromDriverAbstract;
use App\Model\Brand;
use App\Model\CatalogSettings;
use App\Model\Category;
use App\Model\Country;
use App\Model\Filter;
use App\Model\Product;
use Exception;
use MongoDB\BSON\Regex;

class Catalog extends Base
{
  /**
   * @var array
   */
  private array $sortingOptions = [
    'popular' => ['popularity' => -1],
    'price-asc' => ['price' => 1],
    'price-desc' => ['price' => -1],
  ];

  /**
   * @var int[]
   */
  private array $showOptions = [40, 80, 120];

  /**
   * @param Category|null $category
   * @param string|null $search
   * @param array|null $filters
   * @param array|null $brands
   * @param array|null $countries
   * @param int|null $page
   * @param string|null $sort
   * @param int|null $show
   * @param bool $new
   * @param bool $sale
   * @param int|null $priceMin
   * @param int|null $priceMax
   * @return void
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   * @throws Exception
   */
  public function index(
    ?Category $category = null,
    ?string   $search = null,
    ?array    $filters = [],
    ?array    $brands = [],
    ?array    $countries = [],
    ?int      $page = 1,
    ?string   $sort = 'popular',
    ?int      $show = 40,
    bool      $new = false,
    bool      $sale = false,
    ?int      $priceMin = null,
    ?int      $priceMax = null
  ): void
  {
    if (!in_array($show, $this->showOptions)) {
      $show = $this->showOptions[0];
    }

    if ($category) {
      $this->getView()->setMeta($category->meta);
      $this->getView()->assign('category', $category);
      $cond = ['$or' => [
        ['categories' => $category->id],
        ['category' => $category->id],
      ]];

    } else {
      $catalogSettings = CatalogSettings::singleOne();
      $this->getView()->setMeta($catalogSettings->meta);
      $this->getView()->assign('catalogSettings', $catalogSettings);
      $cond = [];
    }

    if ($search) {
      $search = trim(strip_tags($search));
      $cond['search'] = new Regex($search, 'i');
    }

    if ($new) {
      $cond['isNew'] = true;
    }

    if ($sale) {
      $cond['isSale'] = true;
    }

    if (($min = intval($priceMin)) && ($max = intval($priceMax))) {
      $cond['$and'] = [
        ['price' => ['$gte' => $min]],
        ['price' => ['$lte' => $max]],
      ];

    } elseif ($min = intval($priceMin)) {
      $cond['price'] = ['$gte' => $min];

    } elseif ($max = intval($priceMax)) {
      $cond['price'] = ['$lte' => $max];
    }

    $selectedFilters = [];
    foreach (($filters ?? []) as $url => $values) {
      $filter = Filter::one(['url' => $url]);
      if ($filter) {
        $selectedFilters[$url] = [
          'filter' => $filter,
          'values' => $values
        ];
        $cond['$and'] = $cond['$and'] ?? [];
        foreach ($values as $value) {
          $cond['$and'][] = [
            'filters' => [
              'filter' => $filter->id,
              'value' => (string)$value
            ]
          ];
        }
      }
    }

    $selectedBrands = [];
    foreach (($brands ?? []) as $url) {
      $brand = Brand::one(['url' => $url]);
      $selectedBrands[$url] = $brand;
      $cond['brand']['$in'] = $cond['brand']['$in'] ?? [];
      $cond['brand']['$in'][] = $brand->id;
    }

    $selectedCountries = [];
    foreach (($countries ?? []) as $url) {
      $country = Country::one(['url' => $url]);
      $selectedCountries[$url] = $country;
      $cond['country']['$in'] = $cond['country']['$in'] ?? [];
      $cond['country']['$in'][] = $country->id;
    }

    $this->getView()->assign('selectedFilters', $selectedFilters);
    $this->getView()->assign('selectedBrands', $selectedBrands);
    $this->getView()->assign('selectedCountries', $selectedCountries);

    $this->getView()->assign('page', $page);
    $this->getView()->assign('sort', $sort);
    $this->getView()->assign('show', $show);

    $this->getView()->assign('new', $new);
    $this->getView()->assign('sale', $sale);

    $this->getView()->assign('search', $search);

    $this->getView()->assign('priceMin', $priceMin);
    $this->getView()->assign('priceMax', $priceMax);

    $this->getView()->assign('count', Product::quantity($cond));
    $this->getView()->assign('products', Product::all(
      $cond,
      $this->sortingOptions[$sort] ?? $this->sortingOptions['popular'],
      $show, (($page ?? 1) - 1) * $show)
    );
  }
}