<?php
require '../Meli/meli.php';

$meli = new Meli('3331309901577719', 'Q8yipQGFimWHshcs4e69KZRn4pXpQpL2');

$params = array();

$result = $meli->get('/sites/MLB', $params);

echo '<pre>';
print_r($result);
echo '</pre>';