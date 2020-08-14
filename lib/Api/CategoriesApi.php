<?php
/**
 * CategoriesApi
 * PHP version 5
 *
 * @category Class
 * @package  Meli
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 */

/**
 * MELI Markeplace SDK
 *
 * This is a the codebase to generate a SDK for Open Platform Marketplace
 *
 * The version of the OpenAPI document: 3.0.0
 * 
 * Generated by: https://openapi-generator.tech
 * OpenAPI Generator version: 4.3.1
 */

/**
 * NOTE: This class is auto generated by OpenAPI Generator (https://openapi-generator.tech).
 * https://openapi-generator.tech
 * Do not edit the class manually.
 */

namespace Meli\Api;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\MultipartStream;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\RequestOptions;
use Meli\ApiException;
use Meli\Configuration;
use Meli\HeaderSelector;
use Meli\ObjectSerializer;

/**
 * CategoriesApi Class Doc Comment
 *
 * @category Class
 * @package  Meli
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 */
class CategoriesApi
{
    /**
     * @var ClientInterface
     */
    protected $client;

    /**
     * @var Configuration
     */
    protected $config;

    /**
     * @var HeaderSelector
     */
    protected $headerSelector;

    /**
     * @var int Host index
     */
    protected $hostIndex;

    /**
     * @param ClientInterface $client
     * @param Configuration   $config
     * @param HeaderSelector  $selector
     * @param int             $host_index (Optional) host index to select the list of hosts if defined in the OpenAPI spec
     */
    public function __construct(
        ClientInterface $client = null,
        Configuration $config = null,
        HeaderSelector $selector = null,
        $host_index = 0
    ) {
        $this->client = $client ?: new Client();
        $this->config = $config ?: new Configuration();
        $this->headerSelector = $selector ?: new HeaderSelector();
        $this->hostIndex = $host_index;
    }

    /**
     * Set the host index
     *
     * @param  int Host index (required)
     */
    public function setHostIndex($host_index)
    {
        $this->hostIndex = $host_index;
    }

    /**
     * Get the host index
     *
     * @return Host index
     */
    public function getHostIndex()
    {
        return $this->hostIndex;
    }

    /**
     * @return Configuration
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Operation categoriesCategoryIdGet
     *
     * Return by category.
     *
     * @param  string $category_id category_id (required)
     *
     * @throws \Meli\ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return AnyType
     */
    public function categoriesCategoryIdGet($category_id)
    {
        list($response) = $this->categoriesCategoryIdGetWithHttpInfo($category_id);
        return $response;
    }

    /**
     * Operation categoriesCategoryIdGetWithHttpInfo
     *
     * Return by category.
     *
     * @param  string $category_id (required)
     *
     * @throws \Meli\ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return array of AnyType, HTTP status code, HTTP response headers (array of strings)
     */
    public function categoriesCategoryIdGetWithHttpInfo($category_id)
    {
        $request = $this->categoriesCategoryIdGetRequest($category_id);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            }

            $statusCode = $response->getStatusCode();

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    $response->getBody()
                );
            }

            $responseBody = $response->getBody();
            switch($statusCode) {
                case 200:
                    if ('AnyType' === '\SplFileObject') {
                        $content = $responseBody; //stream goes to serializer
                    } else {
                        $content = (string) $responseBody;
                    }

                    return [
                        ObjectSerializer::deserialize($content, 'AnyType', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
            }

            $returnType = 'AnyType';
            $responseBody = $response->getBody();
            if ($returnType === '\SplFileObject') {
                $content = $responseBody; //stream goes to serializer
            } else {
                $content = (string) $responseBody;
            }

            return [
                ObjectSerializer::deserialize($content, $returnType, []),
                $response->getStatusCode(),
                $response->getHeaders()
            ];

        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        'AnyType',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
            }
            throw $e;
        }
    }

    /**
     * Operation categoriesCategoryIdGetAsync
     *
     * Return by category.
     *
     * @param  string $category_id (required)
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function categoriesCategoryIdGetAsync($category_id)
    {
        return $this->categoriesCategoryIdGetAsyncWithHttpInfo($category_id)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation categoriesCategoryIdGetAsyncWithHttpInfo
     *
     * Return by category.
     *
     * @param  string $category_id (required)
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function categoriesCategoryIdGetAsyncWithHttpInfo($category_id)
    {
        $returnType = 'AnyType';
        $request = $this->categoriesCategoryIdGetRequest($category_id);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    $responseBody = $response->getBody();
                    if ($returnType === '\SplFileObject') {
                        $content = $responseBody; //stream goes to serializer
                    } else {
                        $content = (string) $responseBody;
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'categoriesCategoryIdGet'
     *
     * @param  string $category_id (required)
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    protected function categoriesCategoryIdGetRequest($category_id)
    {
        // verify the required parameter 'category_id' is set
        if ($category_id === null || (is_array($category_id) && count($category_id) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $category_id when calling categoriesCategoryIdGet'
            );
        }

        $resourcePath = '/categories/{category_id}';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;



        // path params
        if ($category_id !== null) {
            $resourcePath = str_replace(
                '{' . 'category_id' . '}',
                ObjectSerializer::toPathValue($category_id),
                $resourcePath
            );
        }

        // body params
        $_tempBody = null;

        if ($multipart) {
            $headers = $this->headerSelector->selectHeadersForMultipart(
                ['application/json']
            );
        } else {
            $headers = $this->headerSelector->selectHeaders(
                ['application/json'],
                []
            );
        }

        // for model (json/xml)
        if (isset($_tempBody)) {
            // $_tempBody is the method argument, if present
            if ($headers['Content-Type'] === 'application/json') {
                $httpBody = \GuzzleHttp\json_encode(ObjectSerializer::sanitizeForSerialization($_tempBody));
            } else {
                $httpBody = $_tempBody;
            }
        } elseif (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $multipartContents[] = [
                        'name' => $formParamName,
                        'contents' => $formParamValue
                    ];
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif ($headers['Content-Type'] === 'application/json') {
                $httpBody = \GuzzleHttp\json_encode($formParams);

            } else {
                // for HTTP post (form)
                $httpBody = \GuzzleHttp\Psr7\build_query($formParams);
            }
        }


        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $query = \GuzzleHttp\Psr7\build_query($queryParams);
        return new Request(
            'GET',
            $this->config->getHost() . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation sitesSiteIdCategoriesGet
     *
     * Return a categories by site.
     *
     * @param  string $site_id site_id (required)
     *
     * @throws \Meli\ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return AnyType
     */
    public function sitesSiteIdCategoriesGet($site_id)
    {
        list($response) = $this->sitesSiteIdCategoriesGetWithHttpInfo($site_id);
        return $response;
    }

    /**
     * Operation sitesSiteIdCategoriesGetWithHttpInfo
     *
     * Return a categories by site.
     *
     * @param  string $site_id (required)
     *
     * @throws \Meli\ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return array of AnyType, HTTP status code, HTTP response headers (array of strings)
     */
    public function sitesSiteIdCategoriesGetWithHttpInfo($site_id)
    {
        $request = $this->sitesSiteIdCategoriesGetRequest($site_id);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            }

            $statusCode = $response->getStatusCode();

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    $response->getBody()
                );
            }

            $responseBody = $response->getBody();
            switch($statusCode) {
                case 200:
                    if ('AnyType' === '\SplFileObject') {
                        $content = $responseBody; //stream goes to serializer
                    } else {
                        $content = (string) $responseBody;
                    }

                    return [
                        ObjectSerializer::deserialize($content, 'AnyType', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
            }

            $returnType = 'AnyType';
            $responseBody = $response->getBody();
            if ($returnType === '\SplFileObject') {
                $content = $responseBody; //stream goes to serializer
            } else {
                $content = (string) $responseBody;
            }

            return [
                ObjectSerializer::deserialize($content, $returnType, []),
                $response->getStatusCode(),
                $response->getHeaders()
            ];

        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        'AnyType',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
            }
            throw $e;
        }
    }

    /**
     * Operation sitesSiteIdCategoriesGetAsync
     *
     * Return a categories by site.
     *
     * @param  string $site_id (required)
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function sitesSiteIdCategoriesGetAsync($site_id)
    {
        return $this->sitesSiteIdCategoriesGetAsyncWithHttpInfo($site_id)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation sitesSiteIdCategoriesGetAsyncWithHttpInfo
     *
     * Return a categories by site.
     *
     * @param  string $site_id (required)
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function sitesSiteIdCategoriesGetAsyncWithHttpInfo($site_id)
    {
        $returnType = 'AnyType';
        $request = $this->sitesSiteIdCategoriesGetRequest($site_id);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    $responseBody = $response->getBody();
                    if ($returnType === '\SplFileObject') {
                        $content = $responseBody; //stream goes to serializer
                    } else {
                        $content = (string) $responseBody;
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'sitesSiteIdCategoriesGet'
     *
     * @param  string $site_id (required)
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    protected function sitesSiteIdCategoriesGetRequest($site_id)
    {
        // verify the required parameter 'site_id' is set
        if ($site_id === null || (is_array($site_id) && count($site_id) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $site_id when calling sitesSiteIdCategoriesGet'
            );
        }

        $resourcePath = '/sites/{site_id}/categories';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;



        // path params
        if ($site_id !== null) {
            $resourcePath = str_replace(
                '{' . 'site_id' . '}',
                ObjectSerializer::toPathValue($site_id),
                $resourcePath
            );
        }

        // body params
        $_tempBody = null;

        if ($multipart) {
            $headers = $this->headerSelector->selectHeadersForMultipart(
                ['application/json']
            );
        } else {
            $headers = $this->headerSelector->selectHeaders(
                ['application/json'],
                []
            );
        }

        // for model (json/xml)
        if (isset($_tempBody)) {
            // $_tempBody is the method argument, if present
            if ($headers['Content-Type'] === 'application/json') {
                $httpBody = \GuzzleHttp\json_encode(ObjectSerializer::sanitizeForSerialization($_tempBody));
            } else {
                $httpBody = $_tempBody;
            }
        } elseif (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $multipartContents[] = [
                        'name' => $formParamName,
                        'contents' => $formParamValue
                    ];
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif ($headers['Content-Type'] === 'application/json') {
                $httpBody = \GuzzleHttp\json_encode($formParams);

            } else {
                // for HTTP post (form)
                $httpBody = \GuzzleHttp\Psr7\build_query($formParams);
            }
        }


        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $query = \GuzzleHttp\Psr7\build_query($queryParams);
        return new Request(
            'GET',
            $this->config->getHost() . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation sitesSiteIdDomainDiscoverySearchGet
     *
     * Predictor
     *
     * @param  string $site_id site_id (required)
     * @param  string $q q (required)
     * @param  string $limit limit (required)
     *
     * @throws \Meli\ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return AnyType
     */
    public function sitesSiteIdDomainDiscoverySearchGet($site_id, $q, $limit)
    {
        list($response) = $this->sitesSiteIdDomainDiscoverySearchGetWithHttpInfo($site_id, $q, $limit);
        return $response;
    }

    /**
     * Operation sitesSiteIdDomainDiscoverySearchGetWithHttpInfo
     *
     * Predictor
     *
     * @param  string $site_id (required)
     * @param  string $q (required)
     * @param  string $limit (required)
     *
     * @throws \Meli\ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return array of AnyType, HTTP status code, HTTP response headers (array of strings)
     */
    public function sitesSiteIdDomainDiscoverySearchGetWithHttpInfo($site_id, $q, $limit)
    {
        $request = $this->sitesSiteIdDomainDiscoverySearchGetRequest($site_id, $q, $limit);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            }

            $statusCode = $response->getStatusCode();

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    $response->getBody()
                );
            }

            $responseBody = $response->getBody();
            switch($statusCode) {
                case 200:
                    if ('AnyType' === '\SplFileObject') {
                        $content = $responseBody; //stream goes to serializer
                    } else {
                        $content = (string) $responseBody;
                    }

                    return [
                        ObjectSerializer::deserialize($content, 'AnyType', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
            }

            $returnType = 'AnyType';
            $responseBody = $response->getBody();
            if ($returnType === '\SplFileObject') {
                $content = $responseBody; //stream goes to serializer
            } else {
                $content = (string) $responseBody;
            }

            return [
                ObjectSerializer::deserialize($content, $returnType, []),
                $response->getStatusCode(),
                $response->getHeaders()
            ];

        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        'AnyType',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
            }
            throw $e;
        }
    }

    /**
     * Operation sitesSiteIdDomainDiscoverySearchGetAsync
     *
     * Predictor
     *
     * @param  string $site_id (required)
     * @param  string $q (required)
     * @param  string $limit (required)
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function sitesSiteIdDomainDiscoverySearchGetAsync($site_id, $q, $limit)
    {
        return $this->sitesSiteIdDomainDiscoverySearchGetAsyncWithHttpInfo($site_id, $q, $limit)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation sitesSiteIdDomainDiscoverySearchGetAsyncWithHttpInfo
     *
     * Predictor
     *
     * @param  string $site_id (required)
     * @param  string $q (required)
     * @param  string $limit (required)
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function sitesSiteIdDomainDiscoverySearchGetAsyncWithHttpInfo($site_id, $q, $limit)
    {
        $returnType = 'AnyType';
        $request = $this->sitesSiteIdDomainDiscoverySearchGetRequest($site_id, $q, $limit);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    $responseBody = $response->getBody();
                    if ($returnType === '\SplFileObject') {
                        $content = $responseBody; //stream goes to serializer
                    } else {
                        $content = (string) $responseBody;
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'sitesSiteIdDomainDiscoverySearchGet'
     *
     * @param  string $site_id (required)
     * @param  string $q (required)
     * @param  string $limit (required)
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    protected function sitesSiteIdDomainDiscoverySearchGetRequest($site_id, $q, $limit)
    {
        // verify the required parameter 'site_id' is set
        if ($site_id === null || (is_array($site_id) && count($site_id) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $site_id when calling sitesSiteIdDomainDiscoverySearchGet'
            );
        }
        // verify the required parameter 'q' is set
        if ($q === null || (is_array($q) && count($q) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $q when calling sitesSiteIdDomainDiscoverySearchGet'
            );
        }
        // verify the required parameter 'limit' is set
        if ($limit === null || (is_array($limit) && count($limit) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $limit when calling sitesSiteIdDomainDiscoverySearchGet'
            );
        }

        $resourcePath = '/sites/{site_id}/domain_discovery/search';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;

        // query params
        if ($q !== null) {
            if('form' === 'form' && is_array($q)) {
                foreach($q as $key => $value) {
                    $queryParams[$key] = $value;
                }
            }
            else {
                $queryParams['q'] = $q;
            }
        }
        // query params
        if ($limit !== null) {
            if('form' === 'form' && is_array($limit)) {
                foreach($limit as $key => $value) {
                    $queryParams[$key] = $value;
                }
            }
            else {
                $queryParams['limit'] = $limit;
            }
        }


        // path params
        if ($site_id !== null) {
            $resourcePath = str_replace(
                '{' . 'site_id' . '}',
                ObjectSerializer::toPathValue($site_id),
                $resourcePath
            );
        }

        // body params
        $_tempBody = null;

        if ($multipart) {
            $headers = $this->headerSelector->selectHeadersForMultipart(
                ['application/json']
            );
        } else {
            $headers = $this->headerSelector->selectHeaders(
                ['application/json'],
                []
            );
        }

        // for model (json/xml)
        if (isset($_tempBody)) {
            // $_tempBody is the method argument, if present
            if ($headers['Content-Type'] === 'application/json') {
                $httpBody = \GuzzleHttp\json_encode(ObjectSerializer::sanitizeForSerialization($_tempBody));
            } else {
                $httpBody = $_tempBody;
            }
        } elseif (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $multipartContents[] = [
                        'name' => $formParamName,
                        'contents' => $formParamValue
                    ];
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif ($headers['Content-Type'] === 'application/json') {
                $httpBody = \GuzzleHttp\json_encode($formParams);

            } else {
                // for HTTP post (form)
                $httpBody = \GuzzleHttp\Psr7\build_query($formParams);
            }
        }


        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $query = \GuzzleHttp\Psr7\build_query($queryParams);
        return new Request(
            'GET',
            $this->config->getHost() . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Create http client option
     *
     * @throws \RuntimeException on file opening failure
     * @return array of http client options
     */
    protected function createHttpClientOption()
    {
        $options = [];
        if ($this->config->getDebug()) {
            $options[RequestOptions::DEBUG] = fopen($this->config->getDebugFile(), 'a');
            if (!$options[RequestOptions::DEBUG]) {
                throw new \RuntimeException('Failed to open the debug file: ' . $this->config->getDebugFile());
            }
        }

        return $options;
    }
}
