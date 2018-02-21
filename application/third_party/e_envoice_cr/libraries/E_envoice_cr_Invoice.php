<?php

require_once dirname(__DIR__) . '/config/Hacienda_constants.php';

/**
 * Description of E_envoice_cr_Invoice
 *
 * @author pdev
 */
class E_envoice_cr_Invoice {

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

  public function loadInvoice(&$data) {
    $key = $this->generateClave($data);    
    $this->_invoice['key'] = $key;
    $this->_invoice['consecutive'] = generate_invoice_consecutive($key);
    $this->_invoice['date'] = $this->generateFechaEmision($data);
  }

  protected function generateClave(&$data) {
    $invoice_number = format_invoice_number($data['invoice_number'], 10, '0');
    $secure_code = format_invoice_number($data['invoice_number'], 8, '0');
    $id = $this->_ci->Appconfig->get('e_envoice_cr_id');
    $id_user = format_invoice_number($id, 12, '0');
    $key = generate_invoice_key($invoice_number, $secure_code, $id_user);
    return $key;
  }
  
  protected function generateFechaEmision(&$data) {    
    $date = format_invoice_date($data['transaction_time']);
    return $date;
  }
  
  

}
