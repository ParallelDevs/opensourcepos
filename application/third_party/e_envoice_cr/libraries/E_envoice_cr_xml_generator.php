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
    $nodes = [];    
    $root = $this->getRootTag($type);
    $nodes[] = $this->getSimpleTag('Clave', $general_data['key']);    
    $nodes[] = $this->getSimpleTag('NumeroConsecutivo', $general_data['consecutive']);
    $nodes[] = $this->getSimpleTag('FechaEmision', $general_data['date']);
    $nodes[] = $this->getEmisorTag($emitter);
    $nodes[] = $this->getReceptorTag($client);
    $nodes[] = $this->getSimpleTag('CondicionVenta', $general_data['condition']);
    $nodes[] = $this->getSimpleTag('PlazoCredito', $general_data['p_credit']);
    $nodes[] = $this->getSimpleTag('MedioPago', $general_data['pay_type']);
    
    foreach ($nodes as $node) {
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
    $tagName = '';
    switch ($type) {
      case 'FE':
        $tagName .= 'FacturaElectronica';
        break;
      case 'TE':
        $tagName .= 'TiqueteElectronico';
        break;
      case 'NC':
        $tagName .= 'NotaCreditoElectronica';
        break;
      case 'ND':
        $tagName .= 'NotaDebitoElectronica';
        break;
      default:
        $tagName .= 'root';
        break;
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
    $xmlns = '';
    switch ($type) {
      case 'FE':
        $xmlns .= 'https://tribunet.hacienda.go.cr/docs/esquemas/2017/v4.2/facturaElectronica';
        break;
      case 'TE':
        $xmlns .= 'https://tribunet.hacienda.go.cr/docs/esquemas/2017/v4.2/tiqueteElectronico';
        break;
      case 'NC':
        $xmlns .= 'https://tribunet.hacienda.go.cr/docs/esquemas/2017/v4.2/notaCreditoElectronica';
        break;
      case 'ND':
        $xmlns .= 'https://tribunet.hacienda.go.cr/docs/esquemas/2017/v4.2/notaDebitoElectronica';
        break;
      default:
        $xmlns .= '';
        break;
    }
    return $xmlns;
  }

}
