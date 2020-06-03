# Meli\ItemsApi

All URIs are relative to *https://api.mercadolibre.com*

Method | HTTP request | Description
------------- | ------------- | -------------
[**itemsIdGet**](ItemsApi.md#itemsIdGet) | **GET** /items/{id} | Return a Item.
[**itemsIdPut**](ItemsApi.md#itemsIdPut) | **PUT** /items/{id} | Update a Item.
[**itemsPost**](ItemsApi.md#itemsPost) | **POST** /items | Create a Item.



## itemsIdGet

> itemsIdGet($id)

Return a Item.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


$apiInstance = new Meli\Api\ItemsApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);
$id = 'id_example'; // string | 

try {
    $apiInstance->itemsIdGet($id);
} catch (Exception $e) {
    echo 'Exception when calling ItemsApi->itemsIdGet: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters


Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **id** | **string**|  |

### Return type

void (empty response body)

### Authorization

No authorization required

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: Not defined

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints)
[[Back to Model list]](../../README.md#documentation-for-models)
[[Back to README]](../../README.md)


## itemsIdPut

> itemsIdPut($id, $access_token, $item)

Update a Item.

### Example

```php
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
$id = 'id_example'; // string | 
$access_token = 'access_token_example'; // string | 
$item = new \Meli\Model\Item(); // \Meli\Model\Item | 

try {
    $apiInstance->itemsIdPut($id, $access_token, $item);
} catch (Exception $e) {
    echo 'Exception when calling ItemsApi->itemsIdPut: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters


Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **id** | **string**|  |
 **access_token** | **string**|  |
 **item** | [**\Meli\Model\Item**](../Model/Item.md)|  |

### Return type

void (empty response body)

### Authorization

[oAuth2AuthCode](../../README.md#oAuth2AuthCode)

### HTTP request headers

- **Content-Type**: application/json
- **Accept**: Not defined

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints)
[[Back to Model list]](../../README.md#documentation-for-models)
[[Back to README]](../../README.md)


## itemsPost

> itemsPost($access_token, $item)

Create a Item.

### Example

```php
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
$item = new \Meli\Model\Item(); // \Meli\Model\Item | 

try {
    $apiInstance->itemsPost($access_token, $item);
} catch (Exception $e) {
    echo 'Exception when calling ItemsApi->itemsPost: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters


Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **access_token** | **string**|  |
 **item** | [**\Meli\Model\Item**](../Model/Item.md)|  |

### Return type

void (empty response body)

### Authorization

[oAuth2AuthCode](../../README.md#oAuth2AuthCode)

### HTTP request headers

- **Content-Type**: application/json
- **Accept**: Not defined

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints)
[[Back to Model list]](../../README.md#documentation-for-models)
[[Back to README]](../../README.md)

