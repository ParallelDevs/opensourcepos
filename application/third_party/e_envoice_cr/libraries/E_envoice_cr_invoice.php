<?php

require_once dirname(__DIR__) . '/config/Hacienda_constants.php';
require_once dirname(__DIR__) . '/vendor/autoload.php';

use Rinvex\Country\Country;

/**
 * Description of E_envoice_cr_Invoice
 *
 * @author pdev
 */
class E_envoice_cr_invoice {

  private $_invoice;
  private $_emitter;
  private $_client;
  private $_cart;
  private $_ci;
  private $_doc_type;

  public function __construct() {
    $this->_ci = & get_instance();
    $this->_invoice = array();
    $this->_emitter = array();
    $this->_client = array();
    $this->_cart = array();
    $this->_ci->load->add_package_path(APPPATH . 'third_party/e_envoice_cr/');
    $this->_ci->load->helper('invoice');
    $this->_ci->load->model('Appconfig');
  }

  public function mapSale(&$data, $sale_type) {
    $this->loadDocumentType($sale_type);
    $this->loadInvoiceData($data);
    $this->loadEmitterData();
    $this->loadClientData($data);
    $this->loadCart($data);
  }

  public function getInvoiceData() {
    return $this->_invoice;
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

  protected function loadInvoiceData(&$data) {
    $this->_invoice['consecutive'] = $this->generateConsecutivo($data);
    $this->_invoice['key'] = $this->generateClave($data, $this->_invoice['consecutive']);
    $this->_invoice['date'] = $this->generateFechaEmision($data);
    $this->_invoice['condition'] = $this->getCondicionVenta($data);
    $this->_invoice['pay_types'] = $this->getMedioPago($data);
    $this->_invoice['document_code'] = Hacienda_constants::get_code_by_document_type($this->_doc_type);
    $this->_invoice['code'] = '02';
    $this->_invoice['reason'] = 'a';
    $this->_invoice['resolution'] = $this->getNormativa();
    $this->_invoice['others'] = $this->getOtros($data);

    $country_code = $this->_ci->Appconfig->get('country_codes');
    $country = country($country_code);
    $currency_info = $country->getCurrency();
    $this->_invoice['currency_code'] = $currency_info['iso_4217_code'];
    $this->_invoice['currency_rate'] = 0;
    $this->_invoice['tsg']=0;
    $this->_invoice['tse']=0;
    $this->_invoice['tmg']=0;
    $this->_invoice['tme']=0;
    $this->_invoice['tg']=0;
    $this->_invoice['te']=0;
    $this->_invoice['tv']=0;
    $this->_invoice['td']=0;
    $this->_invoice['tvn']=0;
    $this->_invoice['ti']=0;
    $this->_invoice['tc']=0;
  }

  protected function loadDocumentType($sale_type) {
    switch ($sale_type) {
      case 1://SALE_TYPE_INVOICE
        $this->_doc_type = Hacienda_constants::DOCUMENT_TYPE_FE;
        break;
      case 0://SALE_TYPE_POS
      case 2://SALE_TYPE_WORK_ORDER
      case 3://SALE_TYPE_QUOTE
      case 4://SALE_TYPE_RETURN
      default :
        $this->_doc_type = '';
        break;
    }
  }

  protected function generateClave(&$data, $consecutive) {
    $secure_code = format_invoice_number($data['invoice_number'], 8);
    $id = $this->_ci->Appconfig->get('e_envoice_cr_id');
    $id_user = format_invoice_number($id, 12);
    $key = generate_invoice_key($consecutive, $secure_code, $id_user);
    return $key;
  }

  protected function generateConsecutivo(&$data) {
    $sucursal = 1;
    $terminal = 1;
    $doc_type = Hacienda_constants::get_code_by_document_type($this->_doc_type);
    $consecutive = generate_invoice_consecutive($sucursal, $terminal, $doc_type, $data['invoice_number']);
    return $consecutive;
  }

  protected function generateFechaEmision(&$data) {
    $date = format_invoice_date($data['transaction_time']);
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
    foreach ($data['payments'] as $pay_type) {
      switch ($pay_type['payment_type']) {
        case 'Cash':
          array_push($payments, '01');
          break;
        case 'Debit Card':
        case 'Credit Card':
          array_push($payments, '02');
          break;
        case 'Check':
          array_push($payments, '03');
          break;
        case 'Due':
        case 'Gift Card':
        default:
          array_push($payments, '99');
          break;
      }
    }
    return array_unique($payments);
  }

  protected function getNormativa() {
    $resolution = array(
      'number' => 'DGT-R-48-2016',
      'date' => '12-12-2016 08:08:12',
    );
    return $resolution;
  }

  protected function getOtros(&$data) {
    $others = array();
    if (!empty($data['comments'])) {
      $others[] = $data['comments'];
    }
    if (array_key_exists('customer_comments', $data)) {
      $others[] = $data["customer_comments"];
    }
    return $others;
  }

  protected function loadEmitterData() {
    $name = $this->_ci->Appconfig->get('company');
    $commercial_name = $this->_ci->Appconfig->get('e_envoice_cr_name');
    $id = $this->_ci->Appconfig->get('e_envoice_cr_id');
    $id_type = $this->_ci->Appconfig->get('e_envoice_cr_id_type');
    $email = $this->_ci->Appconfig->get('email');
    $otras_senas = $this->_ci->Appconfig->get('address');
    if (strlen($otras_senas) > 160) {
      $otras_senas = substr($otras_senas, 0, 160);
    }

    $this->_emitter['name'] = $name;
    $this->_emitter['id'] = array('type' => $id_type, 'number' => $id);
    $this->_emitter['commercialName'] = $commercial_name;
    $this->_emitter['email'] = $email;
    $this->_emitter['phone'] = array();
    $this->_emitter['fax'] = array();
    $this->_emitter['location'] = array(
      'prov' => 1,
      'cant' => format_invoice_number(13, 2),
      'dist' => format_invoice_number(3, 2),
      'other' => $otras_senas,
    );
  }

  protected function loadClientData(&$data) {
    $name = $data['first_name'] . ' ' . $data['last_name'];
    $commercial_name = strcasecmp($name, $data['customer']) != 0 ? $data['customer'] : '';
    $email = $data['customer_email'];
    $this->_client['name'] = $name;
    $this->_client['id'] = array();
    $this->_client['commercialName'] = $commercial_name;
    $this->_client['email'] = $email;
    $this->_client['phone'] = array();
    $this->_client['fax'] = array();
    $this->_client['location'] = array();
  }

  protected function loadCart(&$data) {
    foreach ($data['cart'] as $item) {
      $line = $this->loadItem($item);
      array_push($this->_cart, $line);
    }
  }

  protected function loadItem(&$item) {
    $discount = doubleval($item['discount']);
    $quantity = doubleval($item['quantity']);
    $price = doubleval($item['price']);
    $total_amount = $quantity * $price;
    $discount_amount = $total_amount * ($discount / 100);
    $subtotal = $total_amount - $discount_amount;
    $tax_amount = 0.0;
    $line_total_amount = $subtotal + $tax_amount;

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
      'tax' => array(),
    );

    if ($discount > 0.0) {
      $line['discount']['amount'] = round($discount_amount, 5);
      $line['discount']['reason'] = 'Discount';
    }

    return $line;
  }

}
