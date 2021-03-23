![No longer maintained](https://img.shields.io/badge/Maintenance-OFF-red.svg)

### [DEPRECATED] This repository is no longer maintained

> From the first week of April 2021 we will stop maintaining our SDKs.
>
> This project is not functional, the dependencies will not be updated to latest ones.
>
> We recommend you read our [documentation](https://developers.mercadolibre.com).

  <a href="https://developers.mercadolibre.com">
    <img src="https://user-images.githubusercontent.com/1153516/73021269-043c2d80-3e06-11ea-8d0e-6e91441c2900.png" alt="Mercado Libre Developers" width="200"></a>
  </a>

---

<br>
<h1 align="center">
  <a href="https://developers.mercadolibre.com">
    <img src="https://user-images.githubusercontent.com/1153516/29861072-689ec57e-8d3e-11e7-8368-dd923543258f.jpg" alt="Mercado Libre Developers" width="230"></a>
  </a>
  <br><br>
  MercadoLibre's PHP SDK
  <br>
</h1>

<h4 align="center">This is the official PHP SDK for MercadoLibre's Platform.</h4>

## Requirements

PHP 5.5 and later

## Installation & Usage

### Composer

To install the bindings via [Composer](http://getcomposer.org/), add the following to `composer.json`:

```json
{
  "repositories": [
    {
      "type": "vcs",
      "url": "https://github.com/mercadolibre/php-sdk.git"
    }
  ],
  "require": {
    "mercadolibre/php-sdk": "*@dev"
  }
}
```

Then run `composer install`

### Manual Installation

Download the files

Run `composer install`

Include `autoload.php` in your code:

```php
    require_once('/path-to-integration-folder/vendor/autoload.php');
```

## Tests

To run the unit tests:

```bash
composer install
./vendor/bin/phpunit
```

## Usage

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$config = new Meli\Configuration();
$servers = $config->getHostSettings();
// Auth URLs Options by country

// 1:  "https://auth.mercadolibre.com.ar"
// 2:  "https://auth.mercadolivre.com.br"
// 3:  "https://auth.mercadolibre.com.co"
// 4:  "https://auth.mercadolibre.com.mx"
// 5:  "https://auth.mercadolibre.com.uy"
// 6:  "https://auth.mercadolibre.cl"
// 7:  "https://auth.mercadolibre.com.cr"
// 8:  "https://auth.mercadolibre.com.ec"
// 9:  "https://auth.mercadolibre.com.ve"
// 10: "https://auth.mercadolibre.com.pa"
// 11: "https://auth.mercadolibre.com.pe"
// 12: "https://auth.mercadolibre.com.do"
// 13: "https://auth.mercadolibre.com.bo"
// 14: "https://auth.mercadolibre.com.py"

// Use the correct auth URL
$config->setHost($servers[1]["url"]);

// Or Print all URLs
print_r($servers);

// Or Print or Put the following URL in your browser window to obtain authorization:
//
// http://auth.mercadolibre.com.ar/authorization?response_type=code&client_id=$APP_ID&redirect_uri=$YOUR_URL
?>
```

This will give you the url to redirect the user. You need to specify a callback url which will be the one that the user will redirected after a successfull authrization process.

Once the user is redirected to your callback url, you'll receive in the query string, a parameter named code. You'll need this for the second part of the process

## Examples for OAuth - get token

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


$apiInstance = new Meli\Api\OAuth20Api(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);
$grant_type = 'authorization_code';
$client_id = 'client_id_example'; // Your client_id
$client_secret = 'client_secret_example'; // Your client_secret
$redirect_uri = 'redirect_uri_example'; // Your redirect_uri
$code = 'code_example'; // The parameter CODE
$refresh_token = 'refresh_token_example'; // Your refresh_token

try {
    $result = $apiInstance->getToken($grant_type, $client_id, $client_secret, $redirect_uri, $code, $refresh_token);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling OAuth20Api->getToken: ', $e->getMessage(), PHP_EOL;
}
?>
```

## Example using the RestClient with a POST Item

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


$apiInstance = new Meli\Api\RestClientApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);
$resource = 'resource_example'; // string | for example: items
$access_token = 'access_token_example'; // string |
$body = new \stdClass; // object |

try {
    $apiInstance->resourcePost($resource, $access_token, $body);
} catch (Exception $e) {
    echo 'Exception when calling RestClientApi->resourcePost: ', $e->getMessage(), PHP_EOL;
}
?>
```

## Documentation & Important notes

##### The URIs are relative to https://api.mercadolibre.com

##### The Authorization URLs (set the correct country domain): https://auth.mercadolibre.{country_domain}

##### All docs for the library are located [here](https://github.com/mercadolibre/php-sdk/tree/master/docs)

##### Check out our examples codes in the folder [examples](https://github.com/mercadolibre/php-sdk/tree/master/examples)

##### Don’t forget to check out our [developer site](https://developers.mercadolibre.com/)
