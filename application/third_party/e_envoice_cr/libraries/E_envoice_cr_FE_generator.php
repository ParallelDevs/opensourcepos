<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'third_party/e_envoice_cr/libraries/E_envoice_cr_document_generator.php';

/**
 * Description of E_envoice_cr_FE_generator
 *
 * @author pdev
 */
class E_envoice_cr_FE_generator extends E_envoice_cr_document_generator {

  public function __construct($key) {
    parent::__construct($key);
    $this->type = 'FE';
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
    $children[] = $this->getReceptorTag($receiver);
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
    $this->root_tag = 'FacturaElectronica';
    $this->root_attributes += [
      'xmlns' => "https://tribunet.hacienda.go.cr/docs/esquemas/2017/v4.2/facturaElectronica",
      'targetNamespace' => "https://tribunet.hacienda.go.cr/docs/esquemas/2017/v4.2/facturaElectronica",
    ];
  }

  protected function getReceptorTag(&$receiver) {
    $clientTag = $this->xml->createElement('Receptor');
    $name = $this->getSimpleTag('Nombre', $receiver['name']);
    $clientTag->appendChild($name);

    if (!empty($receiver['id'])) {
      $idTag = $this->getIdentificationTag($receiver['id']['type'], $receiver['id']['number']);
      $clientTag->appendChild($idTag);
    }

    if (!empty($receiver['commercialName'])) {
      $commercialName = $this->getSimpleTag('NombreComercial', $receiver['commercialName']);
      $clientTag->appendChild($commercialName);
    }

    if (!empty($receiver['location'])) {
      $location = $this->getUbicacionTag($receiver['location']);
      $clientTag->appendChild($location);
    }

    if (!empty($receiver['phone'])) {
      $phone = $this->getPhoneTag('Telefono', $receiver['phone']['code'], $receiver['phone']['number']);
      $clientTag->appendChild($phone);
    }

    if (!empty($receiver['fax'])) {
      $fax = $this->getPhoneTag('Fax', $receiver['fax']['code'], $receiver['fax']['number']);
      $clientTag->appendChild($fax);
    }

    if (!empty($receiver['email'])) {
      $email = $this->getSimpleTag('CorreoElectronico', $receiver['email']);
      $clientTag->appendChild($email);
    }
    return $clientTag;
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
