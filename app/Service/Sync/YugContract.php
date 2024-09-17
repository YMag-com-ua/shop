<?php

namespace App\Service\Sync;

use Exception;
use Firebase\JWT\JWT;

class YugContract
{
  private const string URL_AUTH = 'https://auth.yugcontract.ua/api/auth/get-auth-token';

  /**
   * @var string
   */
  private string $userKey;

  /**
   * @var string
   */
  private string $secret;

  /**
   * @var string|null
   */
  private null|string $authToken = null;

  /**
   * @param string $userKey
   * @param string $secret
   */
  public function __construct(string $userKey, string $secret)
  {
    $this->userKey = $userKey;
    $this->secret = $secret;
  }

  /**
   * @return void
   * @throws Exception
   */
  public function auth(): void
  {
    $response = $this->http(
      url: self::URL_AUTH,
      body: ['requestToken' => JWT::encode(['user_key' => $this->userKey], $this->secret)]
    );

    $this->authToken = $response['authToken'];
  }

  /**
   * @return array
   * @throws Exception
   */
  public function getCategories(): array
  {
    return $this->request('https://b2b.yugcontract.ua/api/catalog/get-categories');
  }

  /**
   * @param array $categories
   * @return array
   * @throws Exception
   */
  public function getGoods(array $categories = []): array
  {
    return $this->request('https://b2b.yugcontract.ua/api/catalog/get-content-goods', ['cats' => $categories])['goods'];
  }

  /**
   * @param array $categories
   * @return array
   * @throws Exception
   */
  public function getPrice(array $categories = []): array
  {
    return $this->request('https://b2b.yugcontract.ua/api/catalog/get-price', [
      'cats' => $categories,
      'ext_cols' => ['country', 'price_wout_vat', 'descr'],
      'format' => 'json',
      'type' => 'regular',
      'type_prod' => []
    ])['data']['rests']['product'];
  }

  /**
   * @param string $url
   * @param array $body
   * @return array
   * @throws Exception
   */
  private function request(string $url, array $body = []): array
  {
    if (!$this->authToken) {
      $this->auth();
    }
    return $this->http(
      headers: ['Authorization: Bearer ' . $this->authToken],
      body: $body,
      url: $url,
    );
  }

  /**
   * @param string $url
   * @param array $headers
   * @param array $body
   * @return array
   * @throws Exception
   */
  private function http(string $url, array $headers = [], array $body = []): array
  {
    $response = file_get_contents(
      $url,
      false,
      stream_context_create(['http' => [
        'method' => 'POST',
        'header' => array_merge(['Content-Type: application/json'], $headers),
        'content' => json_encode($body),
      ]])
    );

    $response = json_decode($response, true);

    if ($response['status'] !== 'ok' || empty($response['content'])) {
      throw new Exception('YugContract Auth Error: ' . $response['error']);
    }

    return $response['content'];
  }
}