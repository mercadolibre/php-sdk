<h1 align="center">
  <a href="http://developers.mercadolibre.com/">
    <img src="https://user-images.githubusercontent.com/1153516/29861072-689ec57e-8d3e-11e7-8368-dd923543258f.jpg" alt="Mercado Libre Developers" width="230"></a>
  </a>
  <br>
  MercadoLibre's PHP SDK
  <br>
</h1>

<h4 align="center">This is the official PHP SDK for MercadoLibre's Platform.<span>[Beta]</span></h4>

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
      "url": "https://github.com/GIT_USER_ID/GIT_REPO_ID.git"
    }
  ],
  "require": {
    "GIT_USER_ID/GIT_REPO_ID": "*@dev"
  }
}
```

Then run `composer install`

### Manual Installation

Download the files and include `autoload.php`:

```php
    require_once('/path/to/OpenAPIClient-php/vendor/autoload.php');
```

## Tests

To run the unit tests:

```bash
composer install
./vendor/bin/phpunit
```

## Getting Started

Please follow the [installation procedure](#installation--usage) and then run the following:

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
    $apiInstance->itemsIdHealthActionsGet($id, $access_token);
} catch (Exception $e) {
    echo 'Exception when calling ItemsHealthApi->itemsIdHealthActionsGet: ', $e->getMessage(), PHP_EOL;
}
?>
```

## Documentation for API Endpoints

All URIs are relative to *https://api.mercadolibre.com*

Class | Method | HTTP request | Description
------------ | ------------- | ------------- | -------------
*CategoriesApi* | [**categoriesCategoryIdGet**](docs/Api/CategoriesApi.md#categoriescategoryidget) | **GET** /categories/{category_id} | Return by category.
*CategoriesApi* | [**sitesSiteIdCategoriesGet**](docs/Api/CategoriesApi.md#sitessiteidcategoriesget) | **GET** /sites/{site_id}/categories | Return a categories by site.
*CategoriesApi* | [**sitesSiteIdDomainDiscoverySearchGet**](docs/Api/CategoriesApi.md#sitessiteiddomaindiscoverysearchget) | **GET** /sites/{site_id}/domain_discovery/search | Predictor
*ItemsApi* | [**itemsIdGet**](docs/Api/ItemsApi.md#itemsidget) | **GET** /items/{id} | Return a Item.
*ItemsApi* | [**itemsIdPut**](docs/Api/ItemsApi.md#itemsidput) | **PUT** /items/{id} | Update a Item.
*ItemsApi* | [**itemsPost**](docs/Api/ItemsApi.md#itemspost) | **POST** /items | Create a Item.
*ItemsHealthApi* | [**itemsIdHealthActionsGet**](docs/Api/ItemsHealthApi.md#itemsidhealthactionsget) | **GET** /items/{id}/health/actions | Return item health actions by id.
*ItemsHealthApi* | [**itemsIdHealthGet**](docs/Api/ItemsHealthApi.md#itemsidhealthget) | **GET** /items/{id}/health | Return health by id.
*ItemsHealthApi* | [**sitesSiteIdHealthLevelsGet**](docs/Api/ItemsHealthApi.md#sitessiteidhealthlevelsget) | **GET** /sites/{site_id}/health_levels | Return health levels.
*OAuth20Api* | [**auth**](docs/Api/OAuth20Api.md#auth) | **GET** /authorization | Authentication Endpoint
*OAuth20Api* | [**getToken**](docs/Api/OAuth20Api.md#gettoken) | **POST** /oauth/token | Request Access Token


## Documentation For Models

 - [Attributes](docs/Model/Attributes.md)
 - [AttributesValueStruct](docs/Model/AttributesValueStruct.md)
 - [AttributesValues](docs/Model/AttributesValues.md)
 - [Item](docs/Model/Item.md)
 - [ItemPictures](docs/Model/ItemPictures.md)
 - [Token](docs/Model/Token.md)
 - [Variations](docs/Model/Variations.md)
 - [VariationsAttributeCombinations](docs/Model/VariationsAttributeCombinations.md)


## Documentation For Authorization



## oAuth2AuthCode


- **Type**: OAuth
- **Flow**: accessCode
- **Authorization URL**: https://auth.mercadolibre.com.ar/authorization
- **Scopes**: 
- **read**: Grants read access
- **write**: Grants write access
- **offline_access**: Grants read and write access, and adds the possibility to get a refresh token and stay authenticated as the user.


## Author



