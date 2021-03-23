# Meli\OAuth20Api

All URIs are relative to *https://api.mercadolibre.com*

Method | HTTP request | Description
------------- | ------------- | -------------
[**auth**](OAuth20Api.md#auth) | **GET** /authorization | Authentication Endpoint
[**getToken**](OAuth20Api.md#getToken) | **POST** /oauth/token | Request Access Token



## auth

> auth($response_type, $client_id, $redirect_uri)

Authentication Endpoint

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


$apiInstance = new Meli\Api\OAuth20Api(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);
$response_type = 'code'; // string | 
$client_id = 'client_id_example'; // string | 
$redirect_uri = 'redirect_uri_example'; // string | 

try {
    $apiInstance->auth($response_type, $client_id, $redirect_uri);
} catch (Exception $e) {
    echo 'Exception when calling OAuth20Api->auth: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters


Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **response_type** | **string**|  | [default to &#39;code&#39;]
 **client_id** | **string**|  |
 **redirect_uri** | **string**|  |

### Return type

void (empty response body)

### Authorization

No authorization required

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints)
[[Back to Model list]](../../README.md#documentation-for-models)
[[Back to README]](../../README.md)


## getToken

> object getToken($grant_type, $client_id, $client_secret, $redirect_uri, $code, $refresh_token)

Request Access Token

Partner makes a request to the token endpoint by adding the following parameters described below

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


$apiInstance = new Meli\Api\OAuth20Api(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);
$grant_type = 'grant_type_example'; // string | 
$client_id = 'client_id_example'; // string | 
$client_secret = 'client_secret_example'; // string | 
$redirect_uri = 'redirect_uri_example'; // string | 
$code = 'code_example'; // string | 
$refresh_token = 'refresh_token_example'; // string | 

try {
    $result = $apiInstance->getToken($grant_type, $client_id, $client_secret, $redirect_uri, $code, $refresh_token);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling OAuth20Api->getToken: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters


Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **grant_type** | **string**|  | [optional]
 **client_id** | **string**|  | [optional]
 **client_secret** | **string**|  | [optional]
 **redirect_uri** | **string**|  | [optional]
 **code** | **string**|  | [optional]
 **refresh_token** | **string**|  | [optional]

### Return type

**object**

### Authorization

No authorization required

### HTTP request headers

- **Content-Type**: application/x-www-form-urlencoded
- **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints)
[[Back to Model list]](../../README.md#documentation-for-models)
[[Back to README]](../../README.md)

