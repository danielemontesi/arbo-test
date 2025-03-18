<?php

namespace Arbo;

require_once __DIR__ . DIRECTORY_SEPARATOR . 'Item.php';

use Arbo\Item;

class Cart {
  
  private int $ecommerce_id;
  private int $customer_id;
  private string $status;
  private \DateTime $created_at;
  private \DateTime $updated_at;
  private ?\DateTime $date_checkout = null;
  private string $customer_role;
  
  private array $articles;
  
  public function setEcommerceId(int $ecommerce_id)
  {
    $this->ecommerce_id = $ecommerce_id;
  }
  
  public function getEcommerceId() : int
  {
    return $this->ecommerce_id;
  }
  
  public function setCustomerId(int $customer_id)
  {
    $this->customer_id = $customer_id;
  }
  
  public function getCustomerId() : int
  {
    return $this->customer_id;
  }
  
  public function setStatus(string $status)
  {
    if ($status !== 'created' && $status !== 'building' && $status !== 'checkout')
      throw new Exception('Status ' . $status . ' not allowed');
    
    $this->status = $status;
  }
  
  public function getStatus() : string
  {    
    return $this->status;
  }
  
  public function setCreatedAt(\DateTime $created_at)
  {
    $this->created_at = $created_at;
  }
  
  public function getCreatedAt() : \DateTime
  {
    return $this->created_at;
  }
  
  public function setUpdatedAt(\DateTime $updated_at)
  {
    $this->updated_at = $updated_at;
  }
  
  public function setDateCheckout(\DateTime $date_checkout)
  {
    $this->date_checkout = $date_checkout;
  }
  
  public function getDateCheckout() : \DateTime
  {
    return $this->date_checkout;
  }
  
  public function setCustomerRole(string $customer_role)
  {
    if ($customer_role !== 'private' && $customer_role !== 'business')
      throw new Exception('Customer role ' . $customer_role . ' not allowed');
    
    $this->customer_role = $customer_role;
  }
  
  public function addItem(Item $item)
  {
    $this->items[] = $item;
  }
  
  public function getItems()
  {
    return $this->items;
  }
  
  private function isOneShotDay()
  {
    if ($this->date_checkout == null)
      return false;
    
    $week_day = $this->date_checkout->format("N");
    if ($week_day != 5)
      return false;

    $year = $this->date_checkout->format("Y");
    $month = $this->date_checkout->format("m");
    $last_friday_date = new DateTime("last Friday of $year-$month");

    return $this->date_checkout->format('Y-m-d') === $last_friday_date->format('Y-m-d');
  }
  
  public function calcPrice() : float
  {
    $category_discount_thresholds = [
      10 => 0.05,  // 5% per quantità superiori a 10
      25 => 0.10,  // 10% per quantità superiori a 25
      50 => 0.15,  // 15% per quantità superiori a 50
      100 => 0.20  // 20% per quantità superiori a 100
    ];
    
    $base_price = 100;
    $free_items = [];
    $gift_category = 3;
    $gift_threshold = 5;
    $one_shot_category = 1;
    $one_shot_price = 25;
    $one_shot_allowed_sku_list = ['SPECIAL001', 'SPECIAL002']; // Lista di SKU in promo "one-shot"
    $is_one_shot_day = $this->isOneShotDay();

    $recalc_items = [];
    foreach ($this->items as $item) {
        $sku = $item->getProductSku();
        $category = $item->getProductCategory();
        $quantity = $item->getQuantity();
        $price = 0;
        
        // Prezzo base standard
        if ($is_one_shot_day && $category == $one_shot_category && in_array($sku, $one_shot_allowed_sku_list)) {
            $price = $one_shot_price;
        } else {
            $price = $base_price;

            // Sconto quantità
            foreach (array_reverse($category_discount_thresholds, true) as $discount_quantity => $discount_perc) {
                if ($quantity > $discount_quantity) {
                    $price = $price * (1 - $discount_perc);
                    break;
                }
            }
        }
        
        $recalc_items[] = [
          'product_sku' => $sku,
          'category_id' => $category,
          'quantity' => $quantity,
          'price' => $price
        ];

        // Regalo per la categoria 3
        if ($category == $gift_category) {
            $multiples = intdiv($quantity, $gift_threshold);
            if ($multiples > 0) {
                $free_items[] = [
                    'product_sku' => $sku,
                    'category_id' => $category,
                    'quantity' => $multiples, // 1 gratis per ogni multiplo di 5
                    'price' => 0
                ];
            }
        }
    }

    $price = 0;
    $updated_items = array_merge($recalc_items, $free_items);
    foreach ($updated_items as $item)
      $price = $price + $item['price'];
    
    return $price;
  }
  
  public static function factory(int $ecommerce_id, int $customer_id, string $status, \DateTime $created_at, \DateTime $updated_at, string $customer_role) : self
  {
    $cart = new self();
    $cart->setEcommerceId($ecommerce_id);
    $cart->setCustomerId($customer_id);
    $cart->setStatus($status);
    $cart->setCreatedAt($created_at);
    $cart->setUpdatedAt($updated_at);
    $cart->setCustomerRole($customer_role);
    
    return $cart;
  }
  
  public static function factoryExample()
  {
    $ecommerce_id = 150;
    $customer_id = 350;
    $status = 'building';
    $created_at = new \DateTime('2025-03-17');
    $updated_at = new \DateTime('2025-03-18');
    $customer_role = 'private';

    $cart = self::factory($ecommerce_id, $customer_id, $status, $created_at, $updated_at, $customer_role);
    $cart->addItem(Item::factory('ABC123', 'Prodotto 1', 3, 10));
    $cart->addItem(Item::factory('XYZ789', 'Prodotto 2', 2, 3));
    $cart->addItem(Item::factory('SPECIAL001', 'Prodotto 3', 1, 5));
    
    return $cart;
  }
  
}