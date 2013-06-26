<?php

require_once "base_meli.php";

class SessionManager {

    public function start() {
        if (!session_id()) {
            session_start();
        }
    }
}

/**
 * Extends the BaseMeli class with the intent of using
 * PHP sessions to store user ids and access tokens.
 */
class Meli extends BaseMeli {
    /**
     * Identical to the parent constructor, except that
     * we start a PHP session to store the user ID and
     * access token if during the course of execution
     * we discover them.
     *
     * @param Array $config the application configuration.
     * @see BaseMeli::__construct in Meli.php
     */
    public function __construct($config, $sm = NULL) {
        if (is_null($sm))
            $sm = new SessionManager();
        $sm->start();
        parent::__construct($config);
    }

    protected static $kSupportedKeys = array('access_token', 'user_id');

    /**
     * Provides the implementations of the inherited abstract
     * methods.  The implementation uses PHP sessions to maintain
     * a store for authorization codes, user ids, CSRF states, and
     * access tokens.
     */
    protected function setPersistentData($key, $value) {
        if (!in_array($key, self::$kSupportedKeys)) {
            return;
        }

        $session_var_name = $this -> constructKeyName($key);
        $_SESSION[$session_var_name] = $value;
    }

    protected function getPersistentData($key, $default = false) {
        if (!in_array($key, self::$kSupportedKeys)) {
            return $default;
        }

        $session_var_name = $this -> constructKeyName($key);
        return isset($_SESSION[$session_var_name]) ? $_SESSION[$session_var_name] : $default;
    }

    protected function clearPersistentData($key) {
        if (!in_array($key, self::$kSupportedKeys)) {
            return;
        }

        $session_var_name = $this -> constructKeyName($key);
        unset($_SESSION[$session_var_name]);
    }

    protected function clearAllPersistentData() {
        foreach (self::$kSupportedKeys as $key) {
            $this -> clearPersistentData($key);
        }
    }

    protected function constructKeyName($key) {
        return implode('_', array('ml', $this -> getAppId(), $key));
    }

}
