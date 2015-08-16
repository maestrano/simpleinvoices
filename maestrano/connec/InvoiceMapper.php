<?php

require_once 'BaseMapper.php';
require_once 'InvoiceLineMapper.php';
require_once 'MnoIdMap.php';

/**
* Map Connec Organization representation to/from OrangeHRM Customer
*/
class InvoiceMapper extends BaseMapper {

  public function __construct() {
    parent::__construct();

    $this->connec_entity_name = 'Invoice';
    $this->local_entity_name = 'INVOICES';
    $this->connec_resource_name = 'invoices';
    $this->connec_resource_endpoint = 'invoices';
  }

  // Return the Customer local id
  protected function getId($model) {
    return $model->id;
  }

  // Initialize a new model and assign id
  public function loadModelById($local_id) {
    $model = (object) getInvoice($local_id);
    return $model;
  }

  // Overwrite me!
  // Optional: define how to initialize a new model in SimpleInvoices
  protected function initializeNewModel() {
    return (object) array();
  }

  // Map the Connec resource attributes onto the SimpleInvoice Item
  protected function mapConnecResourceToModel($cnc_hash, $model) {
    // Map hash attributes to Customer
    if(!is_null($cnc_hash['name'])) { $model->description = $cnc_hash['name']; }

  }

  // Map the OrangeHRM Customer to a Connec resource hash
  protected function mapModelToConnecResource($model) {
    $cnc_hash = array();

    // Map Product to Connec hash
    if(!is_null($model->description)) { $cnc_hash['name'] = $model->description; }

    return $cnc_hash;
  }

  // Persist the SimpleInvoices Item
  protected function persistLocalModel($model, $cnc_hash) {
    $hash = json_decode(json_encode($model), true);
    if ($this->getId($model)) {
      updateProductByObject($hash, false);
    } else {
      $hash["enabled"] = 1;
      insertProductByObject($hash, 1, 1, false);
      $model->id = $hash['id'];
    }
  }
}
