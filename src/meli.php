<?php

require_once "base_meli.php";

/**
 * Extends the BaseMeli class with the intent of using
 * PHP sessions to store user ids and access tokens.
 */
class Meli extends BaseMeli
{

  public function __construct($config) {
    parent::__construct($config);
  }

}
