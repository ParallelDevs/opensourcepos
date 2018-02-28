<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Description of E_envoice_cr_document
 *
 * @author pdev
 */
abstract class E_envoice_cr_document_generator {

  protected $xml;
  protected $file;
  protected $type;
  protected $key;
  protected $root_tag;
  protected $root_attributes;

  public function __construct($key) {
    $this->xml = new DOMDocument('1.0', 'UTF-8');
    $this->xml->standalone = true;
    $this->xml->formatOutput = true;
    $this->key = $key;
    $this->root_attributes = [
      'xmlns:ds' => "http://www.w3.org/2000/09/xmldsig#",
    ];
  }

  public function getFile() {
    return $this->file;
  }

  abstract public function generateXMLDocument(&$general_data, &$receiver, &$emitter, &$items);

  abstract protected function initRootTag();

  abstract protected function getReceptorTag(&$receiver);

  abstract protected function getInformacionReferenciaTag(&$data);

  protected function getRootTag() {
    $root = $this->xml->createElement($this->root_tag);
    foreach ($this->root_attributes as $attribute_name => $attribute_value) {
      $root->setAttribute($attribute_name, $attribute_value);
    }
    return $root;
  }

  protected function getSimpleTag($tagName, $value) {
    $tag = $this->xml->createElement($tagName, $value);
    return $tag;
  }

  protected function getEmisorTag(&$emitter) {
    $emitterTag = $this->xml->createElement('Emisor');
    $name = $this->getSimpleTag('Nombre', $emitter['name']);
    $emitterTag->appendChild($name);
    $idTag = $this->getIdentificationTag($emitter['id']['type'], $emitter['id']['number']);
    $emitterTag->appendChild($idTag);

    if (!empty($emitter['commercialName'])) {
      $commercialName = $this->getSimpleTag('NombreComercial', $emitter['commercialName']);
      $emitterTag->appendChild($commercialName);
    }

    $location = $this->getUbicacionTag($emitter['location']);
    $emitterTag->appendChild($location);

    if (!empty($emitter['phone'])) {
      $phone = $this->getPhoneTag('Telefono', $emitter['phone']['code'], $emitter['phone']['number']);
      $emitterTag->appendChild($phone);
    }

    if (!empty($emitter['fax'])) {
      $fax = $this->getPhoneTag('Fax', $emitter['fax']['code'], $emitter['fax']['number']);
      $emitterTag->appendChild($fax);
    }

    $email = $this->getSimpleTag('CorreoElectronico', $emitter['email']);
    $emitterTag->appendChild($email);

    return $emitterTag;
  }

  protected function getIdentificationTag($idType, $id) {
    $idTag = $this->xml->createElement('Identificacion');
    $type = $this->getSimpleTag('Tipo', $idType);
    $number = $this->getSimpleTag('Numero', $id);
    $idTag->appendChild($type);
    $idTag->appendChild($number);
    return $idTag;
  }

  protected function getUbicacionTag($location) {
    $locationTag = $this->xml->createElement('Ubicacion');
    $children = array();
    $children[] = $this->getSimpleTag('Provincia', $location['prov']);
    $children[] = $this->getSimpleTag('Canton', $location['cant']);
    $children[] = $this->getSimpleTag('Distrito', $location['dist']);

    if (!empty($location['barr'])) {
      $children[] = $this->getSimpleTag('Barrio', $location['barr']);
    }

    $children[] = $this->getSimpleTag('OtrasSenas', $location['other']);

    foreach ($children as $child) {
      $locationTag->appendChild($child);
    }

    return $locationTag;
  }

  protected function getPhoneTag($tagName, $code, $number) {
    $tag = $this->xml->createElement($tagName);
    $codeTag = $this->getSimpleTag('CodigoPais', $code);
    $numTag = $this->getSimpleTag('NumTelefono', $number);
    $tag->appendChild($codeTag);
    $tag->appendChild($numTag);
    return $tag;
  }

  protected function getResumenFacturaTag(&$data) {
    $tag = $this->xml->createElement('ResumenFactura');
    $children = array();
    $children[] = $this->getSimpleTag('CodigoMoneda', $data['currency_code']);
    $children[] = $this->getSimpleTag('TipoCambio', $data['currency_rate']);
    $children[] = $this->getSimpleTag('TotalServGravados', $data['tsg']);
    $children[] = $this->getSimpleTag('TotalServExentos', $data['tse']);
    $children[] = $this->getSimpleTag('TotalMercanciasGravadas', $data['tmg']);
    $children[] = $this->getSimpleTag('TotalMercanciasExentas', $data['tme']);
    $children[] = $this->getSimpleTag('TotalGravado', $data['tg']);
    $children[] = $this->getSimpleTag('TotalExento', $data['te']);
    $children[] = $this->getSimpleTag('TotalVenta', $data['tv']);
    $children[] = $this->getSimpleTag('TotalDescuentos', $data['td']);
    $children[] = $this->getSimpleTag('TotalVentaNeta', $data['tvn']);
    $children[] = $this->getSimpleTag('TotalImpuesto', $data['ti']);
    $children[] = $this->getSimpleTag('TotalComprobante', $data['tc']);

    foreach ($children as $child) {
      $tag->appendChild($child);
    }

    return $tag;
  }

  protected function getNormativaTag(&$data) {
    $tag = $this->xml->createElement('Normativa');
    $children = array();
    $children[] = $this->getSimpleTag('NumeroResolucion', $data['resolution']['number']);
    $children[] = $this->getSimpleTag('FechaResolucion', $data['resolution']['date']);
    foreach ($children as $child) {
      $tag->appendChild($child);
    }
    return $tag;
  }

  protected function getDetalleServicioTag(&$items) {
    $tag = $this->xml->createElement('DetalleServicio');
    foreach ($items as $item) {
      $lineTag = $this->getLineaDetalleTag($item);
      $tag->appendChild($lineTag);
    }
    return $tag;
  }

  protected function getLineaDetalleTag(&$item) {
    $tag = $this->xml->createElement('LineaDetalle');
    $children = array();
    $children[] = $this->getSimpleTag('NumeroLinea', $item['line']);
    $children[] = $this->getCodigoTag($item);
    $children[] = $this->getSimpleTag('Cantidad', $item['quantity']);
    $children[] = $this->getSimpleTag('UnidadMedida', $item['unit']);

    if (!empty($item['unit_name'])) {
      $children[] = $this->getSimpleTag('UnidadMedidaComercial', $item['unit_name']);
    }
    $children[] = $this->getSimpleTag('Detalle', $item['detail']);
    $children[] = $this->getSimpleTag('PrecioUnitario', $item['price']);
    $children[] = $this->getSimpleTag('MontoTotal', $item['total']);

    if (!empty($item['discount'])) {
      $children[] = $this->getSimpleTag('MontoDescuento', $item['discount']['amount']);
      $children[] = $this->getSimpleTag('NaturalezaDescuento', $item['discount']['reason']);
    }

    $children[] = $this->getSimpleTag('SubTotal', $item['subtotal']);
    foreach ($item['taxes'] as $tax) {
      $children[] = $this->getImpuestoTag($tax);
    }

    $children[] = $this->getSimpleTag('MontoTotalLinea', $item['line_total_amount']);

    foreach ($children as $child) {
      $tag->appendChild($child);
    }

    return $tag;
  }

  protected function getCodigoTag(&$item) {
    $codeTag = $this->xml->createElement('Codigo');
    $cType = $this->getSimpleTag('Tipo', $item['code']['type']);
    $cCode = $this->getSimpleTag('Codigo', $item['code']['number']);

    $codeTag->appendChild($cType);
    $codeTag->appendChild($cCode);
    return $codeTag;
  }

  protected function getImpuestoTag(&$tax) {
    $taxTag = $this->xml->createElement('Impuesto');
    $code = $this->getSimpleTag('Codigo', $tax['code']);
    $cost = $this->getSimpleTag('Tarifa', $tax['rate']);
    $amount = $this->getSimpleTag('Monto', $tax['amount']);

    $taxTag->appendChild($code);
    $taxTag->appendChild($cost);
    $taxTag->appendChild($amount);
    return $taxTag;
  }

  protected function getOtrosTag(&$data) {
    $tag = $this->xml->createElement('Otros');

    foreach ($data['others'] as $element) {
      $child = $this->getSimpleTag('OtroTexto', $element);
      $tag->appendChild($child);
    }

    return $tag;
  }

  protected function getMedioPagoTag(&$data, &$children) {
    foreach ($data as $pay_code) {
      $pay_type = $this->getSimpleTag('MedioPago', $pay_code);
      array_push($children, $pay_type);
    }
  }

}
