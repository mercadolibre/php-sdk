<?php

if (!function_exists('curl_init')) {
  throw new Exception('MeliPHP sdk needs the CURL PHP extension.');
}
if (!function_exists('json_decode')) {
  throw new Exception('MeliPHP sdk needs the JSON PHP extension.');
}

/**
 * Thrown when an API call returns an exception.
 *
 * @author Pablo Moretti <pablomoretti@gmail.com>
 */
class MeliApiException extends Exception
{
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
    $this->result = $result;

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
    return $this->result;
  }

  /**
   * Returns the associated type for the error. This will default to
   * 'Exception' when a type is not available.
   *
   * @return string
   */
  public function getType() {
    if (isset($this->result['error'])) {
      $error = $this->result['error'];
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
    $str = $this->getType() . ': ';
    if ($this->code != 0) {
      $str .= $this->code . ': ';
    }
    return $str . $this->message;
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
abstract class BaseMeli
{
  /**
   * Version.
   */
  const VERSION = '0.0.1';

  /**
   * Default options for curl.
   */
  public static $CURL_OPTS = array(
    CURLOPT_CONNECTTIMEOUT => 10,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 60,
    CURLOPT_USERAGENT      => 'MeliPHP-sdk-0.0.1',
  );


  /**
   * Maps aliases to MercadoLibre domains.
   */
  public static $API_DOMAIN = 'https://api.mercadolibre.com';


  /**
   * List of query parameters that get automatically dropped when rebuilding
   * the current URL.
   */
  protected static $DROP_QUERY_PARAMS = array(
    'code',
    'state',
    'meli-logout',
    'signed_request',
  );


  /**
   * Maps country to MercadoLibre sites.
   */
  public static $COUNTRY_CONFIG = array(
    'ar'       => array(
						'SITE_ID' 	=> 'MLA',
						'DOMAIN' 	=> 'mercadolibre.com.ar',
				),
  );

   /**
   * The Country ID.
   *
   * @var string
   */
  protected $countryId;

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

  /**
   * A CSRF state variable to assist in the defense against CSRF attacks.
   */
  protected $state;

  /**
   * The OAuth access token received in exchange for a valid authorization
   * code.  null means the access token has yet to be determined.
   *
   * @var string
   */
  protected $accessToken = null;

  /**
   * Indicates if the CURL based @ syntax for file uploads is enabled.
   *
   * @var boolean
   */
  protected $fileUploadSupport = false;

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
    $this->setCountryId($config['countryId']);
    $this->setAppId($config['appId']);
    $this->setAppSecret($config['secret']);

    $state = $this->getPersistentData('state');
    if (!empty($state)) {
      $this->state = $state;
    }
  }

  /**
   * Set the Application ID.
   *
   * @param string $appId The Application ID
   * @return BaseMeli
   */
  public function setAppId($appId) {
    $this->appId = $appId;
    return $this;
  }
  

  /**
   * Set the Country ID.
   *
   * @param string $countryId The Country ID
   * @return BaseMeli
   */
  public function setCountryId($countryId) {
    $this->countryId = $countryId;
    return $this;
  }

  /**
   * Get the Application ID.
   *
   * @return string the Application ID
   */
  public function getAppId() {
    return $this->appId;
  }

  /**
   * Get the Site ID
   *
   * @return string the Application ID
   */
  public function getSiteId() {
  	return self::$COUNTRY_CONFIG[$this->countryId]['SITE_ID'];
  }

  /**
   * Get the Domain ID
   *
   * @return string the domain
   */
  public function getDomain() {
  	return self::$COUNTRY_CONFIG[$this->countryId]['DOMAIN']; 
  }
  

  /**
   * Set the App Secret.
   *
   * @param string $apiSecret The App Secret
   * @return BaseMeli
   * @deprecated
   */
  public function setApiSecret($apiSecret) {
    $this->setAppSecret($apiSecret);
    return $this;
  }

  /**
   * Set the App Secret.
   *
   * @param string $appSecret The App Secret
   * @return BaseMeli
   */
  public function setAppSecret($appSecret) {
    $this->appSecret = $appSecret;
    return $this;
  }

  /**
   * Get the App Secret.
   *
   * @return string the App Secret
   * @deprecated
   */
  public function getApiSecret() {
    return $this->getAppSecret();
  }

  /**
   * Get the App Secret.
   *
   * @return string the App Secret
   */
  public function getAppSecret() {
    return $this->appSecret;
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
  	$this->setPersistentData('access_token', $access_token);
    $this->accessToken = $access_token;
    return $this;
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
    if ($this->accessToken !== null) {
      // we've done this already and cached it.  Just return.
      return $this->accessToken;
    }
	
	return $this->accessToken = $this->getPersistentData('access_token');
	
  }

  /**
   * Get the userId of the connected user, or 0
   * if the MercadoLibre user is not connected.
   *
   * @return string the userId if available.
   */
  public function getUserId() {
    if ($this->userId !== null) {
      return $this->userId;
    }

    return $this->user = $this->getUserFromAvailableData();
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
      $redirect_uri = $this->getCurrentUrl();
    }

	$result = $this->post(false,'/oauth/token', array(
                    'grant_type' 	=> 'authorization_code',
					'code' 			=> $code,
                    'client_id' 	=> $this->getAppId(),
                    'client_secret' => $this-> getAppSecret(),
					'redirect_uri' 	=> $redirect_uri,
					));

	return $result;
   
  }


  /**
   * Determines the connected user by first examining any signed
   * requests, then considering an authorization code, and then
   * falling back to any persistent store storing the user.
   *
   * @return integer The id of the connected Meli user,
   *                 or 0 if no such user exists.
   */
  protected function getUserFromAvailableData() {

	$userId = $this->getPersistentData('user_id', $default = 0);

	if (isset($_REQUEST['code']) && isset($_REQUEST['state'])) {
		
		$accessToken = $this->getAccessTokenFromCode($_REQUEST['code']);

		$this->setAccessToken($accessToken);

		$user = $this->get(true,'/users/me');
		
		$this->setPersistentData('user_id', $user['id']);

		header("Location: " . $this->getCurrentUrl(),TRUE,302);
		

	}
		
	if (isset($_REQUEST['meli-logout'])) {
		$this->clearAllPersistentData();
		
		header("Location: " . $this->getCurrentUrl(),TRUE,302);
		
		exit(0);
		
	}



    return $userId;
  }

  /**
   * Get a Login URL for use with redirects. By default, full page redirect is
   * assumed. If you are using the generated URL with a window.open() call in
   * JavaScript, you can pass in display=popup as part of the $params.
   *
   * The parameters:
   * - redirect_uri: the url to go to after a successful login
   * - scope: comma separated list of requested extended perms
   *
   * @param array $params Provide custom parameters
   * @return string The URL for the login flow
   */
  public function getLoginUrl($params=array()) {
    $this->establishCSRFTokenState();

    // if 'scope' is passed as an array, convert to comma separated list
    $scopeParams = isset($params['scope']) ? $params['scope'] : null;
    if ($scopeParams && is_array($scopeParams)) {
      $params['scope'] = implode(',', $scopeParams);
    }

    return $this->getUrl(
      'auth',
      '/authorization',
      array_merge(array(
                    'client_id' 	=> $this->getAppId(),
					'redirect_uri' 	=> $this->getCurrentUrl(),
                    'response_type' => 'code',
                    'state' 		=> $this->state
				),$params));
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
  public function getLogoutUrl($params=array()) {
  	
		$currentUrl = $this->getCurrentUrl();
		
		if (strpos($currentUrl, '?') === false) {
			$currentUrl .= '?';
		}else{
			$currentUrl .= '&';
		}
						
		$currentUrl .= 'meli-logout=true';		
	
    return $currentUrl;
  }

  /**
   * Get a login status URL to fetch the status from Meli.
   *
   * The parameters:
   * - ok_session: the URL to go to if a session is found
   * - no_session: the URL to go to if the user is not connected
   * - no_user: the URL to go to if the user is not signed into Meli
   *
   * @param array $params Provide custom parameters
   * @return string The URL for the logout flow
   */
  public function getLoginStatusUrl($params=array()) {
    return $this->getUrl(
      'www',
      'extern/login_status.php',
      array_merge(array(
        'api_key' => $this->getAppId(),
        'no_session' => $this->getCurrentUrl(),
        'no_user' => $this->getCurrentUrl(),
        'ok_session' => $this->getCurrentUrl(),
        'session_version' => 3,
      ), $params)
    );
  }



  public function post($useAccessToken,$path,$params) {
	return $this->execute('POST',$useAccessToken,$path,$params);
  }

  public function get($useAccessToken,$path,$params) {
	return $this->execute('GET',$useAccessToken,$path,$params);
  }

  /**
   * Make an API call.
   *
   * @return mixed The decoded response
   */
  public function api(/* polymorphic */) {
	$args = func_get_args();
	return call_user_func_array(array($this, '_graph'), $args);
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
      if ($this->state !== null &&
          isset($_REQUEST['state']) &&
          $this->state === $_REQUEST['state']) {

        // CSRF state has done its job, so clear it
        $this->state = null;
        $this->clearPersistentData('state');
        return $_REQUEST['code'];
      } else {
        self::errorLog('CSRF state token does not match one provided.');
        return false;
      }
    }

    return false;
  }

  /**
   * Retrieves the UID with the understanding that
   * $this->accessToken has already been set and is
   * seemingly legitimate. 
   * 
   * @return integer Returns the UID of the Meli user, or 0
   *                 if the Meli user could not be determined.
   */
  protected function getUserFromAccessToken() {
    try {
      $user_info = $this->get(true,'/users/me');
      return $user_info['id'];
    } catch (MeliApiException $e) {
      return 0;
    }
  }

  /**
   * Lays down a CSRF state token for this process.
   *
   * @return void
   */
  protected function establishCSRFTokenState() {
    if ($this->state === null) {
      $this->state = md5(uniqid(mt_rand(), true));
      $this->setPersistentData('state', $this->state);
    }
  }



  /**
   * Invoke the Graph API.
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

	if($useAccessToken){
		$accessToken = $this->getAccessToken();
		$params['access_token'] = $accessToken['access_token'];
	}

	$url = $this->getUrlForAPI($path);

   	$result = json_decode($this->makeRequest($method, $url, $params), true);

   return $result;
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
  protected function makeRequest($method, $url, $params, $ch=null) {
    if (!$ch) {
      $ch = curl_init();
    }

    $opts = self::$CURL_OPTS;

	if($method == 'GET'){
	    if ($params) {
	      $url .= '?' . http_build_query($params, null, '&');
	    }
	}
	else{
		$opts[CURLOPT_CUSTOMREQUEST] = $method;
	    if ($params) {
			$opts[CURLOPT_POSTFIELDS] = http_build_query($params, null, '&');
		}
	}
    
	$opts[CURLOPT_URL] = $url;

    // disable the 'Expect: 100-continue' behaviour. This causes CURL to wait
    // for 2 seconds if the server does not support this header.
    if (isset($opts[CURLOPT_HTTPHEADER])) {
      $existing_headers = $opts[CURLOPT_HTTPHEADER];
      $existing_headers[] = 'Expect:';
      $opts[CURLOPT_HTTPHEADER] = $existing_headers;
    } else {
      $opts[CURLOPT_HTTPHEADER] = array('Expect:');
    }

    curl_setopt_array($ch, $opts);
    
	$result = curl_exec($ch);

    if ($result === false) {
      $e = new MeliApiException(array(
        'error_code' => curl_errno($ch),
        'error' => array('message' => curl_error($ch),
        'type' => 'CurlException',
        ),
      ));
      curl_close($ch);
      throw $e;
    }
    curl_close($ch);
    return $result;
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
  protected function getUrl($subdomain, $path='/', $params=array()) {
    $url = $subdomain.'.'.$this->getDomain() . $path;
  
    if ($params) {
      $url .= '?' . http_build_query($params, null, '&');
    }

    return 'https://'.$url;
  }

  /**
   * Build the URL for all APIs
   *
   * @param $path string Optional path (with slash)
   * @param $params array Optional query parameters
   *
   * @return string The URL for the given parameters
   */
  protected function getUrlForAPI($path, $params=array()) {
  	
	$path = str_replace('#{siteId}', $this->getSiteId(), $path);
	
    $url = self::$API_DOMAIN . $path;
    if ($params) {
      $url .= '?' . http_build_query($params, null, '&');
    }
    return $url;
  }

  /**
   * Returns the Current URL, stripping it of known FB parameters that should
   * not persist.
   *
   * @return string The current URL
   */
  protected function getCurrentUrl() {
    if (isset($_SERVER['HTTPS']) &&
        ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) ||
        isset($_SERVER['HTTP_X_FORWARDED_PROTO']) &&
        $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
      $protocol = 'https://';
    }
    else {
      $protocol = 'http://';
    }
    $currentUrl = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    $parts = parse_url($currentUrl);

    $query = '';
    if (!empty($parts['query'])) {
      // drop known fb params
      $params = explode('&', $parts['query']);
      $retained_params = array();
      foreach ($params as $param) {
        if ($this->shouldRetainParam($param)) {
          $retained_params[] = $param;
        }
      }

      if (!empty($retained_params)) {
        $query = '?'.implode($retained_params, '&');
      }
    }

    // use port if non default
    $port =
      isset($parts['port']) &&
      (($protocol === 'http://' && $parts['port'] !== 80) ||
       ($protocol === 'https://' && $parts['port'] !== 443))
      ? ':' . $parts['port'] : '';

    // rebuild
    return $protocol . $parts['host'] . $port . $parts['path'] . $query;
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
      if (strpos($param, $drop_query_param.'=') === 0) {
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
      case 'OAuthException':
        // OAuth 2.0 Draft 10 style
      case 'invalid_token':
        // REST server errors are just Exceptions
      case 'Exception':
        $message = $e->getMessage();
        if ((strpos($message, 'Error validating access token') !== false) ||
            (strpos($message, 'Invalid OAuth access token') !== false) ||
            (strpos($message, 'An active access token must be used') !== false)
        ) {
          $this->destroySession();
        }
        break;
    }

    throw $e;
  }


  /**
   * Prints to the error log if you aren't in command line mode.
   *
   * @param string $msg Log message
   */
  protected static function errorLog($msg) {
    // disable error log if we are running in a CLI environment
    // @codeCoverageIgnoreStart
    if (php_sapi_name() != 'cli') {
      error_log($msg);
    }
    // uncomment this if you want to see the errors on the page
    // print 'error_log: '.$msg."\n";
    // @codeCoverageIgnoreEnd
  }

  /**
   * Base64 encoding that doesn't need to be urlencode()ed.
   * Exactly the same as base64_encode except it uses
   *   - instead of +
   *   _ instead of /
   *
   * @param string $input base64UrlEncoded string
   * @return string
   */
  protected static function base64UrlDecode($input) {
    return base64_decode(strtr($input, '-_', '+/'));
  }

  /**
   * Destroy the current session
   */
  public function destroySession() {
    $this->accessToken = null;
    $this->signedRequest = null;
    $this->user = null;
    $this->clearAllPersistentData();

    // Javascript sets a cookie that will be used in getSignedRequest that we
    // need to clear if we can
    $cookie_name = $this->getSignedRequestCookieName();
    if (array_key_exists($cookie_name, $_COOKIE)) {
      unset($_COOKIE[$cookie_name]);
      if (!headers_sent()) {
        // The base domain is stored in the metadata cookie if not we fallback
        // to the current hostname
        $base_domain = '.'. $_SERVER['HTTP_HOST'];

        $metadata = $this->getMetadataCookie();
        if (array_key_exists('base_domain', $metadata) &&
            !empty($metadata['base_domain'])) {
          $base_domain = $metadata['base_domain'];
        }

        setcookie($cookie_name, '', 0, '/', $base_domain);
      } else {
        self::errorLog(
          'There exists a cookie that we wanted to clear that we couldn\'t '.
          'clear because headers was already sent. Make sure to do the first '.
          'API call before outputing anything'
        );
      }
    }
  }

  /**
   * Parses the metadata cookie that our Javascript API set
   *
   * @return  an array mapping key to value
   */
  protected function getMetadataCookie() {
    $cookie_name = $this->getMetadataCookieName();
    if (!array_key_exists($cookie_name, $_COOKIE)) {
      return array();
    }

    // The cookie value can be wrapped in "-characters so remove them
    $cookie_value = trim($_COOKIE[$cookie_name], '"');

    if (empty($cookie_value)) {
      return array();
    }

    $parts = explode('&', $cookie_value);
    $metadata = array();
    foreach ($parts as $part) {
      $pair = explode('=', $part, 2);
      if (!empty($pair[0])) {
        $metadata[urldecode($pair[0])] =
          (count($pair) > 1) ? urldecode($pair[1]) : '';
      }
    }

    return $metadata;
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
