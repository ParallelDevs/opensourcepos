<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');


require_once APPPATH.'third_party/e_envoice_cr/controllers/Settings.php';

/**
 * Description of E_envoice_cr
 *
 * @author pdev
 */
class E_envoice_cr extends Settings {


  public function __construct()
	{
		parent::__construct();
    $this->load->library('session');
	}
  public function index(){
    $data['environments'] = $this->get_environments();
    $data['id_types'] = $this->get_id_types();
    
    $this->load->view('settings',$data);
  }
  public function save_settings() {
    $batch_save_data = $this->get_settings();
    $result = $this->Appconfig->batch_save($batch_save_data);
    $success = $result ? 'success' : 'error';
    $message = $this->lang->line('config_saved_' . ($success ? '' : 'un') . 'successfully');
		$message = $success ? $message : $this->upload->display_errors();

    $notice = [
			'class' => $success,
			'message' => $message
		];
		$this->session->set_flashdata('notice',$notice);
    redirect('/e_envoice_cr');
  }
}
