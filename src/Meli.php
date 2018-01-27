<?php

namespace Meli;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Exception\TooManyRedirectsException;
use \InvalidArgumentException;
use \Exception;

final class Meli implements MeliRequestInterface {
    const VERSION = '2.0.0';

    /** @var string is a main URL to access the Meli API's. */
    private $api_root_url = 'https://api.mercadolibre.com/';

    /** @var string is an endpoint to redirect the user for login. */
    private $Oauth_endpoint = '/oauth/token';

    /** @var array is an array containing all auth URLs for each country supported by ML */
    private $auth_url = [
        'MLA' => 'https://auth.mercadolibre.com.ar', // Argentina 
        'MLB' => 'https://auth.mercadolivre.com.br', // Brasil
        'MCO' => 'https://auth.mercadolibre.com.co', // Colombia
        'MCR' => 'https://auth.mercadolibre.com.cr', // Costa Rica
        'MEC' => 'https://auth.mercadolibre.com.ec', // Ecuador
        'MLC' => 'https://auth.mercadolibre.cl', // Chile
        'MLM' => 'https://auth.mercadolibre.com.mx', // Mexico
        'MLU' => 'https://auth.mercadolibre.com.uy', // Uruguay
        'MLV' => 'https://auth.mercadolibre.com.ve', // Venezuela
        'MPA' => 'https://auth.mercadolibre.com.pa', // Panama
        'MPE' => 'https://auth.mercadolibre.com.pe', // Peru
        'MPT' => 'https://auth.mercadolibre.com.pt', // Prtugal
        'MRD' => 'https://auth.mercadolibre.com.do'  // Dominicana
    ];

    /** @var string Current country */
    private $current_country;

    /** @var string the client's ID */
    private $client_id;

    /** @var string the client's secret */
    private $client_secret;

    /** @var string the redirect URI */
    private $redirect_uri;

    /** @var string the access token */
    private $access_token;

    /** @var string the refresh token */
    private $refresh_token;

    /** @var int the expire token time */
    private $expires_in;

    /** @var object the GuzzleClient instance */
    private $client;

    /** @var bool if the JSON decoding must be assoc or not */
    private $json_decode_array = true;

    /** @var array with valid countries supported by MercadoLivre */
    public $supported_countries;

    /**
     * Initiates the object
     *
     * @param string $site a valid site supported by MercadoLivre
     * @param array $credentials containing some credentials like 'client_id', 'client_secret', 'refresh_token' and 'redirect_uri'
     */
    public function __construct($site = '', array $credentials) 
    {
        $keys = ['client_id', 'client_secret', 'access_token', 'refresh_token', 'redirect_uri'];

        foreach ($keys as $k) {
            if (isset($credentials[$k]) && !empty($credentials[$k])) {
                $this->$k = $credentials[$k];
            }
        }

        $this->supported_countries = array_keys($this->auth_url);

        if (!empty($site) && in_array($site, $this->supported_countries)) {
            $this->current_country = $site;
            $this->api_root_url .= 'sites/'.$this->current_country.'/';
        }

        $this->client = new Client([
            'base_uri' => $this->api_root_url,
            'timeout' => 60
        ]);

        return $this;
    }

    /**
     * Return an string with a complete Meli login URL.
     * 
     * @param string $redirect_uri the redirect_uri
     * @param string $country the country for getting the URL, defaults to $current_country, set on constructor
     * 
     * @throws InvalidArgumentException if the $country is not supported
     * 
     * @return string
     */
    public function getAuthUrl($redirect_uri, $country = '') 
    {
        if (empty($this->current_country) && !in_array($country, array_keys($this->auth_url))) {
            throw new InvalidArgumentException('This country is not supported!');
        }

        $this->redirect_uri = $redirect_uri;
        $params = [
            'client_id' => $this->client_id, 
            'response_type' => 'code', 
            'redirect_uri' => $redirect_uri
        ];

        if (!empty($this->current_country)) {
            $country = $this->current_country;
        }

        $auth_url = $this->auth_url[$country];

        $auth_uri = $auth_url.'/authorization?'.http_build_query($params);

        return $auth_uri;
    }

    /**
     * Executes a POST Request to authorize the application and receive an AccessToken.
     * 
     * @param string $code received from MercadoLivre after user's authorization
     * @param string $redirect_uri the redirect_uri, defaults to what was set on constructor
     * 
     * @throws InvalidArgumentException if $redirect_uri and $this->redirect_uri are empty
     * @throws MeliException if the request was not successful or could not find the access_token within response
     * 
     * @return void
     */
    public function authorize($code, $redirect_uri = '')
    {
        if (empty($this->redirect_uri) && empty($redirect_uri)) {
            throw new InvalidArgumentException('You must pass a valid redirect_uri!');
        }

        if (!empty($redirect_uri)) {
            $this->redirect_uri = $redirect_uri;
        }

        $body = array(
            'grant_type' => 'authorization_code', 
            'client_id' => $this->client_id, 
            'client_secret' => $this->client_secret, 
            'code' => $code, 
            'redirect_uri' => $this->redirect_uri
        );

        $response = $this->request('POST', $this->Oauth_endpoint, ['query' => $body]);

        if ($response['status'] !== 200) {
            throw new MeliException('Could not get the access_token!', $response);
        }

        if (!isset($response['body']['access_token'])) {
            throw new MeliException('Could not find access_token within response!', $response);
        }

        $this->access_token = $response['body']['access_token'];

        if (isset($response['body']['expires_in'])) {
            $this->expires_in = time() + intval($response['body']['expires_in']);
        } else {
            $this->expires_in = time() + 21600;
        }

        if (isset($request['body']['refresh_token'])) {
            $this->refresh_token = $request['body']['refresh_token'];
        }
    }

    /**
     * Execute a POST Request to refresh the token
     * 
     * @throws InvalidArgumentException if the client ID, client secret or refresh_token are empty
     * @throws MeliException if the request was not successful or could not find the access_token within response
     * 
     * @return void
     */
    public function refreshAccessToken() 
    {
        $keys = ['client_id', 'client_secret', 'refresh_token'];

        foreach ($keys as $k) {
            if (empty($this->$k)) {
                throw new InvalidArgumentException("{$k} is empty!");
            }
        }

        $body = [
            'grant_type' => 'refresh_token', 
            'client_id' => $this->client_id, 
            'client_secret' => $this->client_secret, 
            'refresh_token' => $this->refresh_token
         ];
    
        $response = $this->request('POST', $this->Oauth_endpoint, ['query' => $body], false);

        if ($response['status'] == 200) {             
            throw new MeliException('Could not refresht the token!', $response);
        }

        if (!isset($response['body']['access_token'])) {
            throw new MeliException('Could not find access_token within response!', $response);
        }

        $this->access_token = $response['body']['access_token'];

        if (isset($response['body']['expires_in'])) {
            $this->expires_in = time() + intval($response['body']['expires_in']);
        } else {
            $this->expires_in = time() + 21600;
        }

        if (isset($response['body']['refresh_token'])) {
            $this->refresh_token = $response['body']['refresh_token'];
        }
    }

    /**
     * Execute a GET Request
     * 
     * @param string $uri the URI
     * @param array $data the data, as GuzzleHttp needs
     * @param bool $append_access_token if must append the access_token in the URL or not
     * 
     * @return array
     */
    public function get($uri, $data = [], $append_access_token = true) {
        return $this->request('GET', $uri, $data, $append_access_token);
    }

    /**
     * Execute a POST Request
     * 
     * @param string $uri the URI
     * @param array $data the data, as GuzzleHttp needs
     * @param bool $append_access_token if must append the access_token in the URL or not
     * 
     * @return array
     */
    public function post($uri, $data = [], $append_access_token = true) {
        return $this->request('POST', $uri, ['json' => $data], $append_access_token);
    }

    /**
     * Execute a PUT Request
     * 
     * @param string $uri the URI
     * @param array $data the data, as GuzzleHttp needs
     * @param bool $append_access_token if must append the access_token in the URL or not
     * 
     * @return array
     */
    public function put($uri, $data = [], $append_access_token = true) {
        return $this->request('PUT', $uri, ['json' => $data], $append_access_token);
    }

    /**
     * Execute a DELETE Request
     * 
     * @param string $uri the URI
     * @param array $data the data, as GuzzleHttp needs
     * @param bool $append_access_token if must append the access_token in the URL or not
     * 
     * @return array
     */
    public function delete($uri, $data = [], $append_access_token = true) {
        return $this->request('DELETE', $uri, ['query' => $data], $append_access_token);
    }

    /**
     * Execute a OPTION Request
     * 
     * @param string $uri the URI
     * @param array $data the data, as GuzzleHttp needs
     * @param bool $append_access_token if must append the access_token in the URL or not
     * 
     * @return array
     */
    public function options($uri, $data = [], $append_access_token = true) {
        return $this->request('OPTION', $uri, ['query' => $data], $append_access_token);
    }

    /**
     * Execute any request
     * 
     * @param string $method the HTTP Method
     * @param string $uri the URI
     * @param array $data the data, as GuzzleHttp needs
     * @param bool $append_access_token if must append the access_token in the URL or not
     * 
     * @return array
     */
    public function execute($method, $uri, array $data = [], $append_access_token = true) {
        return $this->request($method, $uri, $data, $append_access_token);
    }

    /**
    * Check if credentials are valid also automatically tries to refresh if there is refresh_token
    * 
    * @throws InvalidArgumentException if the access_token is empty
    * @throws MeliException if the request was not successful or could not find the access_token within response when refreshing
    * 
    * @return boolean
    */
    private function checkCredentials()
    {
        if (empty($this->access_token)) {
            throw new InvalidArgumentException('AccessToken not available!');
        }

        if (is_null($this->expires_in)) {
            return false;
        }

        if ($this->expires_in > time()) {
            return true;
        }

        if (is_null($this->refresh_token)) {
            return false;
        }

        $response = $this->refreshAccessToken();

        return true;
    }

    /**
    * Make any request using GuzzleHttp
    * 
    * @param string $method the HTTP Method
    * @param array $data the data to be sent, checks GuzzleHttp docs for more info
    * @param bool $append_access_token if must or not append $access_token in the request
    * 
    * @return array containing the body, HTTP Status Code and headers
    */
    public function request($method, $uri, array $data = [], $append_access_token = true)
    {
        try {
            if ($append_access_token) {
                $this->checkCredentials();

                if (!isset($data['query'])) {
                    $data['query'] = [];
                }

                if (is_array($data['query'])) {
                    $data['query']['access_token'] = $this->access_token;
                } else if (is_string($data['query'])) {
                    $data['query'] .= "&access_token={$this->access_token}";
                }
            }

            $response = $this->client->request($method, $uri, $data);
            $body = (string) $response->getBody();

            if (!is_null(json_decode($body))) {
                $body = json_decode($body, $this->json_decode_array);
            }

            $return = [
                'body' => $body,
                'status' => $response->getStatusCode(),
                'headers' => $response->getHeaders(),
            ];
        } catch (ClientException $e) {
            $response = $e->getResponse();
            $contents = $response->getBody()->getContents();

            if (is_string($contents)) {
                $contents = json_decode($contents, true);
            }

            $return = [
                'status' => $response->getStatusCode(),
                'headers' => $response->getHeaders(),
                'body' => $contents,
            ];
        } catch (RequestException $e) {
            $return = [
                'request' => Psr7\str($e->getRequest()),
            ];

            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $contents = $response->getBody()->getContents();

                if (is_string($contents)) {
                    $contents = json_decode($contents, true);
                }

                $return['headers'] = $response->getHeaders();
                $return['body'] = $contents;
                $return['status'] = $response->getStatusCode();
            }
        } catch (InvalidArgumentException $e) {
            $return = [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ];
        } catch (Exception $e) {
            $return = [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ];
        }

        if (!isset($return['body'])) {
            $return['body'] = [];
        }

        if (!isset($return['status'])) {
            $return['status'] = 0;
        }

        return $return;
    }

    /**
    * Gets a user
    * 
    * @param string $id the user id
    * 
    * @throws InvalidArgumentException if the $id is null
    * @throws MeliException if the request was not successful
    * 
    * @return object instance of User
    */
    public function getUser($id)
    {
        return (new User($this))->getUser($id);
    }

    /**
    * Gets the user itself
    * 
    * @throws MeliException if the request was not successful
    * 
    * @return object instance of User
    */
    public function getMe()
    {
        return (new User($this))->getMe();
    }

    /**
    * Get categories for a given country
    * 
    * @param string $country the country supported by MercadoLivre
    * @param bool $fully_load if must also request for fully data of every category
    * 
    * @throws InvalidArgumentException if the $country is not supported or both $country and $meli->current_country are empty
    * @throws MeliException if the $request was not successful
    * 
    * @return array of instances of Category
    */
    public function getCategories($country = '', $fully_load = true)
    {
        return (new Category($this))->getCategories($country);
    }

    /**
     * Get a category
     * 
     * @param string $id the category id
     * 
     * @throws InvalidArgumentException if the $id is null
     * @throws MeliException if the request was not successful
     * 
     * @return object instance of Category
     */
    public function getCategory($id)
    {
        return (new Category($this))->getCategory($id);
    }

    /**
    * Get a product
    */
    public function getItem()
    {
        // Some cool code will born here!
    }
}
