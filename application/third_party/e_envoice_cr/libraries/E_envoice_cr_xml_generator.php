<?php

require_once dirname(__DIR__) . '/config/Hacienda_constants.php';

/**
 * Description of E_envoice_cr_xml_generator
 *
 * @author pdev
 */
class E_envoice_cr_xml_generator {

  private $_xml;

  public function __construct() {
    
  }

  public function generateInvoiceXML($general_data, $client, $emitter, $rows, $type = 'FE') {
    $this->initXML();
    $children = array();
    $root = $this->getRootTag($type);
    $children[] = $this->getSimpleTag('Clave', $general_data['key']);
    $children[] = $this->getSimpleTag('NumeroConsecutivo', $general_data['consecutive']);
    $children[] = $this->getSimpleTag('FechaEmision', $general_data['date']);
    $children[] = $this->getEmisorTag($emitter);
    $children[] = $this->getReceptorTag($client);
    $children[] = $this->getSimpleTag('CondicionVenta', $general_data['condition']);
    $children[] = $this->getSimpleTag('PlazoCredito', $general_data['p_credit']);
    $children[] = $this->getSimpleTag('MedioPago', $general_data['pay_type']);
    $children[] = $this->getDetalleServicioTag($rows);
    $children[] = $this->getResumenFacturaTag($general_data);
    $children[] = $this->getInformacionReferenciaTag($general_data);
    $children[] = $this->getNormativaTag($general_data);
    $children[] = $this->getOtrosTag($general_data);

    foreach ($children as $node) {
      $root->appendChild($node);
    }

    $this->_xml->appendChild($root);
    $file = get_invoice_dir();
    $file .= '/' . $general_data['consecutive'] . '.xml';
    $this->_xml->save($file);
  }

  protected function initXML() {
    $this->_xml = new DOMDocument('1.0', 'UTF-8');
    $this->_xml->standalone = true;
    $this->_xml->formatOutput = true;
  }

  protected function getRootTag($type) {
    $xmlns = $this->getXmlns($type);
    $tagName = $this->getXmlTagName($type);
    $root = $this->_xml->createElementNS($xmlns, $tagName);
    $root->setAttribute('xmlns:ns2', 'http://www.w3.org/2000/09/xmldsig#');
    return $root;
  }

  protected function getEmisorTag(&$emitter) {
    $emitterTag = $this->_xml->createElement('Emisor');
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

  protected function getReceptorTag(&$client) {
    $clientTag = $this->_xml->createElement('Receptor');
    $name = $this->getSimpleTag('Nombre', $client['name']);
    $clientTag->appendChild($name);

    if (!empty($client['id'])) {
      $idTag = $this->getIdentificationTag($client['id']['type'], $client['id']['number']);
      $clientTag->appendChild($idTag);
    }

    if (!empty($client['commercialName'])) {
      $commercialName = $this->getSimpleTag('NombreComercial', $client['commercialName']);
      $clientTag->appendChild($commercialName);
    }

    if (!empty($client['location'])) {
      $location = $this->getUbicacionTag($client['location']);
      $clientTag->appendChild($location);
    }

    if (!empty($client['phone'])) {
      $phone = $this->getPhoneTag('Telefono', $client['phone']['code'], $client['phone']['number']);
      $clientTag->appendChild($phone);
    }

    if (!empty($client['fax'])) {
      $fax = $this->getPhoneTag('Fax', $client['fax']['code'], $client['fax']['number']);
      $clientTag->appendChild($fax);
    }

    if (!empty($client['email'])) {
      $email = $this->getSimpleTag('CorreoElectronico', $client['email']);
      $clientTag->appendChild($email);
    }
    return $clientTag;
  }

  protected function getIdentificationTag($idType, $id) {
    $idTag = $this->_xml->createElement('Identificacion');
    $type = $this->getSimpleTag('Tipo', $idType);
    $number = $this->getSimpleTag('Numero', $id);
    $idTag->appendChild($type);
    $idTag->appendChild($number);
    return $idTag;
  }

  protected function getUbicacionTag($location) {
    $locationTag = $this->_xml->createElement('Ubicacion');
    $prov = $this->getSimpleTag('Provincia', $location['prov']);
    $cant = $this->getSimpleTag('Canton', $location['cant']);
    $dist = $this->getSimpleTag('Distrito', $location['dist']);
    $barr = $this->getSimpleTag('Barrio', $location['barr']);
    $oth = $this->getSimpleTag('OtrasSenas', $location['other']);
    $locationTag->appendChild($prov);
    $locationTag->appendChild($cant);
    $locationTag->appendChild($dist);
    $locationTag->appendChild($barr);
    $locationTag->appendChild($oth);
    return $locationTag;
  }

  protected function getPhoneTag($tagName, $code, $number) {
    $tag = $this->_xml->createElement($tagName);
    $codeTag = $this->getSimpleTag('CodigoPais', $code);
    $numTag = $this->getSimpleTag('NumTelefono', $number);
    $tag->appendChild($codeTag);
    $tag->appendChild($numTag);
    return $tag;
  }

  protected function getDetalleServicioTag(&$items) {
    $tag = $this->_xml->createElement('DetalleServicio');
    foreach ($items as $item) {
      $lineTag = $this->getLineaDetalleTag($item);
      $tag->appendChild($lineTag);
    }
    return $tag;
  }

  protected function getLineaDetalleTag(&$item) {
    $tag = $this->_xml->createElement('LineaDetalle');
    $children = array();
    $children[] = $this->getSimpleTag('NumeroLinea', $item['line']);
    $children[] = $this->getCodigoTag($item);
    $children[] = $this->getSimpleTag('Cantidad', $item['quantity']);
    $children[] = $this->getSimpleTag('UnidadMedida', $item['unit']);
    $children[] = $this->getSimpleTag('UnidadMedidaComercial', $item['unit_name']);
    $children[] = $this->getSimpleTag('Detalle', $item['detail']);
    $children[] = $this->getSimpleTag('PrecioUnitario', $item['price']);
    $children[] = $this->getSimpleTag('MontoTotal', $item['total']);
    $children[] = $this->getSimpleTag('SubTotal', $item['subtotal']);
    $children[] = $this->getImpuestoTag($item);
    $children[] = $this->getSimpleTag('MontoTotalLinea', $item['total_amount']);

    foreach ($children as $child) {
      $tag->appendChild($child);
    }

    return $tag;
  }

  protected function getCodigoTag(&$item) {
    $codeTag = $this->_xml->createElement('Codigo');
    $cType = $this->getSimpleTag('Tipo', $item['type']);
    $cCode = $this->getSimpleTag('Codigo', $item['code']);

    $codeTag->appendChild($cType);
    $codeTag->appendChild($cCode);
    return $codeTag;
  }

  protected function getImpuestoTag(&$item) {
    $taxTag = $this->_xml->createElement('Impuesto');
    $code = $this->getSimpleTag('Codigo', $item['tax']['code']);
    $cost = $this->getSimpleTag('Tarifa', $item['tax']['cost']);
    $amount = $this->getSimpleTag('Monto', $item['tax']['amount']);

    $taxTag->appendChild($code);
    $taxTag->appendChild($cost);
    $taxTag->appendChild($amount);
    return $taxTag;
  }

  protected function getResumenFacturaTag(&$data) {
    $tag = $this->_xml->createElement('ResumenFactura');
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

  protected function getInformacionReferenciaTag(&$data) {
    $tag = $this->_xml->createElement('InformacionReferencia');
    $children = array();
    $children[] = $this->getSimpleTag('TipoDoc', $data['doc_type']);
    $children[] = $this->getSimpleTag('Numero', $data['key']);
    $children[] = $this->getSimpleTag('FechaEmision', $data['date']);
    $children[] = $this->getSimpleTag('Codigo', $data['code']);
    $children[] = $this->getSimpleTag('Razon', $data['reason']);

    foreach ($children as $child) {
      $tag->appendChild($child);
    }

    return $tag;
  }

  protected function getNormativaTag(&$data) {
    $tag = $this->_xml->createElement('Normativa');
    $children = array();
    $children[] = $this->getSimpleTag('NumeroResolucion', $data['num_res']);
    $children[] = $this->getSimpleTag('FechaResolucion', $data['date_res']);
    foreach ($children as $child) {
      $tag->appendChild($child);
    }
    return $tag;
  }

  protected function getOtrosTag(&$data) {
    $tag = $this->_xml->createElement('Otros');

    foreach ($data as $element) {
      $child = $this->getSimpleTag('OtroTexto', $element);
      $tag->appendChild($child);
    }

    return $tag;
  }

  protected function getSimpleTag($tagName, $value) {
    $tag = $this->_xml->createElement($tagName, $value);
    return $tag;
  }

  /**
   * Gets the Invoice xml tag name according to the type of invoice.
   *
   * @param string $type
   *   Type of invoice.
   *
   * @return null|string
   *   Tag name for the XML File.
   */
  private function getXmlTagName($type) {
    $tagName = Hacienda_constants::get_tagname_by_document_type($type);
    if (empty($tagName)) {
      $tagName .= 'root';
    }
    return $tagName;
  }

  /**
   * Gets the Invoice xml tag according to the type of invoice.
   *
   * @param string $type
   *   Type of invoice.
   *
   * @return string
   *   Url to the XML document.
   */
  private function getXmlns($type) {
    $xmlns = Hacienda_constants::get_xlmns_by_document_type($type);
    
    return $xmlns;
  }

}
