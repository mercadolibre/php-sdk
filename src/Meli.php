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

final class Meli {
    const VERSION = '2.0.0';

    /**
     * @var $api_root_url is a main URL to access the Meli API's.
     * @var $Oauth_endpoint is an endpoint to redirect the user for login.
     * @var $auth_url is an array containing all auth URLs for each country supported by ML
     */
    private $api_root_url = 'https://api.mercadolibre.com/';
    private $Oauth_endpoint = '/oauth/token';
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

    /**
    * Current country
    */
    private $current_country = null;

    /**
    * Client's ID
    */
    private $client_id;

    /**
    * Client's Secret
    */
    private $client_secret;

    /**
    * Redirect URI
    */
    private $redirect_uri;

    /**
    * Access Token
    */
    private $access_token;

    /**
    * Refresh Token
    */
    private $refresh_token;

    /**
    * Expire token time
    */
    private $expires_in;

    /**
    * $client is the GuzzleClient instance
    */
    private $client;

    /**
    * $json_decode_array if the JSON decoding must be assoc or not
    */
    private $json_decode_array = true;

    /**
     * Constructor method. Set all variables to connect in Meli
     *
     * @param string $site
     * @param array $credentials
     * @return void
     */
    public function __construct($site = '', array $credentials) 
    {
        $keys = ['client_id', 'client_secret', 'access_token', 'refresh_token', 'redirect_uri'];

        foreach ($keys as $k) {
            if (isset($credentials[$k]) && !empty($credentials[$k])) {
                $this->$k = $credentials[$k];
            }
        }

        if (!empty($site) && in_array($site, array_keys($this->auth_url))) {
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
     * Return an string with a complete Meli login url.
     * NOTE: You can modify the $auth_url to change the language of login
     * 
     * @param string $redirect_uri
     * @param string $country
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
     * @param string $code
     * @param string $redirect_uri
     * @return array
     */
    public function authorize($code, $redirect_uri)
    {
        $this->redirect_uri = $redirect_uri;

        $body = array(
            'grant_type' => 'authorization_code', 
            'client_id' => $this->client_id, 
            'client_secret' => $this->client_secret, 
            'code' => $code, 
            'redirect_uri' => $this->redirect_uri
        );

        $response = $this->request('POST', $this->Oauth_endpoint, ['query' => $body]);

        if ($response['status'] == 200) {
            if (!isset($response['body']['access_token'])) {
                throw new Exception('Could not find access_token within response!');
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

            return $request;
        } else {
            return $request;
        }
    }

    /**
     * Execute a POST Request to refresh the token
     * 
     * @return array
     */
    public function refreshAccessToken() 
    {
        if (!empty($this->refresh_token)) {
             $body = [
                'grant_type' => 'refresh_token', 
                'client_id' => $this->client_id, 
                'client_secret' => $this->client_secret, 
                'refresh_token' => $this->refresh_token
             ];
        
            $response = $this->request('POST', $this->Oauth_endpoint, ['query' => $body], false);

            if ($response['status'] == 200) {             
                if (!isset($response['body']['access_token'])) {
                    throw new Exception('Could not find access_token within response!');
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

                return $response;
            } else {
                return $response;
            }   
        } else {
            $result = array(
                'error' => 'Offline-Access is not allowed.',
                'status'  => null
            );

            return $result;
        }        
    }

    /**
     * Execute a GET Request
     * 
     * @param string $uri
     * @param array $data
     * @param boolean $append_access_token
     * @return mixed
     */
    public function get($uri, $data = [], $append_access_token = true) {
        return $this->request('GET', $uri, $data, $append_access_token);
    }

    /**
     * Execute a POST Request
     * 
     * @param string $uri
     * @param array $data
     * @param boolean $append_access_token
     * @return mixed
     */
    public function post($uri, $data = [], $append_access_token = true) {
        return $this->request('POST', $uri, ['json' => $data], $append_access_token);
    }

    /**
     * Execute a PUT Request
     * 
     * @param string $uri
     * @param array $data
     * @param boolean $append_access_token
     * @return mixed
     */
    public function put($uri, $data = [], $append_access_token = true) {
        return $this->request('PUT', $uri, ['json' => $data], $append_access_token);
    }

    /**
     * Execute a DELETE Request
     * 
     * @param string $uri
     * @param array $data
     * @param boolean $append_access_token
     * @return mixed
     */
    public function delete($uri, $data = [], $append_access_token = true) {
        return $this->request('DELETE', $uri, ['query' => $data], $append_access_token);
    }

    /**
     * Execute a OPTION Request
     * 
     * @param string $uri
     * @param array $data
     * @param boolean $append_access_token
     * @return mixed
     */
    public function options($uri, $data = [], $append_access_token = true) {
        return $this->request('OPTION', $uri, ['query' => $data], $append_access_token);
    }

    /**
     * Execute all requests and returns the json body and headers
     * 
     * @param string $method
     * @param string $uri
     * @param array $data
     * @param boolean $append_access_token
     * @return mixed
     */
    public function execute($method, $uri, array $data = [], $append_access_token = true) {
        return $this->request($method, $uri, $data, $append_access_token);
    }

    /**
    * Check credentials, throws an Exception in case of access_token not being valid
    * Automatically tries to refresh if there is refresh_token
    *
    * @return boolean
    */
    private function checkCredentials()
    {
        if (empty($this->access_token)) {
            throw new Exception('AccessToken not available!');
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

        $refresh = $this->refreshAccessToken();

        if (empty($refresh['status'])) {
            throw new Exception('Could not refresh access token!');
        }

        return true;
    }

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
            $request = $e->getRequest();
            $contents = $response->getBody()->getContents();

            if (is_string($contents)) {
                $contents = json_decode($contents, true);
            }

            $return = [
                'reason' => $response->getReasonPhrase(),
                'status' => $response->getStatusCode(),
                'headers' => $response->getHeaders(),
                'body' => $contents,
                'method' => $method,
                'uri' => $uri,
                'data' => $data,
            ];
        } catch (RequestException $e) {
            $return = [
                'method' => $method,
                'request' => Psr7\str($e->getRequest()),
                'uri' => $uri,
                'data' => $data,
            ];

            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $return['reason'] = $response->getReasonPhrase();
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
    * Get a user
    * 
    * @param int $user_id
    * @return object Meli\User
    */
    public function getUser($user_id)
    {
        $response = $this->request('GET', "/users/{$user_id}");

        if ($response['status'] == 200) {
            return new User($this, $response['body']);
        } else {
            throw new Exception('Could not get this user!');
        }
    }

    /**
    * Get me
    * 
    * @return object Meli\User
    */
    public function getMe()
    {
        return $this->getUser('me');
    }

    /**
    * Get categories for a given country
    * 
    * @param string $country
    * @return mixed
    */
    public function getCategories($country = '')
    {
        if (empty($country) && empty($this->current_country)) {
            throw new InvalidArgumentException('You must select a country!');
        }

        if (!empty($country) && !in_array($country, array_keys($this->auth_url))) {
            $list = array_keys($this->auth_url);
            $list = implode(', ', $list);
            throw new InvalidArgumentException("You must select a valid country! Allowed values are: {$list}");
        }

        if (empty($country)) {
            $country = $this->current_country;
        }

        $response = $this->request('GET', "/sites/{$country}/categories");

        if ($response['status'] !== 200) {
            throw new Exception('Could not get the categories!');
        }


        $categories = [];

        foreach ($response['body'] as $category) {
            $category_got = $this->request('GET', '/categories/'.$category['id']);
            if ($category_got['status'] !== 200) {
                return false;
            }

            array_push($categories, new Category($this, $category_got['body']));
        }

        return $categories;
    }

    /**
     * Get a category
     *
     * @param string $category_id
     * @return object|instance of Category
     */
    public function getCategory($category_id)
    {
        $response = $this->request('GET', "/categories/{$category_id}");

        if ($response['status'] !== 200) {
            throw new Exception('Could not get this category!');
        }

        return new Category($this, $response['body']);
    }

    /**
    * Get a product
    * 
    */
    public function getProduct()
    {
        // Some cool code will born here!
    }
}
