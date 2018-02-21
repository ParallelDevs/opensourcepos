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
  const DOCUMENT_TYPE_FE = "FE";
  const DOCUMENT_TYPE_TE = "TE";
  const DOCUMENT_TYPE_NC = "NC";
  const DOCUMENT_TYPE_ND = "ND";
  const ENVIRONMENT_TYPE_PROD = "1";
  const ENVIRONMENT_TYPE_STAG = "2";
  const ENVIRONMENT_URL_PROD = "https://idp.comprobanteselectronicos.go.cr/auth/realms/rut/protocol/openid-connect/token";
  const ENVIRONMENT_URL_STAG = 'https://idp.comprobanteselectronicos.go.cr/auth/realms/rut-stag/protocol/openid-connect/token';
  const ENVIRONMENT_CLIENT_PROD = "api-prod";
  const ENVIRONMENT_CLIENT_STAG = "api-stag";
  const DOCUMENT_TYPE_CODE_FE = '01';
  const DOCUMENT_TYPE_CODE_ND = '02';
  const DOCUMENT_TYPE_CODE_NC = '03';
  const DOCUMENT_TYPE_CODE_TE = '04';  
  const XMLNS_FE = 'https://tribunet.hacienda.go.cr/docs/esquemas/2017/v4.2/facturaElectronica';
  const XMLNS_TE = 'https://tribunet.hacienda.go.cr/docs/esquemas/2017/v4.2/tiqueteElectronico';
  const XMLNS_NC = 'https://tribunet.hacienda.go.cr/docs/esquemas/2017/v4.2/notaCreditoElectronica';
  const XMLNS_ND = 'https://tribunet.hacienda.go.cr/docs/esquemas/2017/v4.2/notaDebitoElectronica';

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
      case self::DOCUMENT_TYPE_FE:
        $tagName .= 'FacturaElectronica';
        break;
      case self::DOCUMENT_TYPE_TE:
        $tagName .= 'TiqueteElectronico';
        break;
      case self::DOCUMENT_TYPE_NC:
        $tagName .= 'NotaCreditoElectronica';
        break;
      case self::DOCUMENT_TYPE_ND:
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
      case self::DOCUMENT_TYPE_FE:
        $xmlns .= self::XMLNS_FE;
        break;
      case self::DOCUMENT_TYPE_TE:
        $xmlns .= self::XMLNS_TE;
        break;
      case self::DOCUMENT_TYPE_NC:
        $xmlns .= self::XMLNS_NC;
        break;
      case self::DOCUMENT_TYPE_ND:
        $xmlns .= self::XMLNS_ND;
        break;
      default:
        $xmlns .= '';
        break;
    }
    return $xmlns;
  }

  public static function get_code_by_document_type($type) {
    $code = '';
    switch ($type) {
      case self::DOCUMENT_TYPE_FE:
        $code .= self::DOCUMENT_TYPE_CODE_FE;
        break;
      case self::DOCUMENT_TYPE_TE:
        $code .= self::DOCUMENT_TYPE_CODE_TE;
        break;
      case self::DOCUMENT_TYPE_NC:
        $code .= self::DOCUMENT_TYPE_CODE_NC;
        break;
      case self::DOCUMENT_TYPE_ND:
        $code .= self::DOCUMENT_TYPE_CODE_ND;
        break;
      default:
        $code .= '00';
        break;
    }
    return $code;
  }
}
