<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: oAuth2AuthCode
$config = Meli\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new Meli\Api\ItemsApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$access_token = 'access_token_example'; // string | 

$item = array (
  'title' => 'Item de test - No Ofertar from SDK PHP',
  'category_id' => 'MLA5991',
  'price' => '350',
  'currency_id' => 'ARS',
  'available_quantity' => '12',
  'buying_mode' => 'buy_it_now',
  'listing_type_id' => 'bronze',
  'condition' => 'new',
  'description' => 'Item de Teste. Mercado Livre SDK',
  'video_id' => 'RXWn6kftTHY',
  'warranty' => '12 month',
  'pictures' => 
  array (
    0 => 
    array (
      'source' => 'https://http2.mlstatic.com/storage/developers-site-cms-admin/openapi/319968615067-mp3.jpg',
    ),
  ),
  'attributes' => 
  array (
    0 => 
    array (
      'id' => 'DATA_STORAGE_CAPACITY',
      'name' => 'Capacidad de almacenamiento de datos',
      'value_id' => NULL,
      'value_name' => '8 GB',
      'value_struct' => 
      array (
        'number' => 8,
        'unit' => 'GB',
      ),
      'values' => 
      array (
        0 => 
        array (
          'id' => NULL,
          'name' => '8 GB',
          'struct' => 
          array (
            'number' => 8,
            'unit' => 'GB',
          ),
        ),
      ),
      'attribute_group_id' => 'OTHERS',
      'attribute_group_name' => 'Otros',
    ),
  ),
  'variations' => 
  array (
    0 => 
    array (
      'price' => 350,
      'attribute_combinations' => 
      array (
        0 => 
        array (
          'name' => 'Color',
          'value_id' => '283165',
          'value_name' => 'Gris',
        ),
      ),
      'available_quantity' => 2,
      'sold_quantity' => 0,
      'picture_ids' => 
      array (
        0 => '882629-MLA40983876214_032020',
      ),
    ),
  ),
);

try {
    $apiInstance->itemsPost($access_token, $item);
} catch (Exception $e) {
    echo 'Exception when calling ItemsApi->itemsPost: ', $e->getMessage(), PHP_EOL;
}
?>