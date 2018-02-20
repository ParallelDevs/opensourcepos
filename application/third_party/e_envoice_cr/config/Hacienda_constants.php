<?php

/**
 * Description of Document_Type
 *
 * @author pdev
 */
class Hacienda_constants {

  const ID_TYPE_PHYSICAL_PERSON_ID = "01";
  const ID_TYPE_COMPANY_ID = "02";
  const ID_TYPE_DIMEX_ID = "03";
  const ID_TYPE_NITE_ID = "04";
  const DOCUMENT_TYPE_ELECTRONIC_BILL = "FE";
  const DOCUMENT_TYPE_ELECTRONIC_TICKET = "TE";
  const DOCUMENT_TYPE_CREDIT_NOTE = "NC";
  const DOCUMENT_TYPE_DEBIT_NOTE = "ND";
  const ENVIRONMENT_TYPE_PROD = "1";
  const ENVIRONMENT_TYPE_STAG = "2";
  const ENVIRONMENT_URL_PROD = "https://idp.comprobanteselectronicos.go.cr/auth/realms/rut/protocol/openid-connect/token";
  const ENVIRONMENT_URL_STAG = 'https://idp.comprobanteselectronicos.go.cr/auth/realms/rut-stag/protocol/openid-connect/token';
  const ENVIRONMENT_CLIENT_PROD = "api-prod";
  const ENVIRONMENT_CLIENT_STAG = "api-stag";

  public static function get_id_types() {
    return [
      self::ID_TYPE_PHYSICAL_PERSON_ID => "Physical person id",
      self::ID_TYPE_COMPANY_ID => "Company id",
      self::ID_TYPE_DIMEX_ID => "DIMEX",
      self::ID_TYPE_NITE_ID => "NITE",
    ];
  }

  public static function get_environments() {
    return [
      self::ENVIRONMENT_TYPE_PROD => "Production",
      self::ENVIRONMENT_TYPE_STAG => "Sandbox",
    ];
  }

  public static function get_tagname_by_document_type($type) {
    $tagName = '';

    switch ($type) {
      case self::DOCUMENT_TYPE_ELECTRONIC_BILL:
        $tagName .= 'FacturaElectronica';
        break;
      case self::DOCUMENT_TYPE_ELECTRONIC_TICKET:
        $tagName .= 'TiqueteElectronico';
        break;
      case self::DOCUMENT_TYPE_CREDIT_NOTE:
        $tagName .= 'NotaCreditoElectronica';
        break;
      case self::DOCUMENT_TYPE_DEBIT_NOTE:
        $tagName .= 'NotaDebitoElectronica';
        break;
      default:
        $tagName .= '';
        break;
    }
    return $tagName;
  }

  public static function get_xlmns_by_document_type($type) {
    $xmlns = '';
    switch ($type) {
      case 'FE':
        $xmlns .= 'https://tribunet.hacienda.go.cr/docs/esquemas/2017/v4.2/facturaElectronica';
        break;
      case 'TE':
        $xmlns .= 'https://tribunet.hacienda.go.cr/docs/esquemas/2017/v4.2/tiqueteElectronico';
        break;
      case 'NC':
        $xmlns .= 'https://tribunet.hacienda.go.cr/docs/esquemas/2017/v4.2/notaCreditoElectronica';
        break;
      case 'ND':
        $xmlns .= 'https://tribunet.hacienda.go.cr/docs/esquemas/2017/v4.2/notaDebitoElectronica';
        break;
      default:
        $xmlns .= '';
        break;
    }
    return $xmlns;
  }

}
