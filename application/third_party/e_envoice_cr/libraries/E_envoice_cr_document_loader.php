<?php

if (!defined('BASEPATH')) {
  exit('No direct script access allowed');
}

require_once dirname(__DIR__) . '/config/Hacienda_constants.php';

/**
 * Description of E_envoice_cr_document_loader
 *
 * @author pdev
 */
class E_envoice_cr_document_loader {

  private $_ci;

  public function __construct() {
    $this->_ci = & get_instance();
    $this->_ci->load->add_package_path(APPPATH . 'third_party/e_envoice_cr/');
    $this->_ci->load->library('sale_lib');
    $this->_ci->load->model('Eenvoicecrsaledocuments');
  }

  public function getPrintData($sale_id = false) {
    $data = false;
    if ($sale_id) {
      $sale_type = $this->_ci->sale_lib->get_sale_type($sale_id);
      $document_type = $this->mapSaleType($sale_type);
      $document_code = Hacienda_constants::get_code_by_document_type($document_type);
      $document = $this->_ci->Eenvoicecrsaledocuments->get_document_by_sale_and_code($sale_id, $document_code);
      if (-1 !== $document->document_id) {
        $data = array(
          'document_key' => $document->document_key,
          'document_consecutive' => $document->document_consecutive,
          'lang_document_name' => 'e_envoice_cr_document_'.$document_type,
          'sent_xml_file'=>$document->sent_xml,
          'received_xml_file'=>$document->received_xml,
        );
      }
    }
    return $data;
  }

  protected function mapSaleType($sale_type) {
    switch ($sale_type) {
      case SALE_TYPE_INVOICE:
        $doc_type = Hacienda_constants::DOCUMENT_TYPE_FE;
        break;
      case SALE_TYPE_POS:
        $doc_type = Hacienda_constants::DOCUMENT_TYPE_TE;
        break;
      default :
        $doc_type = '';
        break;
    }
    return $doc_type;
  }

}
