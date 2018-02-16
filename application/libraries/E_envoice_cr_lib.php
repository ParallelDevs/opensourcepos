<?php

require_once APPPATH . 'third_party/e_envoice_cr/libraries/E_Envoice_CR_Library.php';

/**
 * Description of E_envoice_cr_lib
 *
 * @author pdev
 */
class E_envoice_cr_lib extends E_Envoice_CR_Library {
  public function __construct() {
    parent::__construct();
    $this->init_invoice();
  }
}
