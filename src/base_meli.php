<?php

if (!function_exists('curl_init')) {
    throw new Exception('MeliPHP sdk needs the CURL PHP extension.');
}
if (!function_exists('json_decode')) {
    throw new Exception('MeliPHP sdk needs the JSON PHP extension.');
}

/**
 *
 * @author Pablo Moretti <pablomoretti@gmail.com>
 */
class SimpleDiskCache {

    private $basePath;

    public function __construct() {
        if (getenv('PHPSimpleDiskCachePath')) {
            $this -> basePath = getenv('PHPSimpleDiskCachePath');
        } else {
            $this -> basePath = sys_get_temp_dir() . '/' . 'PHPSimpleDiskCache' . '/';
        }
    }

    private function encodeFileName($data) {
        return rtrim(md5(json_encode($data)));
        /* return rtrim(strtr(base64_encode($data), '+/', '-_'), '='); */
    }

    private function getPath($key) {

        $keyMD5 = md5($key);

        return $this -> basePath . (intval(substr($keyMD5, 0, 16)) % 100) . '/' . (intval(substr($keyMD5, 16, 32)) % 100) . '/';

    }

    public function get($key) {

        $resource = $this -> getPath($key) . $this -> encodeFileName($key);

        $data = unserialize(gzinflate(@file_get_contents($resource)));

        if (time() < $data['expires']) {
            return $data['content'];
        }
    }

    public function put($key, $content, $ttl = 0) {

        if ($ttl > 300) {

            $expires = time() + $ttl;

            $path = $this -> getPath($key);

            @mkdir($path, 0777, true);

            $resource = $path . $this -> encodeFileName($key);

            $data = array('content' => $content, 'expires' => $expires);

            file_put_contents($resource, gzdeflate(serialize($data)), FILE_APPEND | LOCK_EX);
        }

    }

}

/**
 * Thrown when an API call returns an exception.
 *
 * @author Pablo Moretti <pablomoretti@gmail.com>
 */
class MeliApiException extends Exception {
    /**
     * The result from the API server that represents the exception information.
     */

    protected $result;

    /**
     * Make a new API Exception with the given result.
     *
     * @param array $result The result from the API server
     */
    public function __construct($result) {
        $this -> result = $result;

        $code = isset($result['error_code']) ? $result['error_code'] : 0;

        if (isset($result['error_description'])) {
            // OAuth 2.0 Draft 10 style
            $msg = $result['error_description'];
        } else if (isset($result['error']) && is_array($result['error'])) {
            // OAuth 2.0 Draft 00 style
            $msg = $result['error']['message'];
        } else if (isset($result['error_msg'])) {
            // Rest server style
            $msg = $result['error_msg'];
        } else {
            $msg = 'Unknown Error. Check getResult()';
        }

        parent::__construct($msg, $code);
    }

    /**
     * Return the associated result object returned by the API server.
     *
     * @return array The result from the API server
     */
    public function getResult() {
        return $this -> result;
    }

    /**
     * Returns the associated type for the error. This will default to
     * 'Exception' when a type is not available.
     *
     * @return string
     */
    public function getType() {
        if (isset($this -> result['error'])) {
            $error = $this -> result['error'];
            if (is_string($error)) {
                // OAuth 2.0 Draft 10 style
                return $error;
            } else if (is_array($error)) {
                // OAuth 2.0 Draft 00 style
                if (isset($error['type'])) {
                    return $error['type'];
                }
            }
        }

        return 'Exception';
    }

    /**
     * To make debugging easier.
     *
     * @return string The string representation of the error
     */
    public function __toString() {
        $str = $this -> getType() . ': ';
        if ($this -> code != 0) {
            $str .= $this -> code . ': ';
        }
        return $str . $this -> message;
    }

}
class AuthorizationException extends MeliApiException {

    /**
     * Make a new API Exception with the given result.
     *
     * @param array $result The result from the API server
     */
    public function __construct($result) {
        parent::__construct($result);

    }

    /**
     * Return the associated result object returned by the API server.
     *
     * @return array The result from the API server
     */
    public function getResult() {
        return $this -> result;
    }

    /**
     * Returns the associated type for the error. This will default to
     * 'Exception' when a type is not available.
     *
     * @return string
     */
    public function getType() {
        if (isset($this -> result['error'])) {
            $error = $this -> result['error'];
            if (is_string($error)) {
                // OAuth 2.0 Draft 10 style
                return $error;
            } else if (is_array($error)) {
                // OAuth 2.0 Draft 00 style
                if (isset($error['type'])) {
                    return $error['type'];
                }
            }
        }

        return 'Exception';
    }

    /**
     * To make debugging easier.
     *
     * @return string The string representation of the error
     */
    public function __toString() {
        $str = $this -> getType() . ': ';
        if ($this -> code != 0) {
            $str .= $this -> code . ': ';
        }
        return $str . $this -> message;
    }

}

/**
 * Provides access to the MercadoLibre Platform.  This class provides
 * a majority of the functionality needed, but the class is abstract
 * because it is designed to be sub-classed.  The subclass must
 * implement the four abstract methods listed at the bottom of
 * the file.
 *
 * @author Pablo Moretti <pablomoretti@gmail.com>
 */
abstract class BaseMeli {
    /**
     * Version.
     */
    const VERSION = '0.0.4';

    /**
     * Default options for curl.
     */
    public static $CURL_OPTS = array(CURLOPT_USERAGENT => 'MeliPHP-sdk-0.0.4', CURLOPT_CONNECTTIMEOUT => 10, CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 60);

    /**
     * Maps aliases to MercadoLibre domains.
     */
    protected static $API_DOMAIN = 'https://api.mercadolibre.com';

    /**
     * We need to know if We are in test mode
     */
    protected static $IS_MOCK = false;

    /**
     * List of query parameters that get automatically dropped when rebuilding
     * the current URL.
     */
    protected static $DROP_QUERY_PARAMS = array('code', 'meli-logout', 'meli-refresh' );

    /**
     * The Country ID.
     *
     * @var string
     */
    protected $countryId;

    /**
     * The init Connect.
     *
     * @var string
     */
    protected $initConnect = false;

    /**
     * The Application ID.
     *
     * @var string
     */
    protected $appId;

    /**
     * The Application App Secret.
     *
     * @var string
     */
    protected $appSecret;

    /**
     * The ID of the MercadoLibre user, or 0 if the user is logged out.
     *
     * @var integer
     */
    protected $userId;

    protected $app;

    /**
     * The OAuth access token received in exchange for a valid authorization
     * code.  null means the access token has yet to be determined.
     * The refresh token is used to get a new access token when it expires
     * if the app has offline_access permission
     *
     * @var string
     */
    protected $accessToken = null;

    protected $cache;

    /**
     * Set the Cache ID.
     *
     * @param
     * @return BaseMeli
     */
    public function setCache($cache) {
        $this -> cache = $cache;
        return $this;
    }

    public function getCache() {
        if ($this -> cache == null) {
            $this -> cache = new SimpleDiskCache();
        }
        return $this -> cache;
    }

    /**
     * Initialize a MercadoLibre Application.
     *
     * The configuration:
     * - countryId: the country ID
     * - appId: the application ID
     * - secret: the application secret
     *
     * @param array $config The application configuration
     */
    public function __construct($config) {
        if (isset($config['mockUrl'])) {
            self::$API_DOMAIN = $config['mockUrl'];
            self::$IS_MOCK = true;
        }

        $this -> setAppId($config['appId']);
        $this -> setAppSecret($config['secret']);
        $this -> initApp($config['appId']);
    }

    public function initApp($appId) {
        $appKey = '/applications/' . $appId;
        $this -> app = $this -> getCache() -> get($appKey);

        if ($this -> app == null) {
            $result = $this -> get($appKey);
            if ($result) {
                $this -> app = $result ['json'];
                $this -> getCache() -> put($appKey, $this -> app, 60 * 60);
            }
        }
    }

    public function getApp() {
        if ($this -> cache == null) {
            $this -> cache = new SimpleDiskCache();
        }
        return $this -> cache;
    }

    /**
     * Set the Application ID.
     *
     * @param string $appId The Application ID
     * @return BaseMeli
     */
    public function setAppId($appId) {
        $this -> appId = $appId;
        return $this;
    }

    /**
     * Get the Application ID.
     *
     * @return string the Application ID
     */
    public function getAppId() {
        return $this -> appId;
    }

    /**
     * Set the App Secret.
     *
     * @param string $appSecret The App Secret
     * @return BaseMeli
     */
    public function setAppSecret($appSecret) {
        $this -> appSecret = $appSecret;
        return $this;
    }

    /**
     * Get the App Secret.
     *
     * @return string the App Secret
     */
    public function getAppSecret() {
        return $this -> appSecret;
    }

    /**
     * Set the Country ID.
     *
     * @param string $countryId The Country ID
     * @return BaseMeli
     */
    public function setCountryId($countryId) {
        $this -> countryId = $countryId;
        return $this;
    }

    /**
     * Get the Site ID
     *
     * @return string the Application ID
     */
    public function getSiteId() {
        return $this -> app['site_id'];
    }

    /**
     * Get the Domain ID
     *
     * @return string the domain
     */
    public function getDomain() {
        $data = $this -> get('/sites/' . $this -> getSiteId() . '/searchUrl');
        return substr(strstr($data['json']['url'], "."), 1, -1);
    }

    /**
     * Sets the access token for api calls.  Use this if you get
     * your access token by other means and just want the SDK
     * to use it.
     *
     * @param string $access_token an access token.
     * @return BaseMeli
     */
    public function setAccessToken($access_token) {
        $this -> setPersistentData('access_token', $access_token);
        return $this;
    }

    protected function setUserId($userId) {
        $this -> setPersistentData('user_id', $userId);
        return $this;
    }

    /**
     * Get the userId of the connected user, or 0
     * if the MercadoLibre user is not connected.
     *
     * @return string the userId if available.
     */
    public function getUserId() {
        if ($this -> initConnect) {
            return $this -> getPersistentData('user_id', 0);
        }
        throw new Exception('You must execute method initConnect before get user id');
    }

    /**
     * Determines the access token that should be used for API calls.
     * The first time this is called, $this->accessToken is set equal
     * to either a valid user access token, or it's set to the application
     * access token if a valid user access token wasn't available.  Subsequent
     * calls return whatever the first call returned.
     *
     * @return string The access token
     */
    public function getAccessToken() {
        if ($this -> initConnect) {
            return $this -> getPersistentData('access_token');
        }
        throw new Exception('You must execute method initConnect before get access token');
    }

    public function tokenNeedsRefresh() {
        $accessToken = $this -> getAccessToken();
        if ($accessToken != null) {
            return $accessToken['expires'] < time() + 1;
        }
        return false;
    }

    public function doRefreshToken(){
        $this -> setAccessToken( $this -> getAccessTokenFromRefreshToken() );
    }


    public function isAccessTokenNotExpired() {
        return ($this -> getAccessToken() != null);
    }

    /**
     * Initialize connect process,
     * return the userId of the connected user, or 0
     * if the MercadoLibre user is not connected.
     *
     * @return string the userId if available.
     */
    public function initConnect($code = NULL) {
        $this -> initConnect = true;
        if (isset($code) || $this -> isLogin()) {
            $this -> doAuthorize($code);
        } else if ($this -> isLogout()) {
            $this -> destroySession();
            $this -> reload();
        } else if ($this -> tokenNeedsRefresh()) {
            $this -> setAccessToken($this -> getAccessTokenFromRefreshToken() );
            $this -> setUserId($this -> getUserIdFromAccessToken());
            return $this -> getUserId();
        } else {
            return $this -> getUserId();
        }

    }

    public function doAuthorize($code) {
        $this -> setAccessToken($this -> getAccessTokenFromCode(isset($code)?$code:$this -> getCode()) );
        $this -> setUserId($this -> getUserIdFromAccessToken());
        $this -> reload();
    }

    protected function reload() {
        if (!self::$IS_MOCK)
            $this -> redirect($this -> getCurrentUrl());
    }

    protected function redirect($url) {
        header("Location: " . $url, TRUE, 302);
        exit(0);
    }

    /**
     * Retrieves an access token for the given authorization code
     *
     * @param string $code An authorization code.
     * @return mixed An access token exchanged for the authorization code, or
     *               false if an access token could not be generated.
     */
    protected function getAccessTokenFromCode($code, $redirect_uri = null) {
        if (empty($code)) {
            return false;
        }

        if ($redirect_uri === null) {
            $redirect_uri = $this -> getCurrentUrl();
        }
        
        
        $result = $this -> execute('POST', false, '/oauth/token', array('grant_type' => 'authorization_code', 'code' => $code, 'client_id' => $this -> getAppId(), 'client_secret' => $this -> getAppSecret(), 'redirect_uri' => $redirect_uri));
        $json = $result['json'];
        if (!isset($json) || isset($json['error'])) {
            throw new AuthorizationException($result);
        }
        
        return array('value' => $json['access_token'], 
                        'expires' => time() + $json['expires_in'], 
                        'scope' => $json['scope'], 
                        'refresh_token' => isset($json['refresh_token']) ? $json['refresh_token'] : null);

    }
    

    /**
     * Retrieves an access token for the actual refresh token
     *
     * @return mixed An access token exchanged for the redresh token, or
     *               false if an access token could not be generated.
     */
    public function getAccessTokenFromRefreshToken() {
        $accessToken = $this -> getAccessToken();
        if ($accessToken == null || !isset($accessToken['refresh_token'])) {
            return false;
        }
        
        $result = $this -> execute('POST', false, '/oauth/token', array(
            'grant_type' => 'refresh_token', 
            'client_id' => $this -> getAppId(), 
            'client_secret' => $this -> getAppSecret(), 
            'refresh_token' => $accessToken['refresh_token']));
        return array('value' => $result['json']['access_token'], 'expires' => time() + $result['json']['expires_in'], 'scope' => $result['json']['scope'], 'refresh_token' => $result['json']['refresh_token']);
    }


    /**
     * Get a Login URL for use with redirects.
     *
     * The parameters:
     * - redirect_uri: the url to go to after a successful login
     * - scope: comma separated list of requested extended perms
     *
     * @param array $params Provide custom parameters
     * @return string The URL for the login flow
     */
    public function getLoginUrl($params = array()) {

        // if 'scope' is passed as an array, convert to comma separated list
        $scopeParams = isset($params['scope']) ? $params['scope'] : null;
        if ($scopeParams && is_array($scopeParams)) {
            $params['scope'] = implode(',', $scopeParams);
        }
        $redirectUri = isset($params['redirect_uri'])?$params['redirect_uri']:$this -> getCurrentUrl();
        return $this -> getUrl('auth', '/authorization', array_merge(array('client_id' => $this -> getAppId(), 'redirect_uri' => $redirectUri, 'response_type' => 'code'), $params));
    }

    /**
     * Get a Logout URL suitable for use with redirects.
     *
     * The parameters:
     * - next: the url to go to after a successful logout
     *
     * @param array $params Provide custom parameters
     * @return string The URL for the logout flow
     */
    public function getLogoutUrl($params = array()) {

        $currentUrl = $this -> getCurrentUrl();

        if (strpos($currentUrl, '?') === false) {
            $currentUrl .= '?';
        } else {
            $currentUrl .= '&';
        }

        $currentUrl .= 'meli-logout=true';

        return $currentUrl;
    }

    public function postWithAccessToken($path, $params = null) {
        $params = ($params == null) ? null : json_encode($params);
        
        return $this -> execute('POST', true, $path, $params);
    }

    public function post($path, $params = null) {
        $params = ($params == null) ? null : json_encode($params);
        return $this -> execute('POST', false, $path, $params);
    }

    public function getWithAccessToken($path, $params = null) {
        return $this -> execute('GET', true, $path, $params);
    }

    public function get($path, $params = null) {
        return $this -> execute('GET', false, $path, $params);
    }

    public function putWithAccessToken($path, $params = null) {
        $params = ($params == null) ? null : json_encode($params);
        return $this -> execute('PUT', true, $path, $params);
    }

    public function put($path, $params = null) {
        $params = ($params == null) ? null : json_encode($params);
        return $this -> execute('PUT', false, $path, $params);
    }

    public function deleteWithAccessToken($path, $params = null) {
        $params = ($params == null) ? null : json_encode($params);
        return $this -> execute('DELETE', true, $path, $params);
    }

    public function delete($path, $params = null) {
        $params = ($params == null) ? null : json_encode($params);
        return $this -> execute('DELETE', false, $path, $params);
    }

    /**
     * Get the authorization code from the query parameters, if it exists,
     * and otherwise return false to signal no authorization code was
     * discoverable.
     *
     * @return mixed The authorization code, or false if the authorization
     *               code could not be determined.
     */
    protected function getCode() {
        if (isset($_REQUEST['code'])) {
            return $_REQUEST['code'];
        }
        return false;
    }

    /**
     * Check if the authorization process is running
     *
     * @return boolean Returns true if the authorization process is running
     */
    protected function isLogin() {
        return isset($_REQUEST['code']);
    }

    /**
     * Check if the Meli Logout is in the query parameters
     *
     * @return boolean Returns true Meli Logout is in the query parameters
     */
    protected function isLogout() {
        return isset($_REQUEST['meli-logout']);
    }

    /**
     * Retrieves the UID with the understanding that
     * $this->accessToken has already been set and is
     * seemingly legitimate.
     *
     * @return integer Returns the ID of the MercadoLibre user, or 0
     *                 if the MercadoLibre user could not be determined.
     */
    protected function getUserIdFromAccessToken() {
        try {
            $user = $this -> getWithAccessToken('/users/me');
            if (!isset($user['json']['id']))
                return 0;
            return $user['json']['id'];
        } catch (MeliApiException $e) {
            return 0;
        }
    }

    /**
     * Invoke the API.
     *
     * @param string $method The http method
     * @param boolean $useAccessToken The path
     * @param string $path The path The http method
     * @param array $params The query/post data
     *
     * @return mixed The decoded response object
     * @throws MeliApiException
     */
    protected function execute($method, $useAccessToken, $path, $params = array()) {

        $getParams = array();

        if ($useAccessToken) {
            $accessToken = $this -> getAccessToken();
            $getParams['access_token'] = $accessToken['value'];
        }

        $url = $this -> getUrlForAPI($path, $getParams);
        $response = $this -> makeRequest($method, $url, $params);
        //check if token needs refresh
        if ($useAccessToken) {
            $accessToken = $this -> getAccessToken();
            if (isset($accessToken['refresh_token']) && $response['statusCode'] == 404) {
                $this->doRefreshToken();
                $accessToken = $this -> getAccessToken();
                $getParams['access_token'] = $accessToken['value'];
                $url = $this -> getUrlForAPI($path, $getParams);
                $response = $this -> makeRequest($method, $url, $params);
            }
        }
        
        $response['json'] = json_decode($response['body'], true);
        return $response;
    }

    /**
     * Makes an HTTP request. This method can be overridden by subclasses if
     * developers want to do fancier things or use something other than curl to
     * make the request.
     *
     * @param string $method The http method
     * @param string $url The URL to make the request to
     * @param array $params The parameters to use for the POST body
     * @param CurlHandler $ch Initialized curl handle
     *
     * @return string The response text
     */
    protected function makeRequest($method, $url, $params, $ch = null) {
        if (!$ch) {
            $ch = curl_init();
        }

        $opts = self::$CURL_OPTS;
        if ($method == 'GET') {
            if ($params) {
                if (strpos($url, '?') !== false) {
                    $url .= '&' . http_build_query($params, null, '&');
                } else {
                    $url .= '?' . http_build_query($params, null, '&');
                }

                $b = $this -> getCache() -> get(str_replace(self::$API_DOMAIN, "", $url));

                if ($b != null) {
                    return $b;
                }

            }
        } else {
            if(!isset($opts[CURLOPT_HTTPHEADER]) || $opts[CURLOPT_HTTPHEADER] == null){
                $opts[CURLOPT_HTTPHEADER] = array();
            }

            $opts[CURLOPT_CUSTOMREQUEST] = $method;
            if ($params) {
                
                if (is_array($params)) {
                    $opts[CURLOPT_POSTFIELDS] = http_build_query($params, null, '&');
                } else {
                    
                    $opts[CURLOPT_POSTFIELDS] = $params;
                    
                    $opts[CURLOPT_HTTPHEADER] = array_merge($opts[CURLOPT_HTTPHEADER], array('Content-Type: application/json', 'Content-Length: ' . strlen($params)));
                }
            }

        }

        $opts[CURLOPT_URL] = $url;

        // disable the 'Expect: 100-continue' behaviour. This causes CURL to wait
        // for 2 seconds if the server does not support this header.
      	//  if (isset($opts[CURLOPT_HTTPHEADER])) {
      	//      $existing_headers = $opts[CURLOPT_HTTPHEADER];
       	//     $existing_headers[] = 'Expect:';
     	//       $opts[CURLOPT_HTTPHEADER] = $existing_headers;
      	//  } else {
       	//     echo "--------------------------------------";
      	//      $opts[CURLOPT_HTTPHEADER] = array('Expect:');
	    //  }
    
        
        //$opts[CURLOPT_HEADERFUNCTION] = array(&$this,'curlHeaderCallback');

        curl_setopt_array($ch, $opts);

        //curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return the response from the server
        curl_setopt($ch, CURLOPT_HEADER, true);
        // tells curl to include headers in response
        $content = curl_exec($ch);
        if ($content === false) {
            $e = new MeliApiException( array('error_code' => curl_errno($ch), 'error' => array('message' => curl_error($ch), 'type' => 'CurlException', ), ));
            curl_close($ch);
            throw $e;
        }

        $response = curl_getinfo($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
        
        $startBody = false;

        $data = explode("\n", $content);

        $headers = array();

        $body = "";
        foreach ($data as $line) {
            if ((strlen($line) == 1 && ord($line) == 13) || $startBody) {
                if ($startBody) {
                    $body .= $line;
                } else {
                    $startBody = true;
                }
            } else {
                if (ord(strpos($line, 'HTTP')) != 0) {
                    $key = 'Status-Code';
                    $value = intval(substr($line, 9, 3));
                } else {
                    list($key, $value) = explode(":", $line);
                }
                $headers[$key] = $value;
            }
        }
        if ($method == 'GET' && isset($headers['Cache-Control'])) {

            if (preg_match('/max-age=(.*)/', $headers['Cache-Control'], $matches)) {
                $this -> getCache() -> put(str_replace(self::$API_DOMAIN, "", $url), $body, intval($matches[1]));
            }

        }

        curl_close($ch);
        return array('statusCode' => $httpCode, 'body' => $body, 'headers' => $headers);
    }

    /**
     * Build the URL for given subdomain, path and parameters.
     *
     * @param $name string The subdomain
     * @param $path string Optional path (with slash)
     * @param $params array Optional query parameters
     *
     * @return string The URL for the given parameters
     */
    protected function getUrl($subdomain, $path = '/', $params = array()) {
        $url = $subdomain . '.' . $this -> getDomain() . $path;

        if ($params) {
            $url .= '?' . http_build_query($params, null, '&');
        }

        return 'https://' . $url;
    }

    /**
     * Build the URL for all APIs
     *
     * @param $path string Optional path (with slash)
     * @param $params array Optional query parameters
     *
     * @return string The URL for the given parameters
     */
    protected function getUrlForAPI($path, $params = array()) {

        if (strpos($path, '#{siteId}') !== false) {
            $path = str_replace('#{siteId}', $this -> getSiteId(), $path);
        }

        $url = self::$API_DOMAIN . $path;
        if ($params) {

            if (strpos($url, '?') !== false) {
                $url .= '&' . http_build_query($params, null, '&');
            } else {
                $url .= '?' . http_build_query($params, null, '&');
            }

        }
        return $url;
    }

    /**
     * Returns the Current URL, stripping it of known FB parameters that should
     * not persist.
     *
     * @return string The current URL
     */
    public function getCurrentUrl() {

        if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
            $protocol = 'https://';
        } else {
            $protocol = 'http://';
        }
        $currentUrl = $protocol . $this->getHost() . $this->getRequestUri();
        $parts = parse_url($currentUrl);

        $query = '';
        if (!empty($parts['query'])) {
            // drop known fb params
            $params = explode('&', $parts['query']);
            $retained_params = array();
            foreach ($params as $param) {
                if ($this -> shouldRetainParam($param)) {
                    $retained_params[] = $param;
                }
            }

            if (!empty($retained_params)) {
                $query = '?' . implode($retained_params, '&');
            }
        }

        // use port if non default
        $port = isset($parts['port']) && (($protocol === 'http://' && $parts['port'] !== 80) || ($protocol === 'https://' && $parts['port'] !== 443)) ? ':' . $parts['port'] : '';

        // rebuild
        return $protocol . $parts['host'] . $port . $parts['path'] . $query;
    }

    public function getHost() {
        return isset($_SERVER['HTTP_HOST'])?$_SERVER['HTTP_HOST']:'nohost';
    }

    public function getRequestUri() {
        return isset($_SERVER['REQUEST_URI'])?$_SERVER['REQUEST_URI']:'/norequest';
    }

    /**
     * Returns true if and only if the key or key/value pair should
     * be retained as part of the query string.  This amounts to
     * a brute-force search of the very small list of Meli-specific
     * params that should be stripped out.
     *
     * @param string $param A key or key/value pair within a URL's query (e.g.
     *                     'foo=a', 'foo=', or 'foo'.
     *
     * @return boolean
     */
    protected function shouldRetainParam($param) {
        foreach (self::$DROP_QUERY_PARAMS as $drop_query_param) {
            if (strpos($param, $drop_query_param . '=') === 0) {
                return false;
            }
        }

        return true;
    }

    /**
     * Analyzes the supplied result to see if it was thrown
     * because the access token is no longer valid.  If that is
     * the case, then we destroy the session.
     *
     * @param $result array A record storing the error message returned
     *                      by a failed API call.
     */
    protected function throwAPIException($result) {
        $e = new MeliApiException($result);
        switch ($e->getType()) {
            // OAuth 2.0 Draft 00 style
            case 'OAuthException' :
            // OAuth 2.0 Draft 10 style
            case 'invalid_token' :
            // REST server errors are just Exceptions
            case 'Exception' :
                $message = $e -> getMessage();
                if ((strpos($message, 'Error validating access token') !== false) || (strpos($message, 'Invalid OAuth access token') !== false) || (strpos($message, 'An active access token must be used') !== false)) {
                    $this -> destroySession();
                }
                break;
        }

        throw $e;
    }

    /**
     * Destroy the current session
     */
    public function destroySession() {
        $this -> clearAllPersistentData();
    }

    /**
     * Each of the following four methods should be overridden in
     * a concrete subclass, as they are in the provided Meli class.
     * The Meli class uses PHP sessions to provide a primitive
     * persistent store, but another subclass--one that you implement--
     * might use a database, memcache, or an in-memory cache.
     *
     * @see Meli
     */

    /**
     * Stores the given ($key, $value) pair, so that future calls to
     * getPersistentData($key) return $value. This call may be in another request.
     *
     * @param string $key
     * @param array $value
     *
     * @return void
     */
    abstract protected function setPersistentData($key, $value);

    /**
     * Get the data for $key, persisted by BaseMeli::setPersistentData()
     *
     * @param string $key The key of the data to retrieve
     * @param boolean $default The default value to return if $key is not found
     *
     * @return mixed
     */
    abstract protected function getPersistentData($key, $default = false);

    /**
     * Clear the data with $key from the persistent storage
     *
     * @param string $key
     * @return void
     */
    abstract protected function clearPersistentData($key);

    /**
     * Clear all data from the persistent storage
     *
     * @return void
     */
    abstract protected function clearAllPersistentData();
}
