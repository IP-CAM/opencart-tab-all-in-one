<?php
class ControllerModuleProductTab extends Controller {
  public function index($setting) {

    if(!isset($this->request->get['route']) || $this->request->get['route'] != 'product/product'){
    $this->document->addScript('catalog/view/javascript/jquery/tabs.js');
    }

    static $module = 0;

    $this->language->load('module/product_tab');
    
    $data['heading_title'] = $this->language->get('heading_title');

    $data['tab_latest'] = $this->language->get('tab_latest');
    $data['tab_featured'] = $this->language->get('tab_featured');
    $data['tab_bestseller'] = $this->language->get('tab_bestseller');
    $data['tab_special'] = $this->language->get('tab_special');


    $data['button_cart'] = $this->language->get('button_cart');
        
    $this->load->model('catalog/product');
    
    $this->load->model('tool/image');

    //Latest Products
    
    $data['latest_products'] = array();
    
    $latest_results = $this->model_catalog_product->getLatestProducts($setting['limit']);

    foreach ($latest_results as $result) {
      if ($result['image']) {
        $image = $this->model_tool_image->resize($result['image'], $setting['width'], $setting['height']);
      } else {
        $image = $this->model_tool_image->resize('placeholder.png', $setting['width'], $setting['height']);
      }
            
      if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
        $price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')));
      } else {
        $price = false;
      }
          
      if ((float)$result['special']) {
        $special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')));
      } else {
        $special = false;
      }
      
      if ($this->config->get('config_review_status')) {
        $rating = $result['rating'];
      } else {
        $rating = false;
      }
      
      $data['latest_products'][] = array(
        'product_id' => $result['product_id'],
        'thumb'      => $image,
        'name'       => $result['name'],
        'price'      => $price,
        'special'    => $special,
        'rating'     => $rating,
        'reviews'    => sprintf($this->language->get('text_reviews'), (int)$result['reviews']),
        'href'       => $this->url->link('product/product', 'product_id=' . $result['product_id']),
      );
    }

    //Specials product

    $data['special_products'] = array();

      $special_data = array(
      'sort'  => 'pd.name',
      'order' => 'ASC',
      'start' => 0,
      'limit' => $setting['limit']
    );

    $special_results = $this->model_catalog_product->getProductSpecials($special_data);

    foreach ($special_results as $result) {
      if ($result['image']) {
        $image = $this->model_tool_image->resize($result['image'], $setting['width'], $setting['height']);
      } else {
        $image = false;
      }
            
      if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
        $price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')));
      } else {
        $price = false;
      }
          
      if ((float)$result['special']) {
        $special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')));
      } else {
        $special = false;
      }
      
      if ($this->config->get('config_review_status')) {
        $rating = $result['rating'];
      } else {
        $rating = false;
      }
      
      $data['special_products'][] = array(
        'product_id' => $result['product_id'],
        'thumb'      => $image,
        'name'       => $result['name'],
        'price'      => $price,
        'special'    => $special,
        'rating'     => $rating,
        'reviews'    => sprintf($this->language->get('text_reviews'), (int)$result['reviews']),
        'href'       => $this->url->link('product/product', 'product_id=' . $result['product_id']),
      );
    }

    //BestSeller
    $data['bestseller_products'] = array();

    $bestseller_results = $this->model_catalog_product->getBestSellerProducts($setting['limit']);

    foreach ($bestseller_results as $result) {
      if ($result['image']) {
        $image = $this->model_tool_image->resize($result['image'], $setting['width'], $setting['height']);
      } else {
        $image = false;
      }
      
      if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
        $price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')));
      } else {
        $price = false;
      }
          
      if ((float)$result['special']) {
        $special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')));
      } else {
        $special = false;
      } 
      
      if ($this->config->get('config_review_status')) {
        $rating = $result['rating'];
      } else {
        $rating = false;
      }
              
      $data['bestseller_products'][] = array(
        'product_id' => $result['product_id'],
        'thumb'      => $image,
        'name'       => $result['name'],
        'price'      => $price,
        'special'    => $special,
        'rating'     => $rating,
        'reviews'    => sprintf($this->language->get('text_reviews'), (int)$result['reviews']),
        'href'       => $this->url->link('product/product', 'product_id=' . $result['product_id']),
      );
    }


    //Featured
    $data['featured_products'] = array();

    $products = explode(',', $this->config->get('featured_product'));   

    if (empty($setting['limit'])) {
      $setting['limit'] = 5;
    }
    
    $products = array_slice($products, 0, (int)$setting['limit']);
    
    foreach ($products as $product_id) {
      $product_info = $this->model_catalog_product->getProduct($product_id);
      
      if ($product_info) {
        if ($product_info['image']) {
          $image = $this->model_tool_image->resize($product_info['image'], $setting['width'], $setting['height']);
        } else {
          $image = false;
        }

        if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
          $price = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')));
        } else {
          $price = false;
        }
            
        if ((float)$product_info['special']) {
          $special = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')));
        } else {
          $special = false;
        }
        
        if ($this->config->get('config_review_status')) {
          $rating = $product_info['rating'];
        } else {
          $rating = false;
        }
          
        $data['featured_products'][] = array(
          'product_id' => $product_info['product_id'],
          'thumb'      => $image,
          'name'       => $product_info['name'],
          'price'      => $price,
          'special'    => $special,
          'rating'     => $rating,
          'reviews'    => sprintf($this->language->get('text_reviews'), (int)$product_info['reviews']),
          'href'       => $this->url->link('product/product', 'product_id=' . $product_info['product_id']),
        );
      }
    }

    $data['module'] = $module++;

    if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/product_tab.tpl')) {
      return $this->load->view($this->config->get('config_template') . '/template/module/product_tab.tpl', $data);
    } else {
      return $this->load->view('default/template/module/product_tab.tpl', $data);
    }
  }
}