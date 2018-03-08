<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require_once dirname(__DIR__) . '/config/Hacienda_constants.php';

/**
 * Description of E_envoice_cr_document
 *
 * @author pdev
 */
abstract class E_envoice_cr_document_generator {

  protected $xml;
  protected $path;
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

  public function getPath() {
    return $this->path;
  }

  public function replaceXmlDocument($new_file) {
    $current_filename = $this->path . $this->file;
    $new_filename = $this->path . $new_file;

    if (file_exists($current_filename)) {
      @unlink($current_filename);
    }

    return rename($new_filename, $current_filename);
  }

  abstract public function generateXMLDocument(&$general_data, &$receiver, &$emitter, &$items);

  abstract protected function initRootTag();

  abstract protected function getReceptorTag(&$receiver);

  abstract protected function getInformacionReferenciaTag(&$data);

  protected function init() {
    $this->path = get_documents_dir() . '/' . $this->type . '/';
    $this->file = $this->key . '.xml';
  }

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
    array_push($children, $this->getSimpleTag('Provincia', $location['prov']));
    array_push($children, $this->getSimpleTag('Canton', $location['cant']));
    array_push($children, $this->getSimpleTag('Distrito', $location['dist']));

    if (!empty($location['barr'])) {
      array_push($children, $this->getSimpleTag('Barrio', $location['barr']));
    }

    array_push($children, $this->getSimpleTag('OtrasSenas', $location['other']));

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
    array_push($children, $this->getSimpleTag('CodigoMoneda', $data['currency_code']));
    array_push($children, $this->getSimpleTag('TipoCambio', $data['currency_rate']));
    array_push($children, $this->getSimpleTag('TotalServGravados', $data['tsg']));
    array_push($children, $this->getSimpleTag('TotalServExentos', $data['tse']));
    array_push($children, $this->getSimpleTag('TotalMercanciasGravadas', $data['tmg']));
    array_push($children, $this->getSimpleTag('TotalMercanciasExentas', $data['tme']));
    array_push($children, $this->getSimpleTag('TotalGravado', $data['tg']));
    array_push($children, $this->getSimpleTag('TotalExento', $data['te']));
    array_push($children, $this->getSimpleTag('TotalVenta', $data['tv']));
    array_push($children, $this->getSimpleTag('TotalDescuentos', $data['td']));
    array_push($children, $this->getSimpleTag('TotalVentaNeta', $data['tvn']));
    array_push($children, $this->getSimpleTag('TotalImpuesto', $data['ti']));
    array_push($children, $this->getSimpleTag('TotalComprobante', $data['tc']));

    foreach ($children as $child) {
      $tag->appendChild($child);
    }

    return $tag;
  }

  protected function getNormativaTag(&$data) {
    $tag = $this->xml->createElement('Normativa');
    $children = array();
    array_push($children, $this->getSimpleTag('NumeroResolucion', $data['resolution']['number']));
    array_push($children, $this->getSimpleTag('FechaResolucion', $data['resolution']['date']));
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
    array_push($children, $this->getSimpleTag('NumeroLinea', $item['line']));
    array_push($children, $this->getCodigoTag($item));
    array_push($children, $this->getSimpleTag('Cantidad', $item['quantity']));
    array_push($children, $this->getSimpleTag('UnidadMedida', $item['unit']));

    if (!empty($item['unit_name'])) {
      array_push($children, $this->getSimpleTag('UnidadMedidaComercial', $item['unit_name']));
    }
    array_push($children, $this->getSimpleTag('Detalle', $item['detail']));
    array_push($children, $this->getSimpleTag('PrecioUnitario', $item['price']));
    array_push($children, $this->getSimpleTag('MontoTotal', $item['total']));

    if (!empty($item['discount'])) {
      array_push($children, $this->getSimpleTag('MontoDescuento', $item['discount']['amount']));
      array_push($children, $this->getSimpleTag('NaturalezaDescuento', $item['discount']['reason']));
    }

    array_push($children, $this->getSimpleTag('SubTotal', $item['subtotal']));
    foreach ($item['taxes'] as $tax) {
      array_push($children, $this->getImpuestoTag($tax));
    }

    array_push($children, $this->getSimpleTag('MontoTotalLinea', $item['line_total_amount']));

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
      if (!empty($element)) {
        $child = $this->getOtroTextoTag($element);
        $tag->appendChild($child);
      }
    }
    return $tag;
  }

  protected function getOtroTextoTag(&$element) {
    if (is_array($element)) {
      $child = $this->xml->createElement('OtroTexto', $element['text']);
      $child->setAttribute('codigo', $element['code']);
    }
    else {
      $child = $this->getSimpleTag('OtroTexto', $element);
    }
    return $child;
  }

  protected function getMedioPagoTag(&$data, &$children) {
    foreach ($data as $pay_code) {
      $pay_type = $this->getSimpleTag('MedioPago', $pay_code);
      array_push($children, $pay_type);
    }
  }

}
