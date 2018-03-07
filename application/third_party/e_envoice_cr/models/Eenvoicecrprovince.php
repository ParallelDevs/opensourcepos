<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

/**
 * Description of Eenvoicecrprovince
 *
 * @author pdev
 */
class Eenvoicecrprovince extends CI_Model {

  public function exists($code) {
    $this->db->from('eenvoicecr_provinces');
    $this->db->where('eenvoicecr_provinces', array('code'=>$code));

    return ($this->db->get()->num_rows() == 1);
  }

  public function get_all() {
    $this->db->select('code,name');
    $this->db->from('eenvoicecr_provinces');
    $this->db->order_by('code', 'name');
    $query = $this->db->get();

    return $query;
  }

  public function get($code = '') {

    $query = $this->db->get_where('eenvoicecr_provinces', array('code' => $code), 1);

    if ($query->num_rows() == 1) {
      return $query->row()->name;
    }

    return '';
  }

  public function save($code, $name) {
    $data = array(
      'code' => $code,
      'name' => $name
    );

    if (!$this->exists($code)) {
      return $this->db->insert('eenvoicecr_provinces', $data);
    }

    $this->db->where('code', $code);

    return $this->db->update('eenvoicecr_provinces', $data);
  }

}
