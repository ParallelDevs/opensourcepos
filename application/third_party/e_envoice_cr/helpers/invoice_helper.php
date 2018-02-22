<?php

defined('BASEPATH') OR exit('No direct script access allowed');

function get_invoice_dir() {
  $invoice_dir = APPPATH .'/third_party/e_envoice_cr/xml';
  return $invoice_dir;
}

function is_invoice_dir_valid() {
  $invoice_dir = get_invoice_dir();

  if (!file_exists($invoice_dir)) {
    return false;
  }

  return is_readable($invoice_dir) && is_writeable($invoice_dir) ? true : false;
}

function create_invoice_dir() {
  $invoice_dir = get_invoice_dir();

  if (!is_dir($invoice_dir)) {
    mkdir("$invoice_dir", 0777, true);
  }

  if (!is_writeable($invoice_dir)) {
    return chmod($invoice_dir, 0777);
  }

  return is_writeable($invoice_dir);
}

function format_invoice_number($value, $final_lenght, $padding_string = '0') {
  $input = is_string($value) ? $value : (string) $value;
  $output = str_pad($input, $final_lenght, $padding_string, STR_PAD_LEFT);
  return $output;
}

function format_invoice_date($date_time) {
  $time = strtotime($date_time);
  $date = date('c', $time);
  return $date;
}

function generate_invoice_key($consecutive_number, $secure_code, $id_user) {
  $day = date("d");
  $mouth = date("m");
  $year = date("y");
  $key = '506' . $day . $mouth . $year . $id_user .$consecutive_number . '1' . $secure_code;
  return $key;
}

function generate_invoice_consecutive($sucursal, $terminal, $doc_type, $invoice_number) {
  $consecutive_number = format_invoice_number($sucursal, 3);
  $consecutive_number .= format_invoice_number($terminal, 5);
  $consecutive_number .= $doc_type;
  $consecutive_number .= format_invoice_number($invoice_number, 10);
  return $consecutive_number;
}
