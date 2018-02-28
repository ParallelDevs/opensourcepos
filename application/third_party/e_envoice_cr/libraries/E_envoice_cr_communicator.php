<?php

require_once dirname(__DIR__) . '/config/Hacienda_constants.php';
require_once dirname(__DIR__) . '/vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

/**
 * Description of E_envoice_cr_communicator
 *
 * @author pdev
 */
class E_envoice_cr_communicator {

  private $_ci;
  private $_success;

  public function __construct() {
    $this->_ci = & get_instance();
    $this->_ci->load->add_package_path(APPPATH . 'third_party/e_envoice_cr/');
    $this->_ci->load->model('Appconfig');
    $this->_success = false;
  }

  public function sendDocument(&$document_info, $xml_file, $auth_token) {
    $url = $this->getUrl();
    $url .= 'recepcion';
    $client = new Client([
      'http_errors' => false,
      'headers' => [
        'Authorization' => 'Bearer ' . $auth_token,
        'Accept' => 'application/json',
      ],
    ]);
    $data = $this->getDocumentPayload($document_info, $xml_file);


    $response = $client->post($url, [
      RequestOptions::JSON => $data,
    ]);

    $code = $response->getStatusCode();
    switch ($code) {
      case 201:
      case 202:
        $this->_success = true;
        break;
      case 400:
      case 403:
        $this->_success = false;
        break;
    }
  }

  protected function getDocumentPayload(&$document_info, $xml_file) {
    $document_content = file_get_contents($xml_file);

    $data = [
      'clave' => $document_info['key'],
      'fecha' => $document_info['date'],
      'emisor' => [
        'tipoIdentificacion' => $document_info['emitter']['id_type'],
        'numeroIdentificacion' => $document_info['emitter']['id_number'],
      ],
    ];

    if (!empty($document_info['receiver'])) {
      $data['receptor'] = [
        'tipoIdentificacion' => $document_info['receiver']['id_type'],
        'numeroIdentificacion' => $document_info['receiver']['id_number'],
      ];
    }

    $data['comprobanteXml'] = base64_encode($document_content);

    return $data;
  }

  protected function getUrl() {
    $environment = $this->_ci->Appconfig->get('e_envoice_cr_env');
    if ($environment === Hacienda_constants::ENVIRONMENT_TYPE_PROD) {
      $url = Hacienda_constants::API_URL_PROD;
    }
    else {
      $url = Hacienda_constants::API_URL_STAG;
    }
    return $url;
  }

}
