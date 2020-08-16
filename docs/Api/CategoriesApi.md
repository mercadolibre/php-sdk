# Meli\CategoriesApi

All URIs are relative to *https://api.mercadolibre.com*

Method | HTTP request | Description
------------- | ------------- | -------------
[**categoriesCategoryIdGet**](CategoriesApi.md#categoriesCategoryIdGet) | **GET** /categories/{category_id} | Return by category.
[**sitesSiteIdCategoriesGet**](CategoriesApi.md#sitesSiteIdCategoriesGet) | **GET** /sites/{site_id}/categories | Return a categories by site.
[**sitesSiteIdDomainDiscoverySearchGet**](CategoriesApi.md#sitesSiteIdDomainDiscoverySearchGet) | **GET** /sites/{site_id}/domain_discovery/search | Predictor



## categoriesCategoryIdGet

> object categoriesCategoryIdGet($category_id)

Return by category.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


$apiInstance = new Meli\Api\CategoriesApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);
$category_id = 'category_id_example'; // string | 

try {
    $result = $apiInstance->categoriesCategoryIdGet($category_id);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CategoriesApi->categoriesCategoryIdGet: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters


Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **category_id** | **string**|  |

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


## sitesSiteIdCategoriesGet

> object sitesSiteIdCategoriesGet($site_id)

Return a categories by site.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


$apiInstance = new Meli\Api\CategoriesApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);
$site_id = 'site_id_example'; // string | 

try {
    $result = $apiInstance->sitesSiteIdCategoriesGet($site_id);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CategoriesApi->sitesSiteIdCategoriesGet: ', $e->getMessage(), PHP_EOL;
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


## sitesSiteIdDomainDiscoverySearchGet

> object sitesSiteIdDomainDiscoverySearchGet($site_id, $q, $limit)

Predictor

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


$apiInstance = new Meli\Api\CategoriesApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);
$site_id = 'site_id_example'; // string | 
$q = 'q_example'; // string | 
$limit = 'limit_example'; // string | 

try {
    $result = $apiInstance->sitesSiteIdDomainDiscoverySearchGet($site_id, $q, $limit);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CategoriesApi->sitesSiteIdDomainDiscoverySearchGet: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters


Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **site_id** | **string**|  |
 **q** | **string**|  |
 **limit** | **string**|  |

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

