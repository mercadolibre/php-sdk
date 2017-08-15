<?php

echo '<a href="examples/example_login.php"><b>Login using MercadoLibre oAuth 2.0</b></a><br>';
echo '<a href="examples/example_get.php"><b>GET using MercadoLibre oAuth 2.0</b></a><br>';
echo '<a href="examples/example_list_item.php"><b>List Item using MercadoLibre oAuth 2.0</b></a><br>';
echo '<a href="examples/example_put_description.php"><b>PUT using MercadoLibre oAuth 2.0</b></a><br>';
echo '<a href="examples/example_delete_question.php"><b>Delete question using MercadoLibre oAuth 2.0</b></a><br>';

echo "<br>App_ID<br>";
echo getenv('App_ID');

echo "<br>Secret_Key<br>";
echo getenv('Secret_Key');

echo "<br>Redirect_URI<br>";
echo getenv('Redirect_URI');

echo "<br><br>";
include_once "examples/example_delete_question.php";
echo "<br><br>";
include_once "examples/example_get.php";
echo "<br><br>";
include_once "examples/example_list_item.php";
echo "<br><br>";
include_once "examples/example_login.php";
echo "<br><br>";
include_once "examples/example_put_description.php";