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

}
