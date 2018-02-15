<?php

require_once dirname(__DIR__) . '/config/Hacienda_constants.php';

trait Settings {

  public function get_id_types() {
    $prompt = ["" => "Select one..."];
    $id_types = Hacienda_constants::get_id_types();
    $options = array_merge($prompt, $id_types);
    return $options;
  }

  public function get_environments() {
    $prompt = ["" => "Select one..."];
    $envs = Hacienda_constants::get_environments();
    $options = array_merge($prompt, $envs);
    return $options;
  }

}
