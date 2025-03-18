<?php

error_reporting(E_ERROR | E_PARSE);

$src_path = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Arbo';

require_once $src_path . DIRECTORY_SEPARATOR . 'Cart.php';
require_once $src_path . DIRECTORY_SEPARATOR . 'Item.php';

use Arbo\Cart;
use Arbo\Item;

$cart = Cart::factoryExample();

$item_list = [];

$items = $cart->getItems();
foreach ($items as $item) {
  $item_list[] = [
    'product_sku' => $item->getProductSku(),
    'product_name' => $item->getProductName(),
    'product_category' => $item->getProductCategory(),
    'quantity' => $item->getQuantity(),
  ];
}

$response = [
  'ecommerce_id' => $cart->getEcommerceId(),
  'customer_id' => $cart->getCustomerId(),
  'created_at' => $cart->getCreatedAt()->format('Y-m-d'),
  'status' => $cart->getStatus(),
  'price' => $cart->calcPrice(),
  'item_list' => $item_list
];

echo json_encode($response, JSON_PRETTY_PRINT);