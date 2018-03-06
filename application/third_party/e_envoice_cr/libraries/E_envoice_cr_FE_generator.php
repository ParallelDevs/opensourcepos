<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require_once __DIR__ . '/E_envoice_cr_document_generator.php';

/**
 * Description of E_envoice_cr_FE_generator
 *
 * @author pdev
 */
class E_envoice_cr_FE_generator extends E_envoice_cr_document_generator {

  public function __construct($key) {
    parent::__construct($key);
    $this->type = 'FE';
    $this->init();
    $this->initRootTag();
  }

  public function generateXMLDocument(&$general_data, &$receiver, &$emitter, &$items) {
    $children = array();
    $root = $this->getRootTag();
    array_push($children, $this->getSimpleTag('Clave', $general_data['key']));
    array_push($children, $this->getSimpleTag('NumeroConsecutivo', $general_data['consecutive']));
    array_push($children, $this->getSimpleTag('FechaEmision', $general_data['date']));
    array_push($children, $this->getEmisorTag($emitter));
    array_push($children, $this->getReceptorTag($receiver));
    array_push($children, $this->getSimpleTag('CondicionVenta', $general_data['condition']));
    array_push($children, $this->getSimpleTag('PlazoCredito', $general_data['p_credit']));
    $this->getMedioPagoTag($general_data['pay_types'], $children);
    array_push($children, $this->getDetalleServicioTag($items));
    array_push($children, $this->getResumenFacturaTag($general_data));
    array_push($children, $this->getInformacionReferenciaTag($general_data));
    array_push($children, $this->getNormativaTag($general_data));

    if (!empty($general_data['others'])) {
      array_push($children, $this->getOtrosTag($general_data));
    }
    foreach ($children as $node) {
      $root->appendChild($node);
    }

    $this->xml->appendChild($root);
    $this->xml->save($this->path . $this->file);
  }

  protected function initRootTag() {
    $this->root_tag = 'FacturaElectronica';
    $this->root_attributes += [
      'xmlns' => Hacienda_constants::XMLNS_FE,
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
    array_push($children, $this->getSimpleTag('TipoDoc', $data['document_code']));
    array_push($children, $this->getSimpleTag('Numero', $data['key']));
    array_push($children, $this->getSimpleTag('FechaEmision', $data['date']));
    array_push($children, $this->getSimpleTag('Codigo', '02'));
    array_push($children, $this->getSimpleTag('Razon', 'a'));

    foreach ($children as $child) {
      $tag->appendChild($child);
    }

    return $tag;
  }

}
