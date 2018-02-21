<?php

require_once dirname(__DIR__) . '/config/Hacienda_constants.php';

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

  public function loadInvoice(&$data, $doc_type) {
    $this->_invoice['consecutive'] = $this->generateConsecutivo($data, $doc_type);
    $this->_invoice['key'] = $this->generateClave($data, $this->_invoice['consecutive']);
    $this->_invoice['date'] = $this->generateFechaEmision($data);
    $this->_invoice['condition'] = $this->getCondicionVenta($data);
    $this->_invoice['pay_types'] = $this->getMedioPago($data);
  }

  public function getInvoiceData() {
    return $this->_invoice;
  }

  protected function generateClave(&$data, $consecutive) {
    $secure_code = format_invoice_number($data['invoice_number'], 8);
    $id = $this->_ci->Appconfig->get('e_envoice_cr_id');
    $id_user = format_invoice_number($id, 12);
    $key = generate_invoice_key($consecutive, $secure_code, $id_user);
    return $key;
  }

  protected function generateConsecutivo(&$data, $doc_type) {
    $consecutive = generate_invoice_consecutive(1, 1, $doc_type, $data['invoice_number']);
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
    $payments=array();
    foreach($data['payments'] as $pay_type){
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
}
