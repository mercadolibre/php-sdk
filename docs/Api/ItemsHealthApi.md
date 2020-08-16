# Meli\ItemsHealthApi

All URIs are relative to *https://api.mercadolibre.com*

Method | HTTP request | Description
------------- | ------------- | -------------
[**itemsIdHealthActionsGet**](ItemsHealthApi.md#itemsIdHealthActionsGet) | **GET** /items/{id}/health/actions | Return item health actions by id.
[**itemsIdHealthGet**](ItemsHealthApi.md#itemsIdHealthGet) | **GET** /items/{id}/health | Return health by id.
[**sitesSiteIdHealthLevelsGet**](ItemsHealthApi.md#sitesSiteIdHealthLevelsGet) | **GET** /sites/{site_id}/health_levels | Return health levels.



## itemsIdHealthActionsGet

> object itemsIdHealthActionsGet($id, $access_token)

Return item health actions by id.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


$apiInstance = new Meli\Api\ItemsHealthApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);
$id = 'id_example'; // string | 
$access_token = 'access_token_example'; // string | 

try {
    $result = $apiInstance->itemsIdHealthActionsGet($id, $access_token);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling ItemsHealthApi->itemsIdHealthActionsGet: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters


Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **id** | **string**|  |
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


## itemsIdHealthGet

> object itemsIdHealthGet($id, $access_token)

Return health by id.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


$apiInstance = new Meli\Api\ItemsHealthApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);
$id = 'id_example'; // string | 
$access_token = 'access_token_example'; // string | 

try {
    $result = $apiInstance->itemsIdHealthGet($id, $access_token);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling ItemsHealthApi->itemsIdHealthGet: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters


Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **id** | **string**|  |
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


## sitesSiteIdHealthLevelsGet

> object sitesSiteIdHealthLevelsGet($site_id)

Return health levels.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


$apiInstance = new Meli\Api\ItemsHealthApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);
$site_id = 'site_id_example'; // string | 

try {
    $result = $apiInstance->sitesSiteIdHealthLevelsGet($site_id);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling ItemsHealthApi->sitesSiteIdHealthLevelsGet: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters


Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **site_id** | **string**|  |

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

