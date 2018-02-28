<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Description of E_envoice_document_signer
 *
 * @author pdev
 */
class E_envoice_cr_document_signer {

  private $_ci;
  private $_signed_file;

  public function __construct() {
    $this->_ci = & get_instance();
    $this->_ci->load->add_package_path(APPPATH . 'third_party/e_envoice_cr/');
    $this->_ci->load->helper('invoice');
    $this->_ci->load->model('Appconfig');
  }

  public function getSignedXMLDocument() {
    return $this->_signed_file;
  }

  public function signXMLDocument($xml_file) {
    $new_xml_file = str_replace('.xml', '-signed', $xml_file);
    $new_xml_file .= '.xml';
    $this->_signed_file = $new_xml_file;
    $cert_file = get_certificate_file();
    $cert_password = $this->_ci->Appconfig->get('e_envoice_cr_cert_password');
    $jar_file = APPPATH . 'third_party/e_envoice_cr/libraries/jar/firmar-xades.jar';
    $command = "java -jar $jar_file $cert_file $cert_password  $xml_file $new_xml_file";

    exec($command, $response);
    // Send the response.
    return $response;
  }

}
