<?php

namespace App\Service\Sync;

use Air\Core\Exception\ClassWasNotFound;
use Air\Core\Front;
use Air\Model\Exception\CallUndefinedMethod;
use Air\Model\Exception\ConfigWasNotProvided;
use Air\Model\Exception\DriverClassDoesNotExists;
use Air\Model\Exception\DriverClassDoesNotExtendsFromDriverAbstract;
use Air\Model\Meta\Exception\CollectionCantBeWithoutPrimary;
use Air\Model\Meta\Exception\CollectionCantBeWithoutProperties;
use Air\Model\Meta\Exception\CollectionNameDoesNotExists;
use Air\Model\Meta\Exception\PropertyIsSetIncorrectly;
use Air\Slug;
use Air\Storage;
use Air\Type\FaIcon;
use Air\Type\File;
use Air\Type\Meta;
use Air\Type\RichContent;
use App\Model\Brand;
use App\Model\Category;
use App\Model\Country;
use App\Model\Filter;
use App\Model\Product;
use Closure;
use Exception;
use MongoDB\BSON\ObjectId;
use ReflectionException;
use Throwable;

class Sync
{
  /**
   * @var Closure|null
   */
  private static ?Closure $logger = null;

  /**
   * @param Closure $logger
   * @return void
   */
  public static function setLogger(Closure $logger): void
  {
    self::$logger = $logger;
  }

  /**
   * @param string $title
   * @param array $data
   * @return void
   */
  private static function log(mixed $title, array $data = []): void
  {
    if (self::$logger) {
      $logger = self::$logger;
      $logger($title, $data);
    }
  }

  /**
   * @var string|null
   */
  private static ?string $syncId = null;

  /**
   * @return YugContract
   * @throws ClassWasNotFound
   */
  private static function getYug(): YugContract
  {
    $yugConfig = Front::getInstance()->getConfig()['yug'];
    return new YugContract($yugConfig['userKey'], $yugConfig['secret']);
  }

  /**
   * @return array
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   * @throws ReflectionException
   * @throws Throwable
   */
  private static function getStartFaIcon(): array
  {
    return (new FaIcon(['icon' => 'star', 'style' => FaIcon::STYLE_DUOTONE]))->toArray();
  }

  /**
   * @return string
   */
  private static function getLanguage(): string
  {
    return '66df08562df17a05660e35d4';
  }

  /**
   * @param string|null $title
   * @param string|null $description
   * @param File|null $image
   * @return array
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   * @throws ReflectionException
   * @throws Throwable
   */
  private static function getMeta(string $title = null, string $description = null, File $image = null): array
  {
    return (new Meta([
      'title' => $title,
      'description' => $description ?? $title,
      'ogTitle' => $title,
      'ogDescription' => $description ?? $title,
      'ogImage' => $image
    ]))->toArray();
  }

  /**
   * @param int $yugId
   * @param int $parentId
   * @param string $title
   * @return void
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   * @throws ReflectionException
   * @throws Throwable
   * @throws CollectionCantBeWithoutPrimary
   * @throws CollectionCantBeWithoutProperties
   * @throws CollectionNameDoesNotExists
   * @throws PropertyIsSetIncorrectly
   */
  private static function addCategory(int $yugId, int $parentId, string $title): void
  {
    if (Category::count(['yugId' => $yugId])) {
      self::log('Category exists, skipping', [$yugId, $title]);
      return;
    }

    $title = ucfirst(trim($title));

    $category = new Category([
      'title' => $title,
      'description' => $title,
      'url' => Slug::slug($title),

      'faIcon' => self::getStartFaIcon(),
      'language' => self::getLanguage(),

      'enabled' => true,

      'yug' => ['id' => $yugId, 'parentId' => $parentId, 'name' => $title],
      'yugId' => $yugId,
      'yugParentId' => $parentId
    ]);

    $category->save();

    Storage::createFolder('/catalog', $category->url);

    self::log('Category added', [$yugId, $title]);
  }

  /**
   * @param string $title
   * @return Brand|null
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws CollectionCantBeWithoutPrimary
   * @throws CollectionCantBeWithoutProperties
   * @throws CollectionNameDoesNotExists
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   * @throws PropertyIsSetIncorrectly
   * @throws ReflectionException
   * @throws Throwable
   */
  private static function addBrand(string $title): ?Brand
  {
    $title = ucfirst(trim($title));

    if (!strlen($title)) {
      return null;
    }

    $brand = Brand::fetchOne(['title' => $title]);
    if ($brand) {
      return $brand;
    }

    $brand = new Brand([
      'title' => $title,
      'url' => Slug::slug($title),
      'meta' => self::getMeta($title),
      'enabled' => true,
    ]);

    $brand->save();
    return $brand;
  }

  /**
   * @param int $yugCategoryId
   * @return Category|null
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   */
  private static function getCategory(int $yugCategoryId): ?Category
  {
    return Category::fetchOne(['yugId' => $yugCategoryId]);
  }

  /**
   * @param array $params
   * @param bool $full
   * @return array
   */
  private static function getSpecification(array $params = [], bool $full = false): array
  {
    $specifications = [];

    if ($full) {
      $params = array_slice($params, 5);
    }

    foreach ($params as $index => $param) {
      if ($index == 5 && !$full) {
        break;
      }
      $specifications[] = [
        'key' => $param['name'],
        'value' => $param['value']
      ];
    }

    if (!$full) {
      return $specifications;
    }
    return [[
      'title' => 'Характеристики',
      'fullSpecifications' => $specifications
    ]];
  }

  /**
   * @param Category $category
   * @param string $productUrl
   * @param array $sources
   * @param string $alt
   * @param string $title
   * @return array
   * @throws ClassWasNotFound
   */
  private static function getImages(
    Category $category,
    string $productUrl,
    array $sources = [],
    string $alt = '',
    string $title = ''
  ): array
  {
    if (!count($sources)) {
      return [];
    }

    Storage::createFolder('/catalog/' . $category->url, $productUrl);
    $folder = '/catalog/' . $category->url . '/' . $productUrl;

    $images = [];
    foreach ($sources as $index => $source) {
      try {
        $imageIndex = '';
        if (count($sources) > 1) {
          $imageIndex = '. Зображення #' . ($index + 1);
        }

        self::log(['Trying upload image', $index + 1, count($sources)]);

        $image = Storage::uploadByUrl($folder, $source);

        $image->title = (mb_strlen($title) ? $title : $alt) . $imageIndex;
        $image->alt = (mb_strlen($alt) ? $alt : $title) . $imageIndex;

        $images[] = $image;
      } catch (Throwable) {
      }
    }
    return $images;
  }

  /**
   * @param array $params
   * @return array
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws CollectionCantBeWithoutPrimary
   * @throws CollectionCantBeWithoutProperties
   * @throws CollectionNameDoesNotExists
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   * @throws PropertyIsSetIncorrectly
   * @throws ReflectionException
   */
  private static function getFilters(array $params): array
  {
    $filters = [];

    foreach ($params as $param) {
      $value = trim($param['value']);
      $filter = Filter::fetchOne(['yugId' => $param['id']]);

      if (!$filter) {
        $title = ucfirst(trim($param['name']));

        $filter = new Filter([
          'enabled' => true,
          'title' => $title,
          'url' => Slug::slug($title),
          'language' => self::getLanguage(),
          'values' => [$value],
          'yug' => $param,
          'yugId' => $param['id']
        ]);

        $filter->save();
      }

      $values = $filter->values ?? [];

      if (!in_array($value, $values)) {
        $values[] = $value;
        $filter->values = $values;
        $filter->save();
      }

      $filters[] = [
        'filter' => $filter->id,
        'value' => $value
      ];
    }
    return $filters;
  }

  /**
   * @param int $yugCategoryId
   * @return array
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   */
  private static function getCategories(int $yugCategoryId): array
  {
    $categories = [];

    $category = Category::fetchOne(['yugId' => $yugCategoryId]);

    if ($category) {
      $categories[] = $category->id;

      if ($category->parent) {
        $categories = array_merge($categories, self::getCategories($category->parent->yugId));
      }
    }

    return $categories;
  }

  /**
   * @param string $title
   * @return Country|null
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws CollectionCantBeWithoutPrimary
   * @throws CollectionCantBeWithoutProperties
   * @throws CollectionNameDoesNotExists
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   * @throws PropertyIsSetIncorrectly
   * @throws ReflectionException
   */
  private static function addCountry(string $title): ?Country
  {
    $title = ucfirst(trim($title));

    if (!strlen($title)) {
      return null;
    }

    $country = Country::fetchOne(['title' => $title]);
    if ($country) {
      return $country;
    }

    $country = new Country([
      'title' => $title,
      'url' => Slug::slug($title),
    ]);

    $country->save();
    return $country;
  }

  /**
   * @param array $data
   * @return void
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws CollectionCantBeWithoutPrimary
   * @throws CollectionCantBeWithoutProperties
   * @throws CollectionNameDoesNotExists
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   * @throws PropertyIsSetIncorrectly
   * @throws ReflectionException
   * @throws Throwable
   */
  private static function addProduct(array $data): void
  {
    if (Product::count(['yugId' => $data['id']])) {
      self::log('Product exists, skipping', [$data['id'], $data['name']]);
      return;
    }

    $title = $data['name'] ?? '';
    $description = mb_substr(strip_tags(trim($data['description'] ?? '')), 0, 150);
    $content = $data['description'] ?? '';

    $richContent = null;
    if (mb_strlen($description)) {
      $richContent = [['type' => RichContent::TYPE_HTML, 'value' => $data['description'] ?? '']];
    }

    $filters = self::getFilters($data['params'] ?? []);

    $brand = self::addBrand($data['brand'] ?? '');
    $category = self::getCategory($data['categoryId']);
    $categories = self::getCategories($data['categoryId']);;

    $vendorCode = $data['EAN'] ?? '';
    $barcode = $data['artikul'] ?? '';

    $url = Slug::slug($data['name'] ?? '');

    $previewSpecifications = self::getSpecification($data['params'] ?? []);
    $fullSpecifications = self::getSpecification($data['params'] ?? [], true);

    $images = self::getImages($category, $url, $data['pictures'] ?? [], $title, $description);
    $image = $images[0] ?? null;

    $meta = self::getMeta($title, $description, $image);

    $product = new Product([
      'title' => $title,
      'description' => $description,
      'content' => $content,
      'richContent' => $richContent,

      'brand' => $brand->id,
      'category' => $category->id,
      'categories' => $categories,
      'filters' => $filters,

      'vendorCode' => $vendorCode,
      'barcode' => $barcode,

      'url' => $url,
      'meta' => $meta,

      'quantity' => 10,
      'enabled' => false,
      'language' => self::getLanguage(),
      'availability' => Product::AVAILABILITY_YES,

      'previewSpecifications' => $previewSpecifications,
      'fullSpecifications' => $fullSpecifications,

      'images' => $images,
      'image' => $image,

      'yug' => $data,
      'yugId' => $data['id'],
      'yugCategoryId' => $data['categoryId'] ?? 0,
    ]);

    $product->save();

    self::log(['Product added', $data['id'], $data['name']]);
  }

  /**
   * @param array $data
   * @return void
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws CollectionCantBeWithoutPrimary
   * @throws CollectionCantBeWithoutProperties
   * @throws CollectionNameDoesNotExists
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   * @throws PropertyIsSetIncorrectly
   * @throws ReflectionException
   * @throws Throwable
   */
  private static function updateProduct(array $data): void
  {
    $localProduct = Product::fetchOne([
      'yugId' => intval($data['id']),
    ]);

    if (!$localProduct) {
      self::log(['No local product', 'Skipping'], [$data['id'], $data['name_ukr']]);
      return;
    }

    $localProduct->enabled = true;
    $localProduct->yugSyncId = self::$syncId;

    $price = intval($data['rrp']);
    if (!$price) {
      $initialPrice = intval($data['price']);
      $price = intval($initialPrice + ($initialPrice * 0.1));
    }

    $localProduct->yugPriceInitial = intval($data['price']);
    $localProduct->price = $price;

    if (!$localProduct->country) {
      $localProduct->country = self::addCountry($data['country'] ?? '');
    }

    if (!$localProduct->brand) {
      $localProduct->brand = self::addBrand($data['brand'] ?? '');
    }

    $localProduct->save();

    self::log(['Updated product', $data['name_ukr']]);
  }

  /**
   * @return void
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws CollectionCantBeWithoutPrimary
   * @throws CollectionCantBeWithoutProperties
   * @throws CollectionNameDoesNotExists
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   * @throws PropertyIsSetIncorrectly
   * @throws ReflectionException
   * @throws Throwable
   */
  private static function products(): void
  {
    self::log('Yug get-products');
    $products = self::getYug()->getGoods();
    $productCount = count($products);
    self::log('Yug get-products received ' . $productCount);

    foreach ($products as $index => $product) {
      self::log(['Adding product', $index + 1, $productCount]);
      self::addProduct($product);
    }
  }

  /**
   * @return void
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws CollectionCantBeWithoutPrimary
   * @throws CollectionCantBeWithoutProperties
   * @throws CollectionNameDoesNotExists
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   * @throws PropertyIsSetIncorrectly
   * @throws ReflectionException
   * @throws Throwable
   */
  private static function updateProducts(): void
  {
    self::log('Yug get-price');
    $products = self::getYug()->getPrice();
    $productCount = count($products);
    self::log('Yug get-price received ' . $productCount);

    foreach ($products as $index => $product) {
      self::log(['Updating product', $index + 1, $productCount]);
      self::updateProduct($product);
    }
  }

  /**
   * @return void
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws CollectionCantBeWithoutPrimary
   * @throws CollectionCantBeWithoutProperties
   * @throws CollectionNameDoesNotExists
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   * @throws PropertyIsSetIncorrectly
   * @throws ReflectionException
   * @throws Throwable
   */
  private static function categories(): void
  {
    $categories = self::getYug()->getCategories();
    $categoryCount = count($categories);

    foreach ($categories as $index => $category) {
      self::log(['Adding category: ', $index + 1, $categoryCount]);
      self::addCategory($category['id'], $category['parent_id'], $category['name']);
    }

    self::log('Setup parent categories');
    foreach (Category::fetchAll(['yugParentId' => ['$ne' => 0]]) as $category) {
      if (!$category->parent) {
        $category->parent = Category::fetchOne(['yugId' => $category->yugParentId]);
        $category->save();
        self::log('Parent category', [$category->title]);
      }
    }
  }

  /**
   * @return void
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   */
  private static function disableEmptyCategories(): void
  {
    $index = 0;
    foreach (Category::fetchAll(['enabled' => true]) as $category) {
      if (!Product::count(['categories' => $category])) {
        $index++;

        $category->enabled = false;
        $category->save();
      }
    }
    self::log('Disabled ' . $index . ' Categories');
  }

  /**
   * @return void
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   */
  private static function updateCategoryImages(): void
  {
    $index = 0;
    foreach (Category::fetchAll(['enabled' => true, 'image' => null]) as $category) {
      $productsCount = Product::count(['categories' => $category]);

      if ($productsCount > 0) {
        $limit = $productsCount - 1;
        $product = Product::fetchAll([], [], 1, rand(0, $limit))[0];
        $category->image = $product->image;
        $category->save();

        $index++;
      }
    }

    self::log('Updated images for ' . $index . ' categories');
  }

  /**
   * @return void
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   */
  private static function updateCategoryBrands(): void
  {
    $index = 0;

    foreach (Category::fetchAll(['enabled' => true, '$or' => [['brands' => null], ['brands' => []]]]) as $category) {
      $brands = [];
      foreach (Product::fetchAll(['categories' => $category]) as $product) {
        if ($product->brand) {
          $brands[$product->brand->id] = true;
        }
      }
      if (count($brands)) {
        $category->populateWithoutQuerying([
          'brands' => array_keys($brands)
        ]);
        $category->save();
        $index++;
      }
    }

    self::log('Updated CategoryBrands for ' . $index . ' Categories');
  }

  /**
   * @return void
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   */
  private static function updateCategoryFilters(): void
  {
    $index = 0;

    foreach (Category::fetchAll(['enabled' => true, '$or' => [['filters' => null], ['filters' => []]]]) as $category) {
      $filters = [];
      foreach (Product::fetchAll(['categories' => $category]) as $product) {
        if (count($product->filters ?? [])) {
          foreach ($product->filters as $filter) {
            $filters[$filter['filter']] = true;
          }
        }
      }

      if (count($filters)) {
        $category->populateWithoutQuerying([
          'filters' => array_slice(array_keys($filters), 0, 5)
        ]);
        $category->save();
        $index++;
      }
    }

    self::log('Updated CategoryFilters for ' . $index . ' Categories');
  }

  /**
   * @return void
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   */
  private static function updateCategoryCountries(): void
  {
    $index = 0;

    foreach (Category::fetchAll(['enabled' => true, '$or' => [['countries' => null], ['countries' => []]]]) as $category) {
      $countries = [];
      foreach (Product::fetchAll(['categories' => $category]) as $product) {
        if ($product->country) {
          $countries[$product->country->id] = true;
        }
      }
      if (count($countries)) {
        $category->populateWithoutQuerying([
          'countries' => array_keys($countries)
        ]);
        $category->save();

        $index++;
      }
    }

    self::log('Updated CategoryCountries for ' . $index . ' Categories');
  }

  /**
   * @return void
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   */
  private static function updateRecommendProducts(): void
  {
    $products = Product::fetchAll(['enabled' => true, '$or' => [['recommendedProducts' => null], ['recommendedProducts' => []]]]);
    $productCount = count($products);

    foreach ($products as $index => $product) {

      $product->recommendedProducts = Product::fetchAll([
        'category' => $product->category,
        '_id' => ['$ne' => new ObjectId($product->id)]
      ], [], 5);

      $product->save();

      self::log(['Updated RecommendProducts', $index + 1, $productCount]);
    }
  }

  /**
   * @return void
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   * @throws ReflectionException
   * @throws Throwable
   */
  private static function updateCategoryMeta(): void
  {
    $index = 0;
    foreach (Category::fetchAll(['enabled' => true, '$or' => [['meta' => null], ['meta' => []]]]) as $category) {
      $category->populateWithoutQuerying([
        'meta' => self::getMeta($category->title, $category->title, $category->image)
      ]);
      $category->save();

      $index++;
    }

    self::log('Updated Meta for ' . $index . ' categories');
  }

  /**
   * @return void
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   * @throws Exception
   */
  private static function updateProductRRP(): void
  {
    self::log('Yug get-price');
    $products = self::getYug()->getPrice();
    $productCount = count($products);
    self::log('Yug get-price received ' . $productCount);

    foreach ($products as $index => $product) {
      $price = intval($product['rrp']);
      if (!$price) {
        $initialPrice = intval($product['price']);
        $price = $initialPrice + ($initialPrice * 0.1);
      }

      $p = Product::fetchOne(['yugId' => intval($product['id'])]);

      if (!$p) {
        continue;
      }

      $p->price = $price;
      $p->save();

      self::log(['Updating product', $index + 1, $productCount]);
    }
  }

  /**
   * @return void
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   */
  private static function hideNotExistedProducts(): void
  {
    Product::update(['yugSyncId' => ['$ne' => self::$syncId]], ['enabled' => false]);
  }

  /**
   * @return void
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws CollectionCantBeWithoutPrimary
   * @throws CollectionCantBeWithoutProperties
   * @throws CollectionNameDoesNotExists
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   * @throws PropertyIsSetIncorrectly
   * @throws ReflectionException
   * @throws Throwable
   */
  public static function syncAll(): void
  {
    self::$syncId = md5(microtime());

    Category::remove();
    Product::remove();
    Brand::remove();
    Filter::remove();
    Storage::deleteFolder('/catalog');
    
    Storage::createFolder('/', 'catalog');

    self::categories();
    self::products();
    self::updateProducts();
    self::hideNotExistedProducts();

    self::disableEmptyCategories();
    self::updateCategoryImages();
    self::updateCategoryMeta();

    self::updateCategoryBrands();
    self::updateCategoryFilters();
    self::updateCategoryCountries();

    self::updateRecommendProducts();
  }

  /**
   * @return void
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws CollectionCantBeWithoutPrimary
   * @throws CollectionCantBeWithoutProperties
   * @throws CollectionNameDoesNotExists
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   * @throws PropertyIsSetIncorrectly
   * @throws ReflectionException
   * @throws Throwable
   */
  public static function syncPrices(): void
  {
    self::$syncId = md5(microtime());

    self::updateProducts();
    self::hideNotExistedProducts();

    self::disableEmptyCategories();
  }
}