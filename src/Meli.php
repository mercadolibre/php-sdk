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

final class Meli {
    const VERSION = '2.0.0';

    /**
     * @var $api_root_url is a main URL to access the Meli API's.
     * @var $auth_url is a url to redirect the user for login.
     */
    private $api_root_url = 'https://api.mercadolibre.com/';
    private $Oauth_url = 'oauth/token';
    private $auth_url = array(
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
    );

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
     */
    public function __construct($site = '', array $credentials = []) {
        $keys = ['client_id', 'client_secret', 'access_token', 'refresh_token', 'redirect_uri'];

        foreach ($keys as $k) {
            if (isset($credentials[$k]) && !empty($credentials[$k])) {
                $this->$k = $credentials[$k];
            }
        }

        if (!empty($site) && in_array($site, array_keys($this->auth_url))) {
            $this->api_root_url .= 'sites/'.$site.'/';
        }

        $this->client = new Client([
            'base_uri' => $this->api_root_url,
            'timeout' => 60
        ]);
    }

    /**
     * Return an string with a complete Meli login url.
     * NOTE: You can modify the $auth_url to change the language of login
     * 
     * @param string $redirect_uri
     * @return string
     */
    public function getAuthUrl($redirect_uri, $country) {
        if (!in_array($country, array_keys($this->auth_url))) {
            throw new InvalidArgumentException('This country is not supported!');
        }

        $this->redirect_uri = $redirect_uri;
        $params = [
            "client_id" => $this->client_id, 
            "response_type" => "code", 
            "redirect_uri" => $redirect_uri
        ];

        $auth_url = $this->auth_url[$country];

        $auth_uri = $auth_url."/authorization?".http_build_query($params);

        return $auth_uri;
    }

    /**
     * Executes a POST Request to authorize the application and take
     * an AccessToken.
     * 
     * @param string $code
     * @param string $redirect_uri
     * 
     */
    public function authorize($code, $redirect_uri) {

        if ($redirect_uri) {
            $this->redirect_uri = $redirect_uri;
        }

        $body = array(
            "grant_type" => "authorization_code", 
            "client_id" => $this->client_id, 
            "client_secret" => $this->client_secret, 
            "code" => $code, 
            "redirect_uri" => $this->redirect_uri
        );

        $response = $this->request('POST', 'oauth/token', ['query' => $body]);

        if ($response["status"] == 200) {             
            $this->access_token = $response["body"]['access_token'];

            if (isset($request['body']['refresh_token'])) {
                $this->refresh_token = $request['body']['refresh_token'];
            }

            return $request;
        } else {
            return $request;
        }
    }

    /**
     * Execute a POST Request to create a new AccessToken from a existent refresh_token
     * 
     * @return string|mixed
     */
    public function refreshAccessToken() {

        if($this->refresh_token) {
             $body = array(
                "grant_type" => "refresh_token", 
                "client_id" => $this->client_id, 
                "client_secret" => $this->client_secret, 
                "refresh_token" => $this->refresh_token
            );

            $opts = array(
                CURLOPT_POST => true, 
                CURLOPT_POSTFIELDS => $body
            );
        
            $request = $this->execute(self::$Oauth_url, $opts);

            if($request["httpCode"] == 200) {             
                $this->access_token = $request["body"]->access_token;

                if($request["body"]->refresh_token)
                    $this->refresh_token = $request["body"]->refresh_token;

                return $request;

            } else {
                return $request;
            }   
        } else {
            $result = array(
                'error' => 'Offline-Access is not allowed.',
                'httpCode'  => null
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
        $exec = $this->execute($path, null, $params, $assoc);

        return $exec;
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

    public function request($method, $uri, array $data = [])
    {
        try {
            if (!isset($data['query']) || !isset($data['query']['access_token'])) {
                $data['query'] = [
                    'access_token' => $this->access_token
                ];
            }

            $response = $this->client->request($method, $uri, $data);
            $return = [
                'body' => json_decode((string) $response->getBody(), true),
                'status' => $response->getStatusCode(),
            ];
        } catch (ClientException $e) {
            $response = $e->getResponse();
            $request = $e->getRequest();
            $uri = $request->getUri();
            $return = [
                'message' => $e->getMessage(),
                'status' => $response->getStatusCode(),
                'method' => $request->getMethod(),
                // 'uri' => $uri->composeComponents(),
                // 'target' => $request->getRequestTarget(),
                'data' => $request,
            ];
        } catch (TransferException $e) {
            $return = [
                'message' => $e->getMessage(),
                'response' => $e->getResponse(),
            ];
        } catch (\InvalidArgumentException $e) {
            $return = [
                'message' => $e->getMessage(),
            ];
        } catch (Exception $e) {
            $return = [
                'message' => $e->getMessage(),
            ];
        }

        return $return;
    }
}
