# Meli\RestClientApi

All URIs are relative to *https://api.mercadolibre.com*

Method | HTTP request | Description
------------- | ------------- | -------------
[**resourceGet**](RestClientApi.md#resourceGet) | **GET** /{resource} | Resource path GET
[**resourcePost**](RestClientApi.md#resourcePost) | **POST** /{resource} | Resourse path POST
[**resourcePut**](RestClientApi.md#resourcePut) | **PUT** /{resource} | Resourse path PUT



## resourceGet

> resourceGet($resource, $access_token)

Resource path GET

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


$apiInstance = new Meli\Api\RestClientApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);
$resource = 'resource_example'; // string | 
$access_token = 'access_token_example'; // string | 

try {
    $apiInstance->resourceGet($resource, $access_token);
} catch (Exception $e) {
    echo 'Exception when calling RestClientApi->resourceGet: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters


Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **resource** | **string**|  |
 **access_token** | **string**|  |

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


## resourcePost

> resourcePost($resource, $access_token, $body)

Resourse path POST

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


$apiInstance = new Meli\Api\RestClientApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);
$resource = 'resource_example'; // string | 
$access_token = 'access_token_example'; // string | 
$body = new \stdClass; // object | 

try {
    $apiInstance->resourcePost($resource, $access_token, $body);
} catch (Exception $e) {
    echo 'Exception when calling RestClientApi->resourcePost: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters


Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **resource** | **string**|  |
 **access_token** | **string**|  |
 **body** | **object**|  |

### Return type

void (empty response body)

### Authorization

No authorization required

### HTTP request headers

- **Content-Type**: application/json
- **Accept**: Not defined

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints)
[[Back to Model list]](../../README.md#documentation-for-models)
[[Back to README]](../../README.md)


## resourcePut

> resourcePut($resource, $access_token, $body)

Resourse path PUT

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


$apiInstance = new Meli\Api\RestClientApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);
$resource = 'resource_example'; // string | 
$access_token = 'access_token_example'; // string | 
$body = new \stdClass; // object | 

try {
    $apiInstance->resourcePut($resource, $access_token, $body);
} catch (Exception $e) {
    echo 'Exception when calling RestClientApi->resourcePut: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters


Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **resource** | **string**|  |
 **access_token** | **string**|  |
 **body** | **object**|  |

### Return type

void (empty response body)

### Authorization

No authorization required

### HTTP request headers

- **Content-Type**: application/json
- **Accept**: Not defined

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints)
[[Back to Model list]](../../README.md#documentation-for-models)
[[Back to README]](../../README.md)

