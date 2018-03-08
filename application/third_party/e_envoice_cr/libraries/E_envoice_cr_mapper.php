<?php

require_once dirname(__DIR__) . '/config/Hacienda_constants.php';
require_once dirname(__DIR__) . '/vendor/autoload.php';

use League\ISO3166\ISO3166;

/**
 * Description of E_envoice_cr_Invoice
 *
 * @author pdev
 */
class E_envoice_cr_mapper {

  private $_document;
  private $_emitter;
  private $_client;
  private $_cart;
  private $_ci;
  private $_doc_type;
  private $_doc_number;
  private $_doc_key;
  private $_doc_consecutive;
  private $_secure_code;
  private $_comments;

  public function __construct() {
    $this->_ci = & get_instance();
    $this->_document = array();
    $this->_emitter = array();
    $this->_client = array();
    $this->_cart = array();
    $this->_comments = array();
    $this->_ci->load->add_package_path(APPPATH . 'third_party/e_envoice_cr/');
    $this->_ci->load->helper('invoice');
    $this->_ci->load->model('Appconfig');
  }

  public function mapSale(&$data, $sale_type, $client_id) {
    $this->loadDocumentType($sale_type);
    $this->loadDocumentNumber();
    $this->_secure_code = $this->generateSecureCode();
    $this->_doc_consecutive = $this->generateConsecutivo();
    $this->_doc_key = $this->generateClave();
    $this->loadDocumentData($data);
    $this->loadEmitterData();
    $this->loadClientData($data, $client_id);
    $this->loadCart($data, $client_id);
    $this->calculateInvoiceSummary();
  }

  public function getDocumentData() {
    return $this->_document;
  }

  public function getDocumentType() {
    return $this->_doc_type;
  }

  public function getEmitterData() {
    return $this->_emitter;
  }

  public function getClientData() {
    return $this->_client;
  }

  public function getCartData() {
    return $this->_cart;
  }

  public function getSecureCode() {
    return $this->_secure_code;
  }

  public function getDocumentKey() {
    return $this->_doc_key;
  }

  public function increaseDocumentNumber() {
    $clean_counter = ltrim($this->_doc_number, '0');
    $counter = intval($clean_counter);
    $counter++;
    $string_counter = (string) $counter;
    $sub_counter = strlen($string_counter) > 10 ? substr($string_counter, -10) : $string_counter;
    $new_counter = intval($sub_counter);
    if (0 === $new_counter) {
      $new_counter = 1;
    }

    $new_number = format_document_number($new_counter, 10);
    switch ($this->_doc_type) {
      case Hacienda_constants::DOCUMENT_TYPE_FE:
        $key = 'e_envoice_cr_consecutive_fe';
        break;
      case Hacienda_constants::DOCUMENT_TYPE_CODE_TE:
        $key = 'e_envoice_cr_consecutive_te';
        break;
      case Hacienda_constants::DOCUMENT_TYPE_CODE_NC:
        $key = 'e_envoice_cr_consecutive_nc';
        break;
      case Hacienda_constants::DOCUMENT_TYPE_CODE_ND:
        $key = 'e_envoice_cr_consecutive_nd';
        break;
      default:
        $key = '';
        break;
    }

    $this->_ci->Appconfig->save($key, $new_number);
  }

  protected function loadDocumentData(&$data) {
    $this->_document['consecutive'] = $this->_doc_consecutive;
    $this->_document['key'] = $this->_doc_key;
    $this->_document['date'] = $this->generateFechaEmision($data);
    $this->_document['condition'] = $this->getCondicionVenta($data);
    $this->_document['pay_types'] = $this->getMedioPago($data);
    $this->_document['document_code'] = Hacienda_constants::get_code_by_document_type($this->_doc_type);
    $this->_document['resolution'] = $this->getNormativa();
    $this->_document['others'] = $this->getOtros($data);

    $country_code = $this->_ci->Appconfig->get('country_codes');
    $country_data = (new ISO3166())->alpha2($country_code);
    $this->_document['currency_code'] = $country_data['currency'][0];
    $this->_document['currency_rate'] = 0.0;
    $this->_document['tsg'] = 0.0;
    $this->_document['tse'] = 0.0;
    $this->_document['tmg'] = 0.0;
    $this->_document['tme'] = 0.0;
    $this->_document['tg'] = 0.0;
    $this->_document['te'] = 0.0;
    $this->_document['tv'] = 0.;
    $this->_document['td'] = 0.0;
    $this->_document['tvn'] = 0.0;
    $this->_document['ti'] = 0.0;
    $this->_document['tc'] = 0.0;
  }

  protected function loadDocumentType($sale_type) {
    switch ($sale_type) {
      case SALE_TYPE_INVOICE:
        $this->_doc_type = Hacienda_constants::DOCUMENT_TYPE_FE;
        break;
      case SALE_TYPE_POS:
        $this->_doc_type = Hacienda_constants::DOCUMENT_TYPE_TE;
        break;
      default :
        $this->_doc_type = '';
        break;
    }
  }

  protected function generateClave() {
    $id = $this->_ci->Appconfig->get('e_envoice_cr_id');
    $id_user = format_document_number($id, 12);
    $key = generate_document_key($this->_doc_consecutive, $this->_secure_code, $id_user);
    return $key;
  }

  protected function loadDocumentNumber() {
    $key = '';
    $type = $this->_doc_type;
    switch ($type) {
      case Hacienda_constants::DOCUMENT_TYPE_FE:
        $key = 'e_envoice_cr_consecutive_fe';
        break;
      case Hacienda_constants::DOCUMENT_TYPE_TE:
        $key = 'e_envoice_cr_consecutive_te';
        break;
      case Hacienda_constants::DOCUMENT_TYPE_NC:
        $key = 'e_envoice_cr_consecutive_nc';
        break;
      case Hacienda_constants::DOCUMENT_TYPE_ND:
        $key = 'e_envoice_cr_consecutive_nd';
        break;
      default:
        $key = '';
        break;
    }
    $this->_doc_number = $this->_ci->Appconfig->get($key);

    if (is_null($this->_doc_number) || empty($this->_doc_number)) {
      $this->_doc_number = format_document_number(1, 10);
      $this->_ci->Appconfig->save($key, $this->_doc_number);
    }
  }

  protected function generateConsecutivo() {
    $sucursal = 1;
    $terminal = 1;
    $doc_code = Hacienda_constants::get_code_by_document_type($this->_doc_type);
    $doc_number = $this->_doc_number;
    $consecutive = generate_document_consecutive($sucursal, $terminal, $doc_code, $doc_number);
    return $consecutive;
  }

  protected function generateSecureCode() {
    $time = time();
    $s_time = (string) $time;
    $tmp_code = strlen($s_time) > 8 ? substr($s_time, -8) : $s_time;
    $code = format_document_number($tmp_code, 8);
    return $code;
  }

  protected function generateFechaEmision(&$data) {
    $date = format_document_date($data['transaction_time']);
    return $date;
  }

  protected function getCondicionVenta($data) {
    if (true == $data['payments_cover_total']) {
      return '01';
    }
    return '99';
  }

  protected function getMedioPago($data) {
    $payments = array();
    $payment_type_cash = $this->_ci->lang->line('sales_cash');
    $payment_type_check = $this->_ci->lang->line('sales_check');
    $payment_type_credit_card = $this->_ci->lang->line('sales_credit');
    $payment_type_debit_card = $this->_ci->lang->line('sales_debit');
    foreach ($data['payments'] as $pay_type) {
      switch ($pay_type['payment_type']) {
        case $payment_type_cash:
          array_push($payments, '01');
          break;
        case $payment_type_credit_card:
        case $payment_type_debit_card:
          array_push($payments, '02');
          break;
        case $payment_type_check:
          array_push($payments, '03');
          break;
        default:
          array_push($payments, '99');
          list($type, $id) = explode(":", $pay_type['payment_type']);
          $amount = $pay_type['payment_amount'];
          $comment = array(
            'code' => '99',
            'text' => "Medio de pago: $type con valor de $amount",
          );
          array_push($this->_comments, $comment);
          break;
      }
    }
    return array_unique($payments);
  }

  protected function getNormativa() {
    $number = $this->_ci->Appconfig->get('e_envoice_cr_resolution_number');
    $date = $this->_ci->Appconfig->get('e_envoice_cr_resolution_date');
    $resolution = array(
      'number' => $number,
      'date' => $date,
    );
    return $resolution;
  }

  protected function getOtros(&$data) {
    $others = array();
    if (!empty($data['comments'])) {
      array_push($others, $data['comments']);
    }
    if (array_key_exists('customer_comments', $data)) {
      array_push($others, $data["customer_comments"]);
    }
    foreach ($this->_comments as $comment) {
      array_push($others, $comment);
    }
    return $others;
  }

  protected function loadEmitterData() {
    $name = $this->_ci->Appconfig->get('company');
    $commercial_name = $this->_ci->Appconfig->get('e_envoice_cr_name');
    $id = $this->_ci->Appconfig->get('e_envoice_cr_id');
    $id_type = $this->_ci->Appconfig->get('e_envoice_cr_id_type');
    $email = $this->_ci->Appconfig->get('email');
    $province = $this->_ci->Appconfig->get('e_envoice_cr_address_province');
    $canton = $this->_ci->Appconfig->get('e_envoice_cr_address_canton');
    $distrit = $this->_ci->Appconfig->get('e_envoice_cr_address_distrit');
    $neighborhood = $this->_ci->Appconfig->get('e_envoice_cr_address_neighborhood');
    $otras_senas = $this->_ci->Appconfig->get('e_envoice_cr_address_other');
    $phone = $this->_ci->Appconfig->get('phone');
    $fax = $this->_ci->Appconfig->get('fax');
    if (strlen($otras_senas) > 160) {
      $otras_senas = substr($otras_senas, 0, 160);
    }

    $this->_emitter['name'] = $name;
    $this->_emitter['id'] = array('type' => $id_type, 'number' => $id);
    $this->_emitter['commercialName'] = $commercial_name;
    $this->_emitter['email'] = $email;
    $this->_emitter['phone'] = $this->mapPhoneNumber($phone);
    $this->_emitter['fax'] = $this->mapPhoneNumber($fax);
    $this->_emitter['location'] = array(
      'prov' => format_document_number($province, 1),
      'cant' => format_document_number($canton, 2),
      'dist' => format_document_number($distrit, 2),
      'barr' => format_document_number($neighborhood, 2),
      'other' => $otras_senas,
    );
  }

  protected function loadClientData(&$data, $client_id) {
    if (-1 == $client_id) {
      return;
    }

    $customer = $this->_ci->Customer->get_info($client_id);
    $name = $data['first_name'] . ' ' . $data['last_name'];
    $commercial_name = strcasecmp($name, $data['customer']) != 0 ? $data['customer'] : '';
    $email = $data['customer_email'];
    $this->_client['name'] = $name;
    $this->_client['id'] = array();
    $this->_client['commercialName'] = $commercial_name;
    $this->_client['email'] = $email;
    $this->_client['phone'] = $this->mapPhoneNumber($customer->phone_number);
    $this->_client['fax'] = array();
    $this->_client['location'] = array();
  }

  protected function loadCart(&$data, $client_id) {
    $this->_ci->load->helper('locale');
    $this->_ci->load->library('sale_lib');
    $this->_ci->load->library('tax_lib');
    $this->_ci->load->model('Customer');
    $this->_ci->load->model('Item_taxes');
    $this->_ci->load->model('Item');
    $this->_ci->load->model('Tax');
    foreach ($data['cart'] as $item) {
      $line = $this->loadItem($item, $client_id);
      array_push($this->_cart, $line);
    }
  }

  protected function loadItem(&$item, $client_id) {
    $discount = doubleval($item['discount']);
    $quantity = doubleval($item['quantity']);
    $price = doubleval($item['price']);
    $total_amount = $quantity * $price;
    $discount_amount = $total_amount * ($discount / 100);
    $subtotal = $total_amount - $discount_amount;
    $tax_amount = 0.0;
    $taxes = $this->getItemTaxes($item, $client_id, $tax_amount);
    $line_total_amount = $subtotal + $tax_amount;
    $customer_discount = 0.0;
    if ($this->_ci->Customer->exists($client_id)) {
      $customer_discount = $this->_ci->Customer->get_info($client_id)->discount_percent;
    }

    $line = array(
      'line' => $item['line'],
      'quantity' => round($quantity, 3),
      'detail' => $item['name'],
      'price' => round($price, 5),
      'code' => array('type' => '04', 'number' => $item['item_number']),
      'unit' => 'Unid',
      'total' => round($total_amount, 5),
      'subtotal' => round($subtotal, 5),
      'line_total_amount' => round($line_total_amount, 5),
      'discount' => array(),
      'taxes' => $taxes,
    );

    if ($discount_amount <> 0.0) {
      $line['discount']['amount'] = round($discount_amount, 5);
      $line['discount']['reason'] = (0.0 <> $customer_discount) ? 'Descuento a cliente' : 'Descuento general';
    }
    $this->addLineToSummary($item['stock_type'], $discount_amount, $tax_amount, $total_amount);
    return $line;
  }

  protected function getItemTaxes(&$item, $client_id, &$total_tax_line) {
    $customer = $this->_ci->Customer->get_info($client_id);
    $general_taxes = $this->getGeneralTaxes($item, $customer, $total_tax_line);
    $specific_taxes = $this->getSpecificTaxes($item, $total_tax_line);
    return $general_taxes + $specific_taxes;
  }

  protected function getGeneralTaxes(&$item, &$customer, &$total_tax_line) {
    $taxes = array();
    $register_mode = $this->_ci->config->item('default_register_mode');
    if ($this->_ci->config->item('customer_sales_tax_support') == '1') {
      $tax_code = $this->_ci->tax_lib->get_applicable_tax_code($register_mode, $customer->city, $customer->state, $customer->sales_tax_code);
      if ($tax_code != '' && $item['price'] != 0) {
        $tax_rate = 0.0000;
        $rounding_code = Rounding_mode::HALF_UP;

        $tax_code_obj = $this->_ci->Tax->get_info($tax_code);
        $tax_category_id = $item['tax_category_id'];

        if ($tax_category_id != 0) {
          $tax_rate_info = $this->_ci->Tax->get_rate_info($tax_code, $tax_category_id);
          if ($tax_rate_info) {
            $tax_rate = $tax_rate_info->tax_rate;
            $rounding_code = $tax_rate_info->rounding_code;
          }
          else {
            $tax_rate = $tax_code_obj->tax_rate;
            $rounding_code = $tax_code_obj->rounding_code;
          }
        }

        if ($tax_category_id != 0) {
          $tax_rate_info = $this->_ci->Tax->get_rate_info($tax_code, $tax_category_id);
          $tax_rate = $tax_rate_info->tax_rate;
          $rounding_code = $tax_rate_info->rounding_code;
          $tax_group_sequence = $tax_rate_info->tax_group_sequence;
          $tax_category = $tax_rate_info->tax_category;
        }
        else {
          $tax_rate = $tax_code_obj->tax_rate;
          $rounding_code = $tax_code_obj->rounding_code;
          $tax_group_sequence = $tax_code_obj->tax_group_sequence;
          $tax_category = $tax_code_obj->tax_category;
        }

        $decimals = tax_decimals();

        // The tax basis should be returned at the currency scale
        $tax_basis = $this->_ci->sale_lib->get_item_total($item['quantity'], $item['price'], $item['discount'], TRUE);
        $tax_amount = $this->_ci->tax_lib->get_sales_tax_for_amount($tax_basis, $tax_rate, $rounding_code, $decimals);


        if ($tax_amount <> 0) {
          $tax_line = array(
            'code' => $tax_code,
            'rate' => round($tax_rate, 2),
            'amount' => round($tax_amount, 5),
          );
          array_push($taxes, $tax_line);
          $total_tax_line += $tax_amount;
        }
      }
    }
    return $taxes;
  }

  protected function getSpecificTaxes(&$item, &$total_tax_line) {
    $tax_info = $this->_ci->Item_taxes->get_info($item['item_id']);
    $tax_decimals = tax_decimals();
    $taxes = array();
    foreach ($tax_info as $tax) {
      // This computes tax for each line item and adds it to the tax type total
      $tax_basis = $this->_ci->sale_lib->get_item_total($item['quantity'], $item['price'], $item['discount'], TRUE);
      $tax_amount = 0;

      if ($this->_ci->config->item('tax_included')) {
        $tax_amount = $this->_ci->tax_lib->get_item_tax($item['quantity'], $item['price'], $item['discount'], $tax['percent']);
      }
      elseif ($this->_ci->config->item('customer_sales_tax_support') == '0') {
        $tax_amount = $this->_ci->tax_lib->get_sales_tax_for_amount($tax_basis, $tax['percent'], '0', $tax_decimals);
      }

      if ($tax_amount <> 0) {
        $total_tax_line += $tax_amount;
        $tax_line = array(
          'code' => $tax['name'],
          'rate' => round($tax['percent'], 2),
          'amount' => round($tax_amount, 5),
        );
        array_push($taxes, $tax_line);
      }
    }

    return $taxes;
  }

  protected function addLineToSummary($stock_type, $line_discount_amount, $line_tax_amount, $line_total) {
    $amount_mg = 0.0;
    $amount_me = 0.0;
    $amount_sg = 0.0;
    $amount_se = 0.0;
    if ($stock_type != '0') { // non-stock item ~ service
      if ($line_tax_amount <> 0.0) {
        $amount_sg += $line_total;
      }
      else {
        $amount_se += $line_total;
      }
    }
    else {
      if ($line_tax_amount <> 0.0) {
        $amount_mg += $line_total;
      }
      else {
        $amount_me += $line_total;
      }
    }

    $this->_document['tmg'] += $amount_mg;
    $this->_document['tme'] += $amount_me;
    $this->_document['tsg'] += $amount_sg;
    $this->_document['tse'] += $amount_se;
    $this->_document['td'] += $line_discount_amount;
    $this->_document['ti'] += $line_tax_amount;
  }

  protected function calculateInvoiceSummary() {
    $this->_document['tg'] = $this->_document['tsg'] + $this->_document['tmg'];
    $this->_document['te'] = $this->_document['tse'] + $this->_document['tme'];
    $this->_document['tv'] = $this->_document['tg'] + $this->_document['te'];
    $this->_document['tvn'] = $this->_document['tv'] - $this->_document['td'];
    $this->_document['tc'] = $this->_document['tvn'] + $this->_document['ti'];
  }

  protected function mapPhoneNumber($phone) {
    $phone_data = array();
    $replace = array(' ', '+', '-', '(', ')');
    $clean_phone = str_replace($replace, '', $phone);
    $phone_lenght = strlen($clean_phone);
    switch ($phone_lenght) {
      case 8:
        $phone_data = array(
          'code' => '506',
          'number' => $clean_phone,
        );
        break;
      case 11:
        $phone_data = array(
          'code' => substr($clean_phone, 0, 3),
          'number' => substr($clean_phone, -8),
        );
      default:
        break;
    }
    return $phone_data;
  }

}
