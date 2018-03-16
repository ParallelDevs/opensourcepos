<?php

if (!defined('BASEPATH'))
{
	exit('No direct script access allowed');
}

/**
 * Description of E_Envoice_cr_library
 *
 * @author pdev
 */
class E_Envoice_CR_Library {

	private $_ci;
	private $_xml_generator;

	public function __construct()
	{
		$this->_ci = & get_instance();
		$this->_ci->load->add_package_path(APPPATH . 'third_party/e_envoice_cr/');
		$this->_ci->load->model('Appconfig');
		$lang_code = $this->_ci->Appconfig->get('language_code');
		$this->_ci->load->language('e_envoice_cr', $lang_code);
	}

	public function init_document()
	{
		$this->_ci->load->helper('invoice');
		if (!is_documents_dir_valid())
		{
			create_documents_dirs();
		}
	}

	public function sendSaleDocument(&$sale_data, $sale_type, $client_id)
	{
		$result = false;
		$this->generateXmlDocument($sale_data, $sale_type, $client_id);
		if ($this->signXmlDocument())
		{
			$result = $this->sendXmlDocument();
		}
		if (false !== $result)
		{
			$this->_ci->e_envoice_cr_mapper->increaseDocumentNumber();
		}
		return $result;
	}

	public function get_print_details($sale_id = false, $sale_data = false)
	{
		$this->_ci->load->library('e_envoice_cr_document_loader');

		$print_info = $this->_ci->e_envoice_cr_document_loader->get_print_data($sale_id);
		if (false !== $print_info)
		{
			$this->getEmitterPrintInformation($print_info);
			$print_info['document_version'] = $this->_ci->Appconfig->get('e_envoice_cr_document_version');
			$print_info['document_legend'] = $this->_ci->Appconfig->get('e_envoice_cr_document_legend');
			$print_info['lang_document_sale_type'] = (true == $sale_data['payments_cover_total']) ?
					'e_envoice_cr_document_cash_sale' : 'e_envoice_cr_document_other_sale';
		}

		return $print_info;
	}

	public function get_email_details($sale_id = false, $sale_data = false)
	{
		$this->_ci->load->library('e_envoice_cr_document_loader');
		$this->_ci->load->model('Appconfig');
		$details = FALSE;
		$email_data = $this->_ci->e_envoice_cr_document_loader->get_email_data($sale_id);
		if (false !== $email_data)
		{
			$details = array();
			$search = array('{company_name}', '{e_envoice_key}', '{e_envoice_consecutive}', '{date}', '{time}');
			$replace = array();
			$this->fill_email_placeholders($sale_data, $email_data, $replace);

			$details['subject'] = str_replace($search, $replace, $email_data['subject']);
			$details['body'] = str_replace($search, $replace, $email_data['body']);
			$details['xml'] = sys_get_temp_dir() . '/' . str_replace($search, $replace, $email_data['document_name_xml']);
			$details['pdf'] = sys_get_temp_dir() . '/' . str_replace($search, $replace, $email_data['document_name_pdf']);

			$xml = file_get_contents($email_data['sent_xml_file']);
			$xml_file = $details['xml'];
			$written = file_put_contents($xml_file, $xml);
			if (FALSE === $written)
			{
				$details = FALSE;
			}
		}
		return $details;
	}

	protected function generateXmlDocument(&$sale_data, &$sale_type, &$client_id)
	{
		$this->_ci->load->library('e_envoice_cr_mapper');
		$this->_ci->e_envoice_cr_mapper->mapSale($sale_data, $sale_type, $client_id);
		$general_data = $this->_ci->e_envoice_cr_mapper->getDocumentData();
		$client = $this->_ci->e_envoice_cr_mapper->getClientData();
		$emitter = $this->_ci->e_envoice_cr_mapper->getEmitterData();
		$rows = $this->_ci->e_envoice_cr_mapper->getCartData();
		$type = $this->_ci->e_envoice_cr_mapper->getDocumentType();
		create_document_subfolder($type);
		$document_key = $this->_ci->e_envoice_cr_mapper->getDocumentKey();
		$class = 'E_envoice_cr_' . $type . '_generator';
		require_once APPPATH . 'third_party/e_envoice_cr/libraries/' . $class . '.php';
		$this->_xml_generator = new $class($document_key);
		$this->_xml_generator->generateXMLDocument($general_data, $client, $emitter, $rows);
	}

	protected function signXmlDocument()
	{
		$xml_document = $this->_xml_generator->getFile();
		$xml_path = $this->_xml_generator->getPath();
		$this->_ci->load->library('e_envoice_cr_document_signer');
		$signed = $this->_ci->e_envoice_cr_document_signer->signXMLDocument($xml_path, $xml_document);
		$signed_document = $this->_ci->e_envoice_cr_document_signer->getSignedXMLDocument();
		if ($signed)
		{
			return $this->_xml_generator->replaceXmlDocument($signed_document);
		}

		return false;
	}

	protected function sendXmlDocument()
	{
		$xml_path = $this->_xml_generator->getPath();
		$signed_document = $this->_xml_generator->getFile();
		$document_info = $this->getSaleDocumentPayload();
		$this->_ci->load->library('e_envoice_cr_communicator');
		$this->_ci->e_envoice_cr_communicator->sendDocument($document_info, $xml_path . $signed_document);
		$doc_type = $this->_ci->e_envoice_cr_mapper->getDocumentType();
		$consecutive = $this->_ci->e_envoice_cr_mapper->getDocumentConsecutive();
		$sale_document_info = array(
			'document_key' => $document_info['key'],
			'document_consecutive' => $consecutive,
			'document_code' => Hacienda_constants::get_code_by_document_type($doc_type),
			'document_status' => $this->_ci->e_envoice_cr_communicator->getStatus(),
			'document_url' => $this->_ci->e_envoice_cr_communicator->getURLDocument(),
			'sent_xml' => $xml_path . $signed_document,
		);
		return $sale_document_info;
	}

	protected function getSaleDocumentPayload()
	{
		$general_data = $this->_ci->e_envoice_cr_mapper->getDocumentData();
		$emitter = $this->_ci->e_envoice_cr_mapper->getEmitterData();
		$client = $this->_ci->e_envoice_cr_mapper->getClientData();

		$document_info = array(
			'key' => $general_data['key'],
			'date' => $general_data['date'],
			'emitter' => [
				'id_type' => $emitter['id']['type'],
				'id_number' => $emitter['id']['number'],
			],
			'receiver' => array(),
		);

		if (!empty($client) && array_key_exists('id', $client))
		{
			$document_info['receiver']['id_type'] = $client['id']['type'];
			$document_info['receiver']['id_number'] = $client['id']['number'];
		}
		return $document_info;
	}

	protected function getEmitterPrintInformation(&$print_info)
	{
		$this->_ci->load->model(array(
			'eenvoicecrprovince',
			'eenvoicecrcanton',
			'eenvoicecrdistrit',
			'eenvoicecrneighborhood',
		));
		$province = $this->_ci->Appconfig->get('e_envoice_cr_address_province');
		$canton = $this->_ci->Appconfig->get('e_envoice_cr_address_canton');
		$distrit = $this->_ci->Appconfig->get('e_envoice_cr_address_distrit');
		$neighborhood = $this->_ci->Appconfig->get('e_envoice_cr_address_neighborhood');
		$print_info['emitter_id'] = $this->_ci->Appconfig->get('e_envoice_cr_id');
		$print_info['emitter_province'] = $this->_ci->eenvoicecrprovince->get($province);
		$print_info['emitter_canton'] = $this->_ci->eenvoicecrcanton->get($province, $canton);
		$print_info['emitter_distrit'] = $this->_ci->eenvoicecrdistrit->get($province, $canton, $distrit);
		$print_info['emitter_neighborhood'] = $this->_ci->eenvoicecrneighborhood->get($province, $canton, $distrit, $neighborhood);
		$print_info['emitter_other'] = $this->_ci->Appconfig->get('e_envoice_cr_address_other');
		$print_info['emitter_company_name'] = $this->_ci->Appconfig->get('e_envoice_cr_commercial_name');
	}

	protected function fill_email_placeholders(&$sale_data, &$email_data, &$replace)
	{
		$company_name = $this->_ci->Appconfig->get('company');
		$date_format = $this->_ci->Appconfig->get('dateformat');
		$time_format = $this->_ci->Appconfig->get('timeformat');
		$dtime = DateTime::createFromFormat($date_format . ' ' . $time_format, $sale_data['transaction_time']);
		$sale_time = $dtime->getTimestamp();
		$date = date($date_format, $sale_time);
		$time = date($time_format, $sale_time);

		array_push($replace, $company_name);
		array_push($replace, $email_data['document_key']);
		array_push($replace, $email_data['document_consecutive']);
		array_push($replace, $date);
		array_push($replace, $time);
	}

}
