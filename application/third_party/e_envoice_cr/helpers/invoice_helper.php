<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function get_invoice_dir(){
  $invoice_dir = 'e_envoice_cr';
  return $invoice_dir;
}

function is_invoice_dir_valid() {
  $ci = & get_instance();
  $ci->load->helper('directory');
  $invoice_dir = get_invoice_dir();
  
  if(!file_exists($invoice_dir)){
    return false;
  }
  
  return is_readable($invoice_dir) && is_writeable($invoice_dir)? true: false;  
}

function create_invoice_dir(){
  $ci = & get_instance();
  $ci->load->helper('directory');
  $invoice_dir = get_invoice_dir();
  
  if (!is_dir($invoice_dir)) {
    mkdir("./$invoice_dir", 0777, true);
  }

  if(!is_writeable($invoice_dir)){
    return chmod($invoice_dir, 0777);
  }
  
  return is_writeable($invoice_dir);
}
