<?php

require_once dirname(__DIR__) . '/config/Hacienda_constants.php';

/**
 * Implements the authentication functionality.
 */
class E_envoice_cr_auth {

  private $_ci;
  
  public function __construct() {
    $this->_ci = & get_instance();
    $this->_ci->load->add_package_path(APPPATH . 'third_party/e_envoice_cr/');
    $this->_ci->load->model('Appconfig');
  }

  /**
   * It gets the connection token.
   */
  public function getLoginToken() {
    $username = $this->_ci->Appconfig->get('e_envoice_cr_username');
    $password = $this->_ci->Appconfig->get('e_envoice_cr_password');
    $url = $this->get_environment_url();
    $client_id = $this->get_environment_client_id();

    if ($username !== "" && $password !== "") {
      $options = $this->generate_options($username, $password, $client_id);
      $context = stream_context_create($options);
      $result = file_get_contents($url, FALSE, $context);
      if ($result === FALSE) {
        echo $result;
      }

      // Get a token object.
      $token = json_decode($result);
      // Return a json object whith token and refresh token.
      return $token->access_token;
    }
    else {
      return "";
    }
    
  }

  protected function get_environment_url() {
    $environment = $this->_ci->Appconfig->get('e_envoice_cr_env');
    if ($environment === Hacienda_constants::ENVIRONMENT_TYPE_PROD) {
      $url = Hacienda_constants::ENVIRONMENT_URL_PROD;
    }
    else {
      $url = Hacienda_constants::ENVIRONMENT_URL_STAG;
    }
    return $url;
  }

  protected function get_environment_client_id() {
    $environment = $this->_ci->Appconfig->get('e_envoice_cr_env');
    if ($environment === Hacienda_constants::ENVIRONMENT_TYPE_PROD) {
      $client_id = Hacienda_constants::ENVIRONMENT_CLIENT_PROD;
    }
    else {
      $client_id = Hacienda_constants::ENVIRONMENT_CLIENT_STAG;
    }
    return $client_id;
  }

  protected function generate_options(&$username, &$password, &$client_id) {
    $data = [
        'client_id' => $client_id,
        'client_secret' => '',
        'grant_type' => 'password',
        'username' => $username,
        'password' => $password,
        'scope' => '',
      ];
      // Use key 'http' even if you send the request to https://.
      $options = [
        'http' => [
          'header' => "Content-type: application/x-www-form-urlencoded\r\n",
          'method' => 'POST',
          'content' => http_build_query($data),
        ],
      ];
      return $options;
  }

}
