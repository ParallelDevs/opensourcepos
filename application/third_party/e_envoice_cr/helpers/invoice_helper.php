<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');
function get_documents_dir()
{
	$invoice_dir = APPPATH . '/third_party/e_envoice_cr/xml';
	return $invoice_dir;
}

function is_documents_dir_valid()
{
	$documents_dir = get_documents_dir();

	if (!file_exists($documents_dir))
	{
		return false;
	}

	return is_readable($documents_dir) && is_writeable($documents_dir) ? true : false;
}

function create_documents_dirs()
{
	$documents_dir = get_documents_dir();

	if (!is_dir($documents_dir))
	{
		mkdir("$documents_dir", 0777, true);
	}

	if (!is_writeable($documents_dir))
	{
		return chmod($documents_dir, 0777);
	}

	return is_writeable($documents_dir);
}

function create_document_subfolder($document_type)
{
	$documents_dir = get_documents_dir();
	$subfolder = $documents_dir . "/$document_type";

	if (!is_dir($subfolder))
	{
		mkdir("$subfolder", 0777, true);
	}

	if (!is_writeable($subfolder))
	{
		return chmod($subfolder, 0777);
	}

	return is_writeable($subfolder);
}

function format_document_number($value, $final_lenght, $padding_string = '0')
{
	$input = is_string($value) ? $value : (string) $value;
	$output = str_pad($input, $final_lenght, $padding_string, STR_PAD_LEFT);
	return $output;
}

function format_document_date($timestamp)
{
	$date = date('c', $timestamp);
	return $date;
}

function generate_document_key($consecutive_number, $secure_code, $id_user)
{
	$day = date("d");
	$mouth = date("m");
	$year = date("y");
	$key = '506' . $day . $mouth . $year . $id_user . $consecutive_number . '1' . $secure_code;
	return $key;
}

function generate_document_consecutive($sucursal, $terminal, $doc_type, $invoice_number)
{
	$consecutive_number = format_document_number($sucursal, 3);
	$consecutive_number .= format_document_number($terminal, 5);
	$consecutive_number .= $doc_type;
	$consecutive_number .= format_document_number($invoice_number, 10);
	return $consecutive_number;
}

function get_certificate_dir()
{
	$cert_dir = FCPATH . 'uploads/certs/';
	return $cert_dir;
}

function is_certificate_valid()
{
	$cert_dir = get_certificate_dir();

	return is_readable($cert_dir);
}
