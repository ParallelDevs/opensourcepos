<?php

if (!defined('BASEPATH')) {
  exit('No direct script access allowed');
}

/**
 * Description of Eenvoicecrdocument
 *
 * @author pdev
 */
class Eenvoicecrsaledocuments extends CI_Model {

  public function exists($document_id) {
    $this->db->from('eenvoicecr_sales_documents');
    $this->db->where(array(
      'document_id' => $document_id,
    ));
    return ($this->db->get()->num_rows() == 1);
  }

  public function get_all($sale_id = '', $document_code = '', $document_status = '') {
    $this->db->select('eenvoicecr_sales_documents.*');
    $this->db->from('eenvoicecr_sales_documents');
    $this->db->where(array(
      'sale_id' => $sale_id,
      'document_code' => $document_code,
      'document_status' => $document_status,
    ));
    $this->db->order_by('sale_id', 'document_code');
    $query = $this->db->get();

    return $query;
  }

  public function get_document($document_id) {
    $this->db->select('eenvoicecr_sales_documents.*');
    $this->db->from('eenvoicecr_sales_documents');
    $this->db->where('document_id', $document_id);
    $query = $this->db->get();

    if ($query->num_rows() == 1) {
      return $query->row();
    }
    else {
      //Get empty base parent object, as $item_id is NOT an item
      $document_obj = new stdClass();

      //Get all the fields from items table
      foreach ($this->db->list_fields('eenvoicecr_sales_documents') as $field) {
        $document_obj->$field = '';
      }

      return $document_obj;
    }
  }

  public function get_document_by_sale_and_code($sale_id, $document_code) {
    $this->db->select('eenvoicecr_sales_documents.*');
    $this->db->from('eenvoicecr_sales_documents');
    $this->db->where(array(
      'sale_id' => $sale_id,
      'document_code' => $document_code,
    ));
    $query = $this->db->get();

    if ($query->num_rows() == 1) {
      return $query->row();
    }
    else {
      //Get empty base parent object, as $item_id is NOT an item
      $document_obj = new stdClass();

      //Get all the fields from items table
      foreach ($this->db->list_fields('eenvoicecr_sales_documents') as $field) {
        $document_obj->$field = '';
      }
      $document_obj->document_id = -1;

      return $document_obj;
    }
  }

  public function save(&$document_data, $document_id = FALSE) {
    if (!$document_id || !$this->exists($document_id)) {
      if ($this->db->insert('eenvoicecr_sales_documents', $document_data)) {
        $document_data['document_id'] = $this->db->insert_id();

        return TRUE;
      }

      return FALSE;
    }

    $this->db->where('document_id', $document_id);

    return $this->db->update('eenvoicecr_sales_documents', $document_data);
  }

}
