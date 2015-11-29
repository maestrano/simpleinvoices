<?php

require_once 'BaseMapper.php';
require_once 'MnoIdMap.php';

/**
* Map Connec TaxCode representation to/from SimpleInvoice tax
*/
class TaxCodeMapper extends BaseMapper {

  public function __construct() {
    parent::__construct();

    $this->connec_entity_name = 'TaxCode';
    $this->local_entity_name = 'tax';
    $this->connec_resource_name = 'tax_codes';
    $this->connec_resource_endpoint = 'tax_codes';
  }

  // Return the Customer local id
  protected function getId($model) {
    return $model->tax_id;
  }

  // Initialize a new model and assign id
  public function loadModelById($local_id) {
    $model = (object) getTaxRate($local_id);
    return $model;
  }

  // Fetch SimpleInvoice TaxCode by name
  protected function matchLocalModel($cnc_hash) {
    $tax_label = $cnc_hash['name'];
    $tax_details = getTaxes();
    foreach ($tax_details as $tax_detail) {
      if($tax_detail['tax_description'] == $tax_label) {
        return (object) $tax_detail;
      }
    }
    return null;
  }

  // Overwrite me!
  // Optional: define how to initialize a new model in SimpleInvoices
  protected function initializeNewModel() {
    return (object) array();
  }

  // Map the Connec resource attributes onto the SimpleInvoice Item
  protected function mapConnecResourceToModel($cnc_hash, $model) {
    // Map hash attributes to Customer
    if(!is_null($cnc_hash['name'])) { $model->tax_description = $cnc_hash['name']; }
    if(!is_null($cnc_hash['sale_tax_rate'])) { $model->tax_percentage = $cnc_hash['sale_tax_rate']; }
  }

  // Map the OrangeHRM Customer to a Connec resource hash
  protected function mapModelToConnecResource($model) {
    $cnc_hash = array();

    // Map tax rate to Connec hash
    if(!is_null($model->tax_description)) { $cnc_hash['name'] = $model->tax_description; }
    if(!is_null($model->tax_percentage)) { $cnc_hash['sale_tax_rate'] = $model->tax_percentage; }

    return $cnc_hash;
  }

  // Persist the SimpleInvoices TaxCode
  protected function persistLocalModel($model, $cnc_hash) {
    $_POST['tax_description'] = $model->tax_description;
    $_POST['tax_percentage'] = $model->tax_percentage;
    $_POST['type'] = '%';
    $_POST['tax_enabled'] = 1;

    if ($this->getId($model)) {
      $_GET['id'] = $this->getId($model);
      updateTaxRate(false);
    } else {
      insertTaxRate(false);
      $model->tax_id = lastInsertId();
    }
  }
}
