<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require_once __DIR__ . '/E_envoice_cr_document_generator.php';

/**
 * Description of E_envoice_cr_TE_generator
 *
 * @author pdev
 */
class E_envoice_cr_TE_generator extends E_envoice_cr_document_generator {

  public function __construct($consecutive) {
    parent::__construct($consecutive);
    $this->type = 'TE';
    $this->file = get_documents_dir() . '/' . $this->type . '-' . $this->key . '.xml';
    $this->initRootTag();
  }

  public function generateXMLDocument(&$general_data, &$receiver, &$emitter, &$items) {
    
  }

  protected function initRootTag() {
    $this->root_tag = 'TiqueteElectronico';
    $this->root_attributes = [
      'xmlns' => Hacienda_constants::XMLNS_TE,
      'targetNamespace' => Hacienda_constants::TARGET_NAMESPACE_TE,
    ];
  }

  protected function getReceptorTag(&$receiver) {
    
  }

  protected function getInformacionReferenciaTag(&$data) {
    
  }

}
