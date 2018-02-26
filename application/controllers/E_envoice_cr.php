<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');


require_once APPPATH . 'third_party/e_envoice_cr/controllers/Settings.php';

/**
 * Description of E_envoice_cr
 *
 * @author pdev
 */
class E_envoice_cr extends Settings {

  public function __construct() {
    parent::__construct();
    $this->load->library('session');
  }

  public function index() {
    $data['environments'] = $this->get_environments();
    $data['id_types'] = $this->get_id_types();
    $data['provinces'] = $this->get_provinces();
    $data['cantones'] = $this->get_cantones();
    $data['distrits'] = $this->get_distrits();
    $data['neighborhoods']= $this->get_neighborhoods();

    $this->load->view('settings', $data);
  }

  public function save_settings() {
    $batch_save_data = $this->get_settings();
    $upload_success = $this->handle_cert_upload();
    $upload_data = $this->upload->data();
    $result = $this->Appconfig->batch_save($batch_save_data);
    $success = $upload_success && $result ? true : false;
    $message = $this->lang->line('config_saved_' . ($success ? '' : 'un') . 'successfully');
    $message = $success ? $message : $this->upload->display_errors();

    if (!empty($upload_data['orig_name'])) {
      copy($upload_data['full_path'], $upload_data['file_path'] . $upload_data['raw_name'] . '.pfx');
      $batch_save_data['e_envoice_cr_cert_path'] = $upload_data['file_path'];
    }
    $notice = [
      'class' => $success ? 'success' : 'danger',
      'message' => $message
    ];
    $this->session->set_flashdata('notice', $notice);
    redirect('/e_envoice_cr');
  }

}
