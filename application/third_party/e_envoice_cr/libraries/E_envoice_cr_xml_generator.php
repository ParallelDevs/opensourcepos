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
    $root = $this->getRootTag($type);
    $root->appendChild($this->getClaveTag($general_data));
    $root->appendChild($this->getNumeroConsecutivoTag($general_data));
    $root->appendChild($this->getFechaEmisionTag($general_data));
    $this->_xml->appendChild($root);
    $file = get_invoice_dir();
    $file .= '/'.$general_data['consecutive'].'.xml';
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
  
  protected function getClaveTag(&$generalData) {
    $tag = $this->_xml->createElement('Clave',$generalData['key']);
    return $tag;    
  }
  
  protected function getNumeroConsecutivoTag(&$generalData){
    $tag = $this->_xml->createElement('NumeroConsecutivo',$generalData['consecutive']);
    return $tag;
  }
  
  protected function getFechaEmisionTag($generalData) {
    $tag = $this->_xml->createElement('FechaEmision',$generalData['date']);
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
