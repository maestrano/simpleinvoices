<?php

require_once 'BaseMapper.php';
require_once 'MnoIdMap.php';

/**
* Map Connec Organization representation to/from OrangeHRM Customer
*/
class InvoiceLineMapper extends BaseMapper {
  private $invoice = null;
  private $invoice_hash = null;

  public function __construct($invoice=null, $invoice_hash=null) {
    parent::__construct();

    $this->connec_entity_name = 'InvoiceLine';
    $this->local_entity_name = 'INVOICE_LINE';
    $this->connec_resource_name = 'invoices/lines';
    $this->connec_resource_endpoint = 'invoices/lines';

    $this->invoice = (object) $invoice;
    $this->invoice_hash = $invoice_hash;
  }

  // Return the Customer local id
  protected function getId($model) {
    return $model->id;
  }

  // Prefix the Invoice Line ID with the Invoice ID to ensure unicity
  protected function getConnecResourceId($model_hash) {
    return $this->invoice_hash['id'] . "#" . $model_hash['id'];
  }

  // Retrieve an invoice line by id
  public function loadModelById($local_id) {
    $invoice_lines = invoice::getInvoiceItems($this->invoice->id);
    foreach ($invoice_lines as $line) {
      if ($line['id'] == $local_id) {
        return (object) $line;
      }
    }

    return null;
  }

  // Optional: define how to initialize a new model in SimpleInvoices
  protected function initializeNewModel() {
    return (object) array();
  }

  // Map the Connec resource attributes onto the SimpleInvoice Item
  protected function mapConnecResourceToModel($cnc_hash, $model) {
    // Line attributes
    $model->id = $this->invoice->id;
    $model->description = $cnc_hash['description'];

    // Product values
    if($this->is_set($cnc_hash['quantity'])) { $model->quantity = $cnc_hash['quantity']; }
    $model->unit_price = $cnc_hash['unit_price']['net_amount'];

    // Totals
    $model->gross_total = $cnc_hash['total_price']['net_amount'] ? $cnc_hash['total_price']['net_amount'] : 0;
    $model->tax_amount = $cnc_hash['total_price']['tax_amount'] ? $cnc_hash['total_price']['tax_amount'] : 0;
    $model->total_ttc = $cnc_hash['total_price']['total_amount'] ? $cnc_hash['total_price']['total_amount'] : 0;

    // Map Item (Product)
    if($this->is_set($cnc_hash['item_id'])) {
      $itemMapper = new ItemMapper();
      $item = $itemMapper->loadModelByConnecId($cnc_hash['item_id']);
      if (!is_null($item)) {
        $mno_id_map = $itemMapper->findIdMapOrPersist($item);
        $model->product_id = $mno_id_map['app_entity_id'];
      }
    }
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
