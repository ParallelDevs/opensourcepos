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

  public function address_canton() {
    $province_code = $this->input->get('province_code');
    $options = [];
    $this->load->model('Eenvoicecrcanton');
    $result = $this->Eenvoicecrcanton->get_all($province_code);
    if ($result) {
      array_push($options, ['code' => "", 'name' => "Select one..."]);
      foreach ($result->result() as $list) {
        $option = ['code' => $list->code, 'name' => $list->name];
        array_push($options, $option);
      }
    }
    echo json_encode($options);
  }

  public function address_distrit() {
    $province_code = $this->input->get('province_code');
    $canton_code = $this->input->get('canton_code');
    $options = [];
    $this->load->model('Eenvoicecrdistrit');
    $result = $this->Eenvoicecrdistrit->get_all($province_code, $canton_code);
    if ($result) {
      array_push($options, ['code' => "", 'name' => "Select one..."]);
      foreach ($result->result() as $list) {
        $option = ['code' => $list->code, 'name' => $list->name];
        array_push($options, $option);
      }
    }
    echo json_encode($options);
  }

  public function address_neighborhood() {
    $province_code = $this->input->get('province_code');
    $canton_code = $this->input->get('canton_code');
    $distrit_code = $this->input->get('distrit_code');
    $options = [];
    $this->load->model('Eenvoicecrneighborhood');
    $result = $this->Eenvoicecrneighborhood->get_all($province_code, $canton_code, $distrit_code);
    if ($result) {
      array_push($options, ['code' => "", 'name' => "Select one..."]);
      foreach ($result->result() as $list) {
        $option = ['code' => $list->code, 'name' => $list->name];
        array_push($options, $option);
      }
    }
    echo json_encode($options);
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

  protected function get_provinces() {
    $options = ["" => "-Province-"];
    $this->load->model('Eenvoicecrprovince');
    $result = $this->Eenvoicecrprovince->get_all();
    foreach ($result->result() as $list) {
      $options[$list->code] = $list->name;
    }
    return $options;
  }

  protected function get_cantones() {
    $options = ["" => "-Canton-"];
    $province = $this->Appconfig->get('e_envoice_cr_address_province');
    if (!empty($province)) {      
      $this->load->model('Eenvoicecrcanton');
      $result = $this->Eenvoicecrcanton->get_all($province);
      if ($result) {
        foreach ($result->result() as $list) {
          $options[$list->code] = $list->name;
        }
      }
    }
    return $options;
  }

  protected function get_distrits() {
    $options = ["" => "-Distrit-"];
    $province = $this->Appconfig->get('e_envoice_cr_address_province');
    $canton = $this->Appconfig->get('e_envoice_cr_address_canton');
    if (!empty($province) && !empty($canton)) {      
      $this->load->model('Eenvoicecrdistrit');
      $result = $this->Eenvoicecrdistrit->get_all($province, $canton);
      if ($result) {
        foreach ($result->result() as $list) {
          $options[$list->code] = $list->name;
        }
      }
    }
    return $options;
  }

  protected function get_neighborhoods() {
    $options = ["" => "-Neighborhood-"];
    $province = $this->Appconfig->get('e_envoice_cr_address_province');
    $canton = $this->Appconfig->get('e_envoice_cr_address_canton');
    $distrit = $this->Appconfig->get('e_envoice_cr_address_distrit');
    if (!empty($province) && !empty($canton) && !empty($distrit)) {      
      $this->load->model('Eenvoicecrneighborhood');
      $result = $this->Eenvoicecrneighborhood->get_all($province, $canton, $distrit);
      if ($result) {
        foreach ($result->result() as $list) {
          $options[$list->code] = $list->name;
        }
      }
    }
    return $options;
  }

  protected function get_settings() {
    $batch_save_data = [
      'e_envoice_cr_env' => $this->input->post('environment'),
      'e_envoice_cr_username' => $this->input->post('username'),
      'e_envoice_cr_password' => $this->input->post('password'),
      'e_envoice_cr_id_type' => $this->input->post('id_type'),
      'e_envoice_cr_id' => $this->input->post('company_id'),
      'e_envoice_cr_commercial_name' => $this->input->post('company_name'),
      'e_envoice_cr_resolution_number' => $this->input->post('resolution_number'),
      'e_envoice_cr_resolution_date' => $this->input->post('resolution_date'),
      'e_envoice_cr_cert_password' => $this->input->post('cert_password'),
      'e_envoice_cr_address_province' => $this->input->post('province'),
      'e_envoice_cr_address_canton' => $this->input->post('canton'),
      'e_envoice_cr_address_distrit' => $this->input->post('distrit'),
      'e_envoice_cr_address_neighborhood' => $this->input->post('neighborhood'),
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
      'file_name' => 'e_envoice_cert',
      'overwrite' => true,
    );
    $this->load->library('upload', $config);
    $this->upload->do_upload('cert');

    return strlen($this->upload->display_errors()) == 0 || !strcmp($this->upload->display_errors(), '<p>' . $this->lang->line('upload_no_file_selected') . '</p>');
  }

}
