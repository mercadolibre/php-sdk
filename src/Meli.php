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
     * Constructor method. Set all variables to connect in Meli
     *
     * @param string $client_id
     * @param string $client_secret
     * @param string $access_token
     * @param string $refresh_token
     * @return object
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
            $this->access_token = $response['body']['access_token'];
            $this->expires_in = $response['body']['expires_in'];

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
        
            $request = $this->request('POST', $this->Oauth_endpoint, ['query' => $body]);

            if ($request['status'] == 200) {             
                $this->access_token = $request['body']['access_token'];
                $this->expires_in = $response['body']['expires_in'];

                if (isset($request['body']['refresh_token'])) {
                    $this->refresh_token = $request['body']['refresh_token'];
                }

                return $request;
            } else {
                return $request;
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
     * @param string $path
     * @param array $params
     * @param boolean $assoc
     * @return mixed
     */
    public function get($path, $params = null, $assoc = false) {
        return $this->request($path, null, $params, $assoc);
    }

    /**
     * Execute a POST Request
     * 
     * @param string $body
     * @param array $params
     * @return mixed
     */
    public function post($path, $body = null, $params = array()) {
        $body = json_encode($body);
        $opts = array(
            CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
            CURLOPT_POST => true, 
            CURLOPT_POSTFIELDS => $body
        );
        
        $exec = $this->execute($path, $opts, $params);

        return $exec;
    }

    /**
     * Execute a PUT Request
     * 
     * @param string $path
     * @param string $body
     * @param array $params
     * @return mixed
     */
    public function put($path, $body = null, $params = array()) {
        $body = json_encode($body);
        $opts = array(
            CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
            CURLOPT_CUSTOMREQUEST => "PUT",
            CURLOPT_POSTFIELDS => $body
        );
        
        $exec = $this->execute($path, $opts, $params);

        return $exec;
    }

    /**
     * Execute a DELETE Request
     * 
     * @param string $path
     * @param array $params
     * @return mixed
     */
    public function delete($path, $params) {
        $opts = array(
            CURLOPT_CUSTOMREQUEST => "DELETE"
        );
        
        $exec = $this->execute($path, $opts, $params);
        
        return $exec;
    }

    /**
     * Execute a OPTION Request
     * 
     * @param string $path
     * @param array $params
     * @return mixed
     */
    public function options($path, $params = null) {
        $opts = array(
            CURLOPT_CUSTOMREQUEST => "OPTIONS"
        );
        
        $exec = $this->execute($path, $opts, $params);

        return $exec;
    }

    /**
     * Execute all requests and returns the json body and headers
     * 
     * @param string $path
     * @param array $opts
     * @param array $params
     * @param boolean $assoc
     * @return mixed
     */
    public function execute($path, $opts = array(), $params = array(), $assoc = false) {
        $uri = $this->make_path($path, $params);

        $ch = curl_init($uri);
        curl_setopt_array($ch, self::$CURL_OPTS);

        if(!empty($opts))
            curl_setopt_array($ch, $opts);

        $return["body"] = json_decode(curl_exec($ch), $assoc);
        $return["httpCode"] = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);
        
        return $return;
    }

    /**
     * Check and construct an real URL to make request
     * 
     * @param string $path
     * @param array $params
     * @return string
     */
    public function make_path($path, $params = array()) {
        if (!preg_match("/^http/", $path)) {
            if (!preg_match("/^\//", $path)) {
                $path = '/'.$path;
            }
            $uri = self::$api_root_url.$path;
        } else {
            $uri = $path;
        }

        if(!empty($params)) {
            $paramsJoined = array();

            foreach($params as $param => $value) {
               $paramsJoined[] = "$param=$value";
            }
            $params = '?'.implode('&', $paramsJoined);
            $uri = $uri.$params;
        }

        return $uri;
    }

    /**
    * Check credentials, throws an Exception in case of access_token not being valid
    * Automatically tries to refresh if there is refresh_token
    *
    * @return void
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

                if (isset($data['query']) && is_array($data['query'])) {
                    $data['query']['access_token'] = $this->access_token;
                } else {
                    $data['query'] = [
                        'access_token' => $this->access_token
                    ];
                }
            }

            $response = $this->client->request($method, $uri, $data);
            $return = [
                'body' => json_decode((string) $response->getBody(), true),
                'status' => $response->getStatusCode(),
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

        return $return;
    }
}