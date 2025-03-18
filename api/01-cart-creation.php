<?php

error_reporting(E_ERROR | E_PARSE);

$src_path = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Arbo';

require_once $src_path . DIRECTORY_SEPARATOR . 'Cart.php';

use Arbo\Cart;

$cart = null;

$request = json_decode(file_get_contents('php://input'), true);

if ($request == null) {
  // Creating example cart
  $cart = Cart::factoryExample();
} else {  
  // Reading request data
  $ecommerce_id = $request['ecommerce_id'];
  $customer_id = $request['customer_id'];
  $status = $request['status']; // `created`, `building`, `checkout`
  $created_at = $request['created_at'];
  $updated_at = $request['updated_at'];
  $customer_role = $request['customer_role']; // `private`, `business`
  
  $cart = Cart::factory($ecommerce_id, $customer_id, $status, $created_at, $updated_at, $customer_role);
}

$response = ['message' => 'Cart created: ' . $cart->getEcommerceId()];

echo json_encode($response, JSON_PRETTY_PRINT);