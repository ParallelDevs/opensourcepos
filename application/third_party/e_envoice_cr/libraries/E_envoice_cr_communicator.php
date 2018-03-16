<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

require_once dirname(__DIR__) . '/config/Hacienda_constants.php';
require_once dirname(__DIR__) . '/vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

/**
 * Description of E_envoice_cr_communicator
 *
 * @author pdev
 */
class E_envoice_cr_communicator {

	private $_ci;
	private $_message;
	private $_document_status;
	private $_document_url;
	private $_auth_token;
	private $_refresh_token;

	public function __construct()
	{
		$this->_ci = & get_instance();
		$this->_ci->load->add_package_path(APPPATH . 'third_party/e_envoice_cr/');
		$this->_ci->load->model('Appconfig');

		$this->_message = '';
		$this->_document_status = '';
		$this->_auth_token = '';
		$this->_refresh_token = '';
	}

	public function get_url_document()
	{
		return $this->_document_url;
	}

	public function get_error_message()
	{
		return $this->_message;
	}

	public function get_status()
	{
		return $this->_document_status;
	}

	public function send_document(&$document_info, $xml_file)
	{
		$this->login();
		$url = $this->get_api_url() . 'recepcion';
		$client = $this->get_connection_client();
		$data = $this->get_document_payload($document_info, $xml_file);
		try
		{
			$response = $client->post($url, [
				RequestOptions::JSON => $data,
			]);
			$this->process_send_document_response($response);
		}
		catch (GuzzleHttp\Exception\RequestException $e_req)
		{
			$this->_message = $e_req->getMessage();
			$this->_document_status = 'Pendiente';
			$this->_document_url = '';
		}
		$this->logout();
	}

	protected function process_send_document_response(&$response)
	{
		$code = $response->getStatusCode();
		switch ($code) {
			case 201:
			case 202:
				$url = $response->getHeader('Location');
				$this->_message = 'Enviado';
				$this->_document_status = 'Enviado';
				$this->_document_url = implode(' ', $url);
				break;
			case 400:
				$message = $response->getHeader('X-Error-Cause');
				$this->_message = implode('. ', $message);
				$this->_document_status = 'Invalido';
				$this->_document_url = '';
				break;
			case 401:
			case 403:
			case 429:
				$this->_document_status = 'Pendiente';
				$this->_document_url = '';
				break;
			default :
				break;
		}
	}

	/**
	 * It gets the connection token.
	 */
	protected function login()
	{
		$username = $this->_ci->Appconfig->get('e_envoice_cr_username');
		$password = $this->_ci->Appconfig->get('e_envoice_cr_password');
		$url = $this->get_authentication_url();
		$url .= '/token';
		$client_id = $this->get_authentication_client_id();

		$client = new Client(array(
			'http_errors' => false,
			'connect_timeout' => 5,
			'timeout' => 15,)
		);

		if (!empty($username) && !empty($password))
		{
			try
			{
				$request = $client->request('POST', $url, array(
					'form_params' => array(
						'client_id' => $client_id,
						'client_secret' => '',
						'grant_type' => 'password',
						'username' => $username,
						'password' => $password,
						'scope' => '',
					)
				));
				$response = $request->getBody();
				$tokens = json_decode($response);
				$this->_auth_token = $tokens->access_token;
				$this->_refresh_token = $tokens->refresh_token;
			}
			catch (GuzzleHttp\Exception\RequestException $exc)
			{
				$this->_auth_token = '';
				$this->_refresh_token = '';
			}
		}
		return $this->_auth_token;
	}

	protected function logout()
	{
		$url = $this->get_authentication_url();
		$url .= '/logout';
		$client = $this->get_connection_client();
		$client_id = $this->get_authentication_client_id();
		try
		{
			$request = $client->request('POST', $url, array(
				'form_params' => array(
					'client_id' => $client_id,
					'refresh_token' => $this->_refresh_token,
				)
			));
			$code = $request->getStatusCode();
		}
		catch (GuzzleHttp\Exception\RequestException $e_req)
		{

		}

		$this->_auth_token = '';
		$this->_refresh_token = '';
	}

	protected function get_authentication_url()
	{
		$environment = $this->_ci->Appconfig->get('e_envoice_cr_env');
		if ($environment === Hacienda_constants::ENVIRONMENT_TYPE_PROD)
		{
			$url = Hacienda_constants::AUTH_URL_PROD;
		}
		else
		{
			$url = Hacienda_constants::AUTH_URL_STAG;
		}
		return $url;
	}

	protected function get_authentication_client_id()
	{
		$environment = $this->_ci->Appconfig->get('e_envoice_cr_env');
		if ($environment === Hacienda_constants::ENVIRONMENT_TYPE_PROD)
		{
			$client_id = Hacienda_constants::AUTH_CLIENT_PROD;
		}
		else
		{
			$client_id = Hacienda_constants::AUTH_CLIENT_STAG;
		}
		return $client_id;
	}

	protected function get_connection_client()
	{
		$client = new Client([
			'http_errors' => false,
			'connect_timeout' => 5,
			'timeout' => 15,
			'headers' => [
				'Authorization' => 'Bearer ' . $this->_auth_token,
				'Accept' => 'application/json',
			],
		]);
		return $client;
	}

	protected function get_document_payload(&$document_info, $xml_file)
	{
		$document_content = file_get_contents($xml_file);

		$data = [
			'clave' => $document_info['key'],
			'fecha' => $document_info['date'],
			'emisor' => [
				'tipoIdentificacion' => $document_info['emitter']['id_type'],
				'numeroIdentificacion' => $document_info['emitter']['id_number'],
			],
		];

		if (!empty($document_info['receiver']))
		{
			$data['receptor'] = [
				'tipoIdentificacion' => $document_info['receiver']['id_type'],
				'numeroIdentificacion' => $document_info['receiver']['id_number'],
			];
		}

		$data['comprobanteXml'] = base64_encode($document_content);

		return $data;
	}

	protected function get_api_url()
	{
		$environment = $this->_ci->Appconfig->get('e_envoice_cr_env');
		if ($environment === Hacienda_constants::ENVIRONMENT_TYPE_PROD)
		{
			$url = Hacienda_constants::API_URL_PROD;
		}
		else
		{
			$url = Hacienda_constants::API_URL_STAG;
		}
		return $url;
	}

}
