<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Description of E_Envoice_cr_library
 *
 * @author pdev
 */
class E_Envoice_CR_Library {

  private $_ci;
  private $_xml_generator;

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

  public function sendSaleDocument(&$sale_data, $sale_type, $client_id) {
    $result = false;
    $this->generateXmlDocument($sale_data, $sale_type, $client_id);
    if ($this->signXmlDocument()) {
      $result = $this->sendXmlDocument();
    }
    return $result;
  }

  protected function generateXmlDocument(&$sale_data, &$sale_type, &$client_id) {
    $this->_ci->load->library('e_envoice_cr_mapper');
    $this->_ci->e_envoice_cr_mapper->mapSale($sale_data, $sale_type, $client_id);
    $general_data = $this->_ci->e_envoice_cr_mapper->getDocumentData();
    $client = $this->_ci->e_envoice_cr_mapper->getClientData();
    $emitter = $this->_ci->e_envoice_cr_mapper->getEmitterData();
    $rows = $this->_ci->e_envoice_cr_mapper->getCartData();
    $type = $this->_ci->e_envoice_cr_mapper->getDocumentType();
    create_document_subfolder($type);
    $document_key = $this->_ci->e_envoice_cr_mapper->getDocumentKey();
    $class = 'E_envoice_cr_' . $type . '_generator';
    require_once APPPATH . 'third_party/e_envoice_cr/libraries/' . $class . '.php';
    $this->_xml_generator = new $class($document_key);
    $this->_xml_generator->generateXMLDocument($general_data, $client, $emitter, $rows);
  }

  protected function signXmlDocument() {
    $xml_document = $this->_xml_generator->getFile();
    $xml_path = $this->_xml_generator->getPath();
    $this->_ci->load->library('e_envoice_cr_document_signer');
    $signed = $this->_ci->e_envoice_cr_document_signer->signXMLDocument($xml_path, $xml_document);
    $signed_document = $this->_ci->e_envoice_cr_document_signer->getSignedXMLDocument();
    if ($signed) {
      $this->_ci->e_envoice_cr_mapper->increaseDocumentNumber();
      return $this->_xml_generator->replaceXmlDocument($signed_document);
    }

    return false;
  }

  protected function sendXmlDocument() {
    $xml_path = $this->_xml_generator->getPath();
    $signed_document = $this->_xml_generator->getFile();
    $document_info = $this->getSaleDocumentPayload();
    $this->_ci->load->library('e_envoice_cr_communicator');
    $this->_ci->e_envoice_cr_communicator->sendDocument($document_info, $xml_path . $signed_document);
    $doc_type = $this->_ci->e_envoice_cr_mapper->getDocumentType();
    $consecutive = $this->_ci->e_envoice_cr_mapper->getDocumentConsecutive();
    $sale_document_info = array(
      'document_key' => $document_info['key'],
      'document_consecutive' => $consecutive,
      'document_code' => Hacienda_constants::get_code_by_document_type($doc_type),
      'document_status' => $this->_ci->e_envoice_cr_communicator->getStatus(),
      'document_url' => $this->_ci->e_envoice_cr_communicator->getURLDocument(),
      'sent_xml' => $xml_path . $signed_document,
    );
    return $sale_document_info;
  }

  protected function getSaleDocumentPayload() {
    $general_data = $this->_ci->e_envoice_cr_mapper->getDocumentData();
    $emitter = $this->_ci->e_envoice_cr_mapper->getEmitterData();
    $client = $this->_ci->e_envoice_cr_mapper->getClientData();

    $document_info = array(
      'key' => $general_data['key'],
      'date' => $general_data['date'],
      'emitter' => [
        'id_type' => $emitter['id']['type'],
        'id_number' => $emitter['id']['number'],
      ],
      'receiver' => array(),
    );

    if (!empty($client) && array_key_exists('id', $client)) {
      $document_info['receiver']['id_type'] = $client['id']['type'];
      $document_info['receiver']['id_number'] = $client['id']['number'];
    }
    return $document_info;
  }

}
