<?php

defined('BASEPATH') OR exit('No direct script access allowed');

function get_invoice_dir() {
  $invoice_dir = 'e_envoice_cr_invoices';
  return $invoice_dir;
}

function is_invoice_dir_valid() {
  $ci = & get_instance();
  $ci->load->helper('directory');
  $invoice_dir = get_invoice_dir();

  if (!file_exists($invoice_dir)) {
    return false;
  }

  return is_readable($invoice_dir) && is_writeable($invoice_dir) ? true : false;
}

function create_invoice_dir() {
  $ci = & get_instance();
  $ci->load->helper('directory');
  $invoice_dir = get_invoice_dir();

  if (!is_dir($invoice_dir)) {
    mkdir("./$invoice_dir", 0777, true);
  }

  if (!is_writeable($invoice_dir)) {
    return chmod($invoice_dir, 0777);
  }

  return is_writeable($invoice_dir);
}

function format_invoice_number($value, $final_lenght, $padding_string) {
  $input = is_string($value) ? $value : (string) $value;
  $output = str_pad($input, $final_lenght, $padding_string);
  return $output;
}

function format_invoice_date($date_time) {
  $time = strtotime($date_time);
  $date = date('c', $time);
  return $date;
}

function generate_invoice_key($invoice_number, $secure_code, $id_user) {
  $day = date("d");
  $mouth = date("m");
  $year = date("y");
  $key = '506' . $day . $mouth . $year . $id_user . '0010000101' . $invoice_number . '1' . $secure_code;
  return $key;
}

function generate_invoice_consecutive($key) {
  $consecutive_number = substr($key, -29, 20);
  return $consecutive_number;
}
