# Meli\RestClientApi

All URIs are relative to *https://api.mercadolibre.com*

Method | HTTP request | Description
------------- | ------------- | -------------
[**resourceDelete**](RestClientApi.md#resourceDelete) | **DELETE** /{resource} | Resource path DELETE
[**resourceGet**](RestClientApi.md#resourceGet) | **GET** /{resource} | Resource path GET
[**resourcePost**](RestClientApi.md#resourcePost) | **POST** /{resource} | Resourse path POST
[**resourcePut**](RestClientApi.md#resourcePut) | **PUT** /{resource} | Resourse path PUT



## resourceDelete

> object resourceDelete($resource, $access_token)

Resource path DELETE

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
    $result = $apiInstance->resourceDelete($resource, $access_token);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling RestClientApi->resourceDelete: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters


Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **resource** | **string**|  |
 **access_token** | **string**|  |

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


## resourceGet

> object resourceGet($resource, $access_token)

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
    $result = $apiInstance->resourceGet($resource, $access_token);
    print_r($result);
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

**object**

### Authorization

No authorization required

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints)
[[Back to Model list]](../../README.md#documentation-for-models)
[[Back to README]](../../README.md)


## resourcePost

> object resourcePost($resource, $access_token, $body)

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
    $result = $apiInstance->resourcePost($resource, $access_token, $body);
    print_r($result);
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

**object**

### Authorization

No authorization required

### HTTP request headers

- **Content-Type**: application/json
- **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints)
[[Back to Model list]](../../README.md#documentation-for-models)
[[Back to README]](../../README.md)


## resourcePut

> object resourcePut($resource, $access_token, $body)

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
    $result = $apiInstance->resourcePut($resource, $access_token, $body);
    print_r($result);
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

**object**

### Authorization

No authorization required

### HTTP request headers

- **Content-Type**: application/json
- **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints)
[[Back to Model list]](../../README.md#documentation-for-models)
[[Back to README]](../../README.md)

