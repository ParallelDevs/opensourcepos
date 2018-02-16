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
    $this->load->language('e_envoice_cr');
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
      'e_envoice_cr_currency_code' => $this->input->post('currency_code'),
      'e_envoice_cr_cert_password' => $this->input->post('cert_password')
    ];

    if (empty($batch_save_data['e_envoice_cr_password'])) {
      unset($batch_save_data['e_envoice_cr_password']);
    }
    if (empty($batch_save_data['e_envoice_cr_cert_password'])) {
      unset($batch_save_data['e_envoice_cr_cert_password']);
    }

    return $batch_save_data;
  }

  protected function handle_cert_upload() {
    $this->load->helper('directory');

    if (!is_dir('uploads')) {
      mkdir('./uploads', 0777, true);
    }
    if (!is_dir('uploads/certs')) {
      mkdir('./uploads/certs', 0777, true);
    }

    // load upload library
    $config = array('upload_path' => './uploads/certs/',
      'allowed_types' => 'p12',
      'file_name'=>'e_envoice_cert',
    );
    $this->load->library('upload', $config);
    $this->upload->do_upload('cert');

    return strlen($this->upload->display_errors()) == 0 || !strcmp($this->upload->display_errors(), '<p>' . $this->lang->line('upload_no_file_selected') . '</p>');
  }

}
