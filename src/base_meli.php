<?php

if (!function_exists('curl_init')) {
  throw new Exception('MeliPHP needs the CURL PHP extension.');
}
if (!function_exists('json_decode')) {
  throw new Exception('MeliPHP needs the JSON PHP extension.');
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
    parent::__construct('Error', 0);
  }

  /**
   * Return the associated result object returned by the API server.
   *
   * @return array The result from the API server
   */
  public function getResult() {
    return $this->result;
  }

}

/**
 * Provides access to the MercadoLibre Platform.  This class provides
 * a majority of the functionality needed, but the class is abstract
 * because it is designed to be sub-classed.
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
   * List of query parameters that get automatically dropped when rebuilding
   * the current URL.
   */
  protected static $DROP_QUERY_PARAMS = array(
    'code',
    'state',
    'signed_request',
  );

  /**
   * Maps aliases to MercadoLibre domains.
   */
  public static $DOMAIN_MAP = array(
    'api'       => 'https://api.mercadolibre.com/',
  );

  /**
   * Maps country to MercadoLibre sites.
   */
  public static $COUNTRY_MAP = array(
    'ar'       => 'MLA',
    'br'       => 'MLB',
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
  protected $user;

  /**
   * The data from the signed_request token.
   */
  protected $signedRequest;

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
  	return self::$COUNTRY_MAP[$this->countryId];
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
    $this->accessToken = $access_token;
    return $this;
  }
 
  /**
   * Make an API call.
   *
   * @return mixed The decoded response
   */
  public function api(/* polymorphic */) {
	$args = func_get_args();
	return call_user_func_array(array($this, '_api'), $args);
  }

  /**
   * Invoke the API.
   *
   * @param string $path The path (required)
   * @param string $method The http method (default 'GET')
   * @param array $params The query/post data
   *
   * @return mixed The decoded response object
   * @throws MeliApiException
   */
  protected function _api($path, $method = 'GET', $params = array()) {
    if (is_array($method) && empty($params)) {
      $params = $method;
      $method = 'GET';
    }
    $params['method'] = $method; // method override as we always do a POST

	$path = str_replace("#{siteId}", $this->getSiteId(), $path);

	$domainKey = 'api';

   	$result = json_decode($this->_oauthRequest(
      $this->getUrl($domainKey, $path),
      $params
    ), true);

   return $result;

  }

  /**
   * Make a OAuth Request.
   *
   * @param string $url The path (required)
   * @param array $params The query/post data
   *
   * @return string The decoded response object
   * @throws MeliApiException
   */
  protected function _oauthRequest($url, $params) {
    
	//if (!isset($params['access_token'])) {
    //  $params['access_token'] = $this->getAccessToken();
    //}

    // json_encode all params values that are not strings
    //foreach ($params as $key => $value) {
    //  if (!is_string($value)) {
    //    $params[$key] = json_encode($value);
    //  }
    //}

    return $this->makeRequest($url, $params);
  }

  /**
   * Makes an HTTP request. This method can be overridden by subclasses if
   * developers want to do fancier things or use something other than curl to
   * make the request.
   *
   * @param string $url The URL to make the request to
   * @param array $params The parameters to use for the POST body
   * @param CurlHandler $ch Initialized curl handle
   *
   * @return string The response text
   */
  protected function makeRequest($url, $params, $ch=null) {
    if (!$ch) {
      $ch = curl_init();
    }

    $opts = self::$CURL_OPTS;

	$opts[CURLOPT_POSTFIELDS] = http_build_query($params, null, '&');
    
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
   * Build the URL for given domain alias, path and parameters.
   *
   * @param $name string The name of the domain
   * @param $path string Optional path (without a leading slash)
   * @param $params array Optional query parameters
   *
   * @return string The URL for the given parameters
   */
  protected function getUrl($name, $path='', $params=array()) {
    $url = self::$DOMAIN_MAP[$name];
    if ($path) {
      if ($path[0] === '/') {
        $path = substr($path, 1);
      }
      $url .= $path;
    }
    if ($params) {
      $url .= '?' . http_build_query($params, null, '&');
    }

    return $url;
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

}
