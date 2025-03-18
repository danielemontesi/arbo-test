<?php

error_reporting(E_ERROR | E_PARSE);

$src_path = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Arbo';

require_once $src_path . DIRECTORY_SEPARATOR . 'Cart.php';
require_once $src_path . DIRECTORY_SEPARATOR . 'Item.php';

use Arbo\Cart;
use Arbo\Item;

$request = json_decode(file_get_contents('php://input'), true);
if ($request == null) {
  // Example item arguments
  $product_sku = 'KCE493';
  $product_name = 'Prodotto 80';
  $product_category = 5;
  $quantity = 20;
} else {
  // Reading request data
  $product_sku = $request['product_sku'];
  $product_name = $request['product_name'];
  $product_category = $request['product_category'];
  $quantity = $request['quantity'];
}

// Creating item
$item = Item::factory($product_sku, $product_name, $product_category, $quantity);

// Adding item to cart
$cart = Cart::factoryExample();
$cart->addItem($item);

$response = ['message' => 'Item ' . $item->getProductSku() . ' added to cart'];

echo json_encode($response, JSON_PRETTY_PRINT);