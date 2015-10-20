<?php

require_once 'BaseMapper.php';
require_once 'TaxCodeMapper.php';
require_once 'MnoIdMap.php';

/**
* Map Connec Organization representation to/from OrangeHRM Customer
*/
class ItemMapper extends BaseMapper {

  public function __construct() {
    parent::__construct();

    $this->connec_entity_name = 'Item';
    $this->local_entity_name = 'items';
    $this->connec_resource_name = 'items';
    $this->connec_resource_endpoint = 'items';
  }

  // Return the Customer local id
  protected function getId($model) {
    return $model->id;
  }

  // Initialize a new model and assign id
  public function loadModelById($local_id) {
    $model = (object) getProduct($local_id);
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

    // Sale & Purchase Price
    if(!is_null($cnc_hash['sale_price'])) { $model->unit_price = $cnc_hash['sale_price']['net_amount']; }
    if(!is_null($cnc_hash['purchase_price'])) { $model->cost = $cnc_hash['purchase_price']['net_amount']; }

    // Item type
    if(!is_null($cnc_hash['type'])) { $model->type = $cnc_hash['type']; }

    // Tax Code
    if(!is_null($cnc_hash['sale_tax_code_id'])) {
      // Get tax
      $mapper = new TaxCodeMapper();
      $tax_model = $mapper->loadModelByConnecId($cnc_hash['sale_tax_code_id']);

      // Assign tax to product
      if(!is_null($tax_model)) { $model->default_tax_id = $tax_model->tax_id; }
    }

  }

  // Map the OrangeHRM Customer to a Connec resource hash
  protected function mapModelToConnecResource($model) {
    $cnc_hash = array();

    // Map Product to Connec hash
    if(!is_null($model->description)) { $cnc_hash['name'] = $model->description; }

    // Prices
    if(!is_null($model->unit_price)) { $cnc_hash['sale_price']['net_amount'] = $model->unit_price; }
    if(!is_null($model->cost)) { $cnc_hash['purchase_price']['net_amount'] = $model->cost; }

    // Item type
    if(!is_null($model->type)) {
      $cnc_hash['type'] = strtoupper($model->type);
    } else {
      $cnc_hash['type'] = "PRODUCT";
    }

    // Tax Code
    if(!is_null($model->default_tax_id)) {
      $mapper = new TaxCodeMapper();
      $tax_model = $mapper->loadModelById($model->default_tax_id);
      if ($tax_id_map = $mapper->findIdMapOrPersist($tax_model)) {
        $cnc_hash['sale_tax_code_id'] = $tax_id_map['mno_entity_guid'];
      }
    }

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
