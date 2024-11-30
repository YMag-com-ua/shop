<?php

declare(strict_types=1);

namespace App\Module\Cli\Helper;

use DOMElement;
use mysql_xdevapi\TableInsert;

class Str
{
  /**
   * @param string $str
   * @return string
   */
  public static function normalize(string $str): string
  {
    return mb_convert_case(mb_strtolower(trim($str)), MB_CASE_TITLE, 'UTF-8');
  }

  /**
   * @param string $description
   * @param int $length
   * @return string
   */
  public static function description(string $description, int $length = 300): string
  {
    $description = preg_replace("/(<style>.*?<\/style>)/is", '', $description);
    $description = str_replace("\n", ' ', $description);
    $description = strip_tags($description);
    $description = trim($description);
    $description = mb_substr($description, 0, $length);

    return $description;
  }

  /**
   * @param string $str
   * @param array $options
   * @return string
   */
  public static function slug(string $str, array $options = []): string
  {
    // Make sure string is in UTF-8 and strip invalid UTF-8 characters
    $str = mb_convert_encoding($str, 'UTF-8', mb_list_encodings());

    $defaults = [
      'delimiter' => '-',
      'limit' => null,
      'lowercase' => true,
      'replacements' => [],
      'transliterate' => true,
    ];

    // Merge options
    $options = array_merge($defaults, $options);

    $charMap = [
      // Latin
      'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'AE', 'Ç' => 'C',
      'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I',
      'Ð' => 'D', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ő' => 'O',
      'Ø' => 'O', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ű' => 'U', 'Ý' => 'Y', 'Þ' => 'TH',
      'ß' => 'ss',
      'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'ae', 'ç' => 'c',
      'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
      'ð' => 'd', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ő' => 'o',
      'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u', 'ű' => 'u', 'ý' => 'y', 'þ' => 'th',
      'ÿ' => 'y',

      // Latin symbols
      '©' => '(c)',

      // Greek
      'Α' => 'A', 'Β' => 'B', 'Γ' => 'G', 'Δ' => 'D', 'Ε' => 'E', 'Ζ' => 'Z', 'Η' => 'H', 'Θ' => '8',
      'Ι' => 'I', 'Κ' => 'K', 'Λ' => 'L', 'Μ' => 'M', 'Ν' => 'N', 'Ξ' => '3', 'Ο' => 'O', 'Π' => 'P',
      'Ρ' => 'R', 'Σ' => 'S', 'Τ' => 'T', 'Υ' => 'Y', 'Φ' => 'F', 'Χ' => 'X', 'Ψ' => 'PS', 'Ω' => 'W',
      'Ά' => 'A', 'Έ' => 'E', 'Ί' => 'I', 'Ό' => 'O', 'Ύ' => 'Y', 'Ή' => 'H', 'Ώ' => 'W', 'Ϊ' => 'I',
      'Ϋ' => 'Y',
      'α' => 'a', 'β' => 'b', 'γ' => 'g', 'δ' => 'd', 'ε' => 'e', 'ζ' => 'z', 'η' => 'h', 'θ' => '8',
      'ι' => 'i', 'κ' => 'k', 'λ' => 'l', 'μ' => 'm', 'ν' => 'n', 'ξ' => '3', 'ο' => 'o', 'π' => 'p',
      'ρ' => 'r', 'σ' => 's', 'τ' => 't', 'υ' => 'y', 'φ' => 'f', 'χ' => 'x', 'ψ' => 'ps', 'ω' => 'w',
      'ά' => 'a', 'έ' => 'e', 'ί' => 'i', 'ό' => 'o', 'ύ' => 'y', 'ή' => 'h', 'ώ' => 'w', 'ς' => 's',
      'ϊ' => 'i', 'ΰ' => 'y', 'ϋ' => 'y', 'ΐ' => 'i',

      // Turkish
      'Ş' => 'S', 'İ' => 'I', 'Ğ' => 'G',
      'ş' => 's', 'ı' => 'i', 'ğ' => 'g',

      // Russian
      'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D', 'Е' => 'E', 'Ё' => 'Yo', 'Ж' => 'Zh',
      'З' => 'Z', 'И' => 'I', 'Й' => 'J', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N', 'О' => 'O',
      'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T', 'У' => 'U', 'Ф' => 'F', 'Х' => 'H', 'Ц' => 'C',
      'Ч' => 'Ch', 'Ш' => 'Sh', 'Щ' => 'Sh', 'Ъ' => '', 'Ы' => 'Y', 'Ь' => '', 'Э' => 'E', 'Ю' => 'Yu',
      'Я' => 'Ya',
      'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'yo', 'ж' => 'zh',
      'з' => 'z', 'и' => 'i', 'й' => 'j', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o',
      'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c',
      'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sh', 'ъ' => '', 'ы' => 'y', 'ь' => '', 'э' => 'e', 'ю' => 'yu',
      'я' => 'ya',

      // Ukrainian
      'Є' => 'Ye', 'І' => 'I', 'Ї' => 'Yi', 'Ґ' => 'G',
      'є' => 'ye', 'і' => 'i', 'ї' => 'yi', 'ґ' => 'g',

      // Czech
      'Č' => 'C', 'Ď' => 'D', 'Ě' => 'E', 'Ň' => 'N', 'Ř' => 'R', 'Š' => 'S', 'Ť' => 'T', 'Ů' => 'U',
      'Ž' => 'Z',
      'č' => 'c', 'ď' => 'd', 'ě' => 'e', 'ň' => 'n', 'ř' => 'r', 'š' => 's', 'ť' => 't', 'ů' => 'u',
      'ž' => 'z',

      // Polish
      'Ą' => 'A', 'Ć' => 'C', 'Ę' => 'e', 'Ł' => 'L', 'Ń' => 'N', 'Ś' => 'S', 'Ź' => 'Z',
      'Ż' => 'Z',
      'ą' => 'a', 'ć' => 'c', 'ę' => 'e', 'ł' => 'l', 'ń' => 'n', 'ś' => 's', 'ź' => 'z',
      'ż' => 'z',

      // Latvian
      'Ā' => 'A', 'Ē' => 'E', 'Ģ' => 'G', 'Ī' => 'i', 'Ķ' => 'k', 'Ļ' => 'L', 'Ņ' => 'N', 'Ū' => 'u',
      'ā' => 'a', 'ē' => 'e', 'ģ' => 'g', 'ī' => 'i', 'ķ' => 'k', 'ļ' => 'l', 'ņ' => 'n', 'ū' => 'u',
    ];

    // Make custom replacements
    $str = preg_replace(array_keys($options['replacements']), $options['replacements'], $str);

    // Transliterate characters to ASCII
    if ($options['transliterate']) {
      $str = str_replace(array_keys($charMap), $charMap, $str);
    }

    // Replace non-alphanumeric characters with our delimiter
    $str = preg_replace('/[^\p{L}\p{Nd}]+/u', $options['delimiter'], $str);

    // Remove duplicate delimiters
    $str = preg_replace('/(' . preg_quote($options['delimiter'], '/') . '){2,}/', '$1', $str);

    // Truncate slug to max. characters
    $str = mb_substr($str, 0, ($options['limit'] ? $options['limit'] : mb_strlen($str, 'UTF-8')), 'UTF-8');

    // Remove delimiter from ends
    $str = trim($str, $options['delimiter']);

    return $options['lowercase'] ? mb_strtolower($str, 'UTF-8') : $str;
  }

  /**
   * @param mixed $id
   * @return int
   */
  public static function id(mixed $id): int
  {
    if (is_int($id)) {
      return $id;
    }

    return intval(str_replace(['_', '-'], '1', (string)$id));
  }

  /**
   * @param DOMElement $domElement
   * @param string|array $keys
   * @param mixed|null $default
   * @return mixed
   */
  public static function mixedValue(DOMElement $domElement, string|array $keys, mixed $default = null): mixed
  {
    foreach ((array)$keys as $key) {
      if ($domElement->getElementsByTagName($key)->length) {
        return trim($domElement->getElementsByTagName($key)[0]->nodeValue);
      }
    }
    return $default;
  }

  /**
   * @param DOMElement $domElement
   * @param string|array $keys
   * @return array
   */
  public static function arrayValue(DOMElement $domElement, string|array $keys): array
  {
    $items = [];

    foreach ((array)$keys as $key) {
      foreach ($domElement->getElementsByTagName($key) as $item) {
        $items[] = trim($item->nodeValue);
      }
    }
    return $items;
  }
}