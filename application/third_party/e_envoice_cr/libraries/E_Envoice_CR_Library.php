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
    $this->_ci->load->model('Appconfig');
  }

  public function init_document() {
    $this->_ci->load->helper('invoice');
    if (!is_documents_dir_valid()) {
      create_documents_dirs();
    }
  }

  public function authenticate() {
    $this->_ci->load->library('e_envoice_cr_auth');
    $this->_auth_token = $this->_ci->e_envoice_cr_auth->getLoginToken();
  }
  
  public function generateXml(&$sale_data, $sale_type, $client_id) {
    $this->_ci->load->library('e_envoice_cr_mapper');    
    $this->_ci->e_envoice_cr_mapper->mapSale($sale_data, $sale_type, $client_id);
    $general_data = $this->_ci->e_envoice_cr_mapper->getDocumentData();
    $client = $this->_ci->e_envoice_cr_mapper->getClientData();
    $emitter = $this->_ci->e_envoice_cr_mapper->getEmitterData();
    $rows = $this->_ci->e_envoice_cr_mapper->getCartData();
    $type = $this->_ci->e_envoice_cr_mapper->getDocumentType();
    create_document_subfolder($type);
    $document_key = $this->_ci->e_envoice_cr_mapper->getDocumentKey();
    $class = 'E_envoice_cr_'.$type.'_generator';
    require_once APPPATH.'third_party/e_envoice_cr/libraries/'.$class.'.php';
    $xml_generator = new $class ($document_key);
    $xml_generator->generateXMLDocument($general_data, $client, $emitter, $rows);
    $this->_ci->e_envoice_cr_mapper->increaseDocumentNumber();
    
    $xml_document = $xml_generator->getFile();
    $this->_ci->load->library('e_envoice_cr_document_signer');
    $this->_ci->e_envoice_cr_document_signer->signXMLDocument($xml_document);
  }
  
  
}
