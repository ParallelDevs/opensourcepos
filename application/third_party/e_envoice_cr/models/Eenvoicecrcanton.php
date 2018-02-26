<?php

/**
 * Description of Eenvoicecrcanton
 *
 * @author pdev
 */
class Eenvoicecrcanton extends CI_Model {

  public function exists($province_code, $code) {
    $this->db->from('eenvoicecr_canton');
    $this->db->where(array('province_code' => $province_code, 'code' => $code));

    return ($this->db->get()->num_rows() == 1);
  }

  public function get_all($province_code = '') {
    $this->db->select('province_code,code,name');
    $this->db->from('eenvoicecr_canton');
    $this->db->where(array('province_code' => $province_code));
    $this->db->order_by('code', 'name');
    $query = $this->db->get();

    return $query;
  }

  public function get($province_code = '', $code = '') {

    $query = $this->db->get_where('eenvoicecr_canton', array(
      'province_code' => $province_code,
      'code' => $code), 1);

    if ($query->num_rows() == 1) {
      return $query->row()->name;
    }

    return '';
  }

  public function save($province_code, $code, $name) {
    $data = array(
      'province_code' => $province_code,
      'code' => $code,
      'name' => $name
    );

    if (!$this->exists($province_code, $code)) {
      return $this->db->insert('eenvoicecr_canton', $data);
    }

    $this->db->where(array('province_code' => $province_code, 'code' => $code));

    return $this->db->update('eenvoicecr_canton', $data);
  }

}
