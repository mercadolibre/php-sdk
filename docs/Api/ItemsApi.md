# Meli\ItemsApi

All URIs are relative to *https://api.mercadolibre.com*

Method | HTTP request | Description
------------- | ------------- | -------------
[**itemsIdGet**](ItemsApi.md#itemsIdGet) | **GET** /items/{id} | Return a Item.
[**itemsIdPut**](ItemsApi.md#itemsIdPut) | **PUT** /items/{id} | Update a Item.
[**itemsPost**](ItemsApi.md#itemsPost) | **POST** /items | Create a Item.



## itemsIdGet

> object itemsIdGet($id)

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
    $result = $apiInstance->itemsIdGet($id);
    print_r($result);
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

**object**

### Authorization

No authorization required

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints)
[[Back to Model list]](../../README.md#documentation-for-models)
[[Back to README]](../../README.md)


## itemsIdPut

> object itemsIdPut($id, $access_token, $item)

Update a Item.

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
$access_token = 'access_token_example'; // string | 
$item = new \Meli\Model\Item(); // \Meli\Model\Item | 

try {
    $result = $apiInstance->itemsIdPut($id, $access_token, $item);
    print_r($result);
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

**object**

### Authorization

No authorization required

### HTTP request headers

- **Content-Type**: application/json
- **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints)
[[Back to Model list]](../../README.md#documentation-for-models)
[[Back to README]](../../README.md)


## itemsPost

> object itemsPost($access_token, $item)

Create a Item.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


$apiInstance = new Meli\Api\ItemsApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);
$access_token = 'access_token_example'; // string | 
$item = new \Meli\Model\Item(); // \Meli\Model\Item | 

try {
    $result = $apiInstance->itemsPost($access_token, $item);
    print_r($result);
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

**object**

### Authorization

No authorization required

### HTTP request headers

- **Content-Type**: application/json
- **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints)
[[Back to Model list]](../../README.md#documentation-for-models)
[[Back to README]](../../README.md)

