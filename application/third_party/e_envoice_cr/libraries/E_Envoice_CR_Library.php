<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Description of E_Envoice_cr_library
 *
 * @author pdev
 */
class E_Envoice_CR_Library {

  private $_ci;
  private $_auth_token;

  public function __construct() {
    $this->_ci = & get_instance();
    $this->_ci->load->add_package_path(APPPATH . 'third_party/e_envoice_cr/');
  }

  public function init_invoice() {
    $this->_ci->load->helper('invoice');
    if (!is_invoice_dir_valid()) {
      create_invoice_dir();
    }
  }

  public function authenticate() {
    $this->_ci->load->library('e_envoice_cr_auth');
    $this->_auth_token = $this->_ci->e_envoice_cr_auth->getLoginToken();
  }

  public function generateXml(&$sale_data, $sale_type) {
    $this->_ci->load->library('e_envoice_cr_invoice');
    $this->_ci->load->library('e_envoice_cr_xml_generator');
    $this->_ci->e_envoice_cr_invoice->mapSale($sale_data, $sale_type);
    $general_data = $this->_ci->e_envoice_cr_invoice->getInvoiceData();
    $client = $this->_ci->e_envoice_cr_invoice->getClientData();
    $emitter = $this->_ci->e_envoice_cr_invoice->getEmitterData();
    $rows = $this->_ci->e_envoice_cr_invoice->getCartData();
    $type = $this->_ci->e_envoice_cr_invoice->getDocumentType();
    $this->_ci->e_envoice_cr_xml_generator->generateInvoiceXML($general_data, $client, $emitter, $rows, $type);
  }

}
