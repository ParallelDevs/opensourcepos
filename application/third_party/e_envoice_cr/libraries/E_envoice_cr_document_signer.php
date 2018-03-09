<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

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

  public function signXMLDocument($document_path, $document_name) {
    $output = array();
    $response = 0;
    $xml_file = str_replace('.xml', '', $document_name);
    $this->_signed_file = $xml_file . 'segned.xml';
    $cert_path = get_certificate_dir();
    $cert_password = $this->_ci->Appconfig->get('e_envoice_cr_cert_password');
    $jar_file = APPPATH . 'third_party/e_envoice_cr/libraries/jar/java-xades4j-signer.jar';
    $command = "java -jar $jar_file $cert_path $cert_password $document_path $document_path $xml_file 2>&1";

    exec($command, $output, $response);
    // Send the response.
    return $response == 0 ? true : false;
  }

}
