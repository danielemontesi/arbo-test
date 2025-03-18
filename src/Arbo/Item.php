<?php

namespace Arbo;

class Item {
  
  private string $product_sku;
  private string $product_name;
  private int $product_category;
  private int $quantity;
  
  const CATEGORY_SPARE_PARTS = 1;
  const CATEGORY_REFRIGERATION = 2;
  const CATEGORY_PHOTOVOLTAIC = 3;
    
  public function setProductSku(string $product_sku)
  {    
    $this->product_sku = $product_sku;
  }
  
  public function getProductSku() : string
  {    
    return $this->product_sku;
  }
  
  public function setProductName(string $product_name)
  {    
    $this->product_name = $product_name;
  }
  
  public function getProductName() : string
  {    
    return $this->product_name;
  }
  
  public function setProductCategory(int $product_category)
  {
    if ($product_category !== self::CATEGORY_SPARE_PARTS && $product_category !== self::CATEGORY_REFRIGERATION
      && $product_category !== self::CATEGORY_PHOTOVOLTAIC)
      throw new Exception('Product category ' . $product_category . ' not allowed');
    
    $this->product_category = $product_category;
  }
  
  public function getProductCategory() : int
  {
    return $this->product_category;
  }
  
  public function setQuantity(int $quantity)
  {   
    $this->quantity = $quantity;
  }
  
  public function getQuantity() : int
  {
    return $this->quantity;
  }
  
  public static function factory(string $product_sku, string $product_name, int $product_category, int $quantity) : self
  {
    $item = new self();
    $item->setProductSku($product_sku);
    $item->setProductName($product_name);
    $item->setProductCategory($product_category);
    $item->setQuantity($quantity);
    
    return $item;
  }
  
}