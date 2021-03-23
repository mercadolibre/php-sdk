<?php
// You can use this code in the examples folder
require_once('../vendor/autoload.php');
// Or you can put them in to the main folder
// require_once(__DIR__ . '/vendor/autoload.php');

$apiInstance = new Meli\Api\RestClientApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);
$resource = 'items'; // A resource example like items, search, category, etc.
$access_token = 'access_token_example'; // Your access token.

$body = json_decode('{
  "title": "Item de test - No Ofertar",
  "category_id": "MLA5991",
  "price": "350",
  "currency_id": "ARS",
  "available_quantity": "12",
  "buying_mode": "buy_it_now",
  "listing_type_id": "bronze",
  "condition": "new",
  "description": "Item de Teste. Mercado Livre SDK",
  "video_id": "RXWn6kftTHY",
  "pictures": [
    {
      "source": "https://http2.mlstatic.com/storage/developers-site-cms-admin/openapi/319968615067-mp3.jpg"
    }
  ],
  "attributes": [
    {
      "id": "DATA_STORAGE_CAPACITY",
      "name": "Capacidad de almacenamiento de datos",
      "value_id": null,
      "value_name": "8 GB",
      "value_struct": {
        "number": 8,
        "unit": "GB"
      },
      "values": [
        {
          "id": null,
          "name": "8 GB",
          "struct": {
            "number": 8,
            "unit": "GB"
          }
        }
      ],
      "attribute_group_id": "OTHERS",
      "attribute_group_name": "Otros"
    }
  ],
  "variations": [
    {
      "price": 350,
      "attribute_combinations": [
        {
          "name": "Color",
          "value_id": "283165",
          "value_name": "Gris"
        }
      ],
      "available_quantity": 2,
      "sold_quantity": 0,
      "picture_ids": [
        "882629-MLA40983876214_032020"
      ]
    }
  ]
}');

try {
    $result = $apiInstance->resourcePost($resource, $access_token, $body);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling RestClientApi->resourcePost: ', $e->getMessage(), PHP_EOL;
}
