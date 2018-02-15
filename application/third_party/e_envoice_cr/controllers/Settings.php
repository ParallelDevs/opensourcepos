<?php

if (!defined('BASEPATH')) {
  exit('No direct script access allowed');
}

require_once APPPATH . 'controllers/Secure_Controller.php';
require_once dirname(__DIR__) . '/config/Hacienda_constants.php';

/**
 * 
 */
class Settings extends Secure_Controller {

  public function __construct() {
    parent::__construct('config');
    $this->load->add_package_path(APPPATH . 'third_party/e_envoice_cr/');
  }

  protected function get_id_types() {
    $prompt = ["" => "Select one..."];
    $id_types = Hacienda_constants::get_id_types();
    $options = $prompt + $id_types;
    return $options;
  }

  protected function get_environments() {
    $prompt = ["" => "Select one..."];
    $envs = Hacienda_constants::get_environments();
    $options = $prompt + $envs;
    return $options;
  }

  protected function get_settings() {
    $batch_save_data = [
      'e_envoice_cr_env' => $this->input->post('environment'),
      'e_envoice_cr_username' => $this->input->post('username'),
      'e_envoice_cr_password' => $this->input->post('password'),
      'e_envoice_cr_id_type' => $this->input->post('id_type'),
      'e_envoice_cr_id' => $this->input->post('company_id'),
      'e_envoice_cr_name' => $this->input->post('company_name'),
      'e_envoice_cr_cert_password' => $this->input->post('cert_password')
    ];

    return $batch_save_data;
  }

}
