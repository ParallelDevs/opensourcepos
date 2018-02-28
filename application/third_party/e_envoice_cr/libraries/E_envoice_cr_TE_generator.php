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
    $this->file = get_documents_dir() . '/' . $this->type . '/' . $this->key . '.xml';
    $this->initRootTag();
  }

  public function generateXMLDocument(&$general_data, &$receiver, &$emitter, &$items) {
    $children = array();
    $root = $this->getRootTag();
    $children[] = $this->getSimpleTag('Clave', $general_data['key']);
    $children[] = $this->getSimpleTag('NumeroConsecutivo', $general_data['consecutive']);
    $children[] = $this->getSimpleTag('FechaEmision', $general_data['date']);
    $children[] = $this->getEmisorTag($emitter);
    $children[] = $this->getSimpleTag('CondicionVenta', $general_data['condition']);
    $children[] = $this->getSimpleTag('PlazoCredito', $general_data['p_credit']);
    $this->getMedioPagoTag($general_data['pay_types'], $children);
    $children[] = $this->getDetalleServicioTag($items);
    $children[] = $this->getResumenFacturaTag($general_data);
    $children[] = $this->getInformacionReferenciaTag($general_data);
    $children[] = $this->getNormativaTag($general_data);

    if (!empty($general_data['others'])) {
      $children[] = $this->getOtrosTag($general_data);
    }
    foreach ($children as $node) {
      $root->appendChild($node);
    }

    $this->xml->appendChild($root);
    $this->xml->save($this->file);
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
    $tag = $this->xml->createElement('InformacionReferencia');
    $children = array();
    $children[] = $this->getSimpleTag('TipoDoc', $data['document_code']);
    $children[] = $this->getSimpleTag('Numero', $data['key']);
    $children[] = $this->getSimpleTag('FechaEmision', $data['date']);
    $children[] = $this->getSimpleTag('Codigo', '02');
    $children[] = $this->getSimpleTag('Razon', 'a');

    foreach ($children as $child) {
      $tag->appendChild($child);
    }

    return $tag;
  }

}
