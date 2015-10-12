<?php

require_once 'BaseMapper.php';
require_once 'MnoIdMap.php';
require_once 'InvoiceMapper.php';
require_once 'TaxCodeMapper.php';
require_once 'ItemMapper.php';

/**
* Map Connec Organization representation to/from OrangeHRM Customer
*/
class InvoiceLineMapper extends BaseMapper {
  private $invoice = null;

  public function __construct($invoice=null) {
    parent::__construct();

    $this->connec_entity_name = 'InvoiceLine';
    $this->local_entity_name = 'INVOICE_LINE';
    $this->connec_resource_name = 'invoices/lines';
    $this->connec_resource_endpoint = 'invoices/lines';

    $this->invoice = (object) $invoice;
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

  // Takes an id and delete an invoice line permanently,
  // including its mno id map.
  public function hardDeleteById($id) {
    delete('invoice_items','id',$id);
    MnoIdMap::hardDeleteMnoIdMap($id, $this->local_entity_name);
  }

  // Optional: define how to initialize a new model in SimpleInvoices
  protected function initializeNewModel() {
    return (object) array();
  }

  // Map the Connec resource attributes onto the SimpleInvoice Invoice Item
  protected function mapConnecResourceToModel($cnc_hash, $model) {
    // Line attributes
    $model->invoice_id = $this->invoice->id;
    $model->description = $cnc_hash['description'];

    // Product - line values
    if($this->is_set($cnc_hash['quantity'])) { $model->quantity = $cnc_hash['quantity']; }
    if($this->is_set($cnc_hash['unit_price']['net_amount'])) { $model->unit_price = $cnc_hash['unit_price']['net_amount']; }

    // Totals
    $model->gross_total = $cnc_hash['total_price']['net_amount'] ? $cnc_hash['total_price']['net_amount'] : 0;
    $model->tax_amount = $cnc_hash['total_price']['tax_amount'] ? $cnc_hash['total_price']['tax_amount'] : 0;
    $model->total_ttc = $cnc_hash['total_price']['total_amount'] ? $cnc_hash['total_price']['total_amount'] : 0;

    // Map Tax Code
    if($this->is_set($cnc_hash['tax_code_id'])) {
      $taxCodeId = $this->findLocalTaxCodeIdByMnoTaxCodeId($cnc_hash['tax_code_id']);
      if (!is_null($taxCodeId)) { $model->tax_code_id = $taxCodeId; }
    }

    // Map Item (Product)
    if($this->is_set($cnc_hash['item_id'])) {
      $itemId = $this->findLocalProductIdByMnoItemId($cnc_hash['item_id']);
      if (!is_null($itemId)) { $model->product_id = $itemId; }
    }
  }

  // Map the Invoice Item to a Connec Invoice Line resource hash
  protected function mapModelToConnecResource($model) {
    $cnc_hash = array();

    // Get or fetch currency_code
    if(is_null($model->currency_code)) {
      $invoiceMapper = new InvoiceMapper();
      $model->currency_code = $invoiceMapper->getCurrencyCodeById($model->invoice_id);
    }

    // Line attributes
    if(!is_null($model->description)) { $cnc_hash['description'] = $model->description; }

    // Product - line values
    if(!is_null($model->quantity)) { $cnc_hash['quantity'] = $model->quantity; }
    if(!is_null($model->unit_price)) {
      $cnc_hash['unit_price']['net_amount'] = $model->quantity;
      $cnc_hash['unit_price']['currency'] = $model->currency_code;
    }

    // Totals
    $cnc_hash['total_price']['net_amount'] = $model->gross_total ? $model->gross_total : 0;
    $cnc_hash['total_price']['tax_amount'] =  $model->tax_amount ? $model->tax_amount : 0;
    $cnc_hash['total_price']['total_amount'] = $model->total_ttc ? $model->total_ttc : 0;
    $cnc_hash['total_price']['currency'] = $model->currency_code;

    // Map Tax Code
    $taxCodeMnoId = $this->findMnoTaxCodeIdByInvoiceItemId($model->id);
    if(!is_null($taxCodeMnoId)) { $cnc_hash['tax_code_id'] = $taxCodeMnoId; }

    // Map Product
    $productId = $this->findMnoItemIdByProductId($model->product_id);
    if(!is_null($itemId)) { $cnc_hash['item_id'] = $productId; }

    return $cnc_hash;
  }

  // Persist the SimpleInvoices Item
  protected function persistLocalModel($model, $cnc_hash) {
    if ($this->getId($model)) {
      updateInvoiceItem($this->getId($model), $model->quantity, $model->product_id, 0, $model->tax_code_id, $model->description, $model->unit_price, false);
    } else {
      $local_id = insertInvoiceItem($model->invoice_id, $model->quantity, $model->product_id, 0, $model->tax_code_id, $model->description, $model->unit_price, false);
      $model->id = $local_id;
    }
  }

  //==========================================================================
  // Private
  //==========================================================================
  // Takes the connec! id of tax code and returns its local id
  // If the tax code has never been fetched from connec! then it
  // gets fetched and persisted
  private function findLocalTaxCodeIdByMnoTaxCodeId($mno_id) {
    $taxCodeMapper = new TaxCodeMapper();
    // Load Tax Code locally or fetch from Connec!
    $taxCode = $taxCodeMapper->loadModelByConnecId($mno_id);

    if (!is_null($taxCode)) {
      $mno_id_map = $taxCodeMapper->findIdMapOrPersist($taxCode);
      return $mno_id_map['app_entity_id'];
    }

    return null;
  }

  // Takes the connec! id of an item and returns its local id
  // If the item never been fetched from connec! then it
  // gets fetched and persisted
  private function findLocalProductIdByMnoItemId($mno_id) {
    $itemMapper = new ItemMapper();
    // Load Item locally or fetch from Connec!
    $item = $itemMapper->loadModelByConnecId($mno_id);
    if (!is_null($item)) {
      $mno_id_map = $itemMapper->findIdMapOrPersist($item);
      return $mno_id_map['app_entity_id'];
    }

    return null;
  }

  // Takes the id of an invoice item in argument and returns
  // the connec! id of the related tax code
  // If the tax code has not been previously pushed to Connec!
  // then it gets pushed
  private function findMnoTaxCodeIdByInvoiceItemId($id) {
    // Fetch local id of the related tax code
    $query = 'SELECT tax_id FROM '.TB_PREFIX.'invoice_item_tax WHERE invoice_item_id = :id LIMIT 1';
    $result = dbQuery($query, ':id', $id);
    $row = $result->fetch();

    // Get the Tax Code
    $taxCodeMapper = new TaxCodeMapper();
    $taxCode = $taxCodeMapper->loadModelById($row['tax_id']);
    if(is_null($taxCode)) { return null; }

    // Get the mno id from id map or by persisting the tax code
    $mno_id_map = $taxCodeMapper->findIdMapOrPersist($taxCode);
    return $mno_id_map['mno_entity_guid'];
  }

  // Takes the id of an item and returns it connec! id
  // If the item has not been previously pushed to Connec! then
  // it gets pushed.
  private function findMnoItemIdByProductId($product_id) {
    $itemMapper = new ItemMapper();
    $item = $itemMapper->loadModelById($product_id);
    $mno_id_map = $itemMapper->findIdMapOrPersist($item);
    return $mno_id_map['mno_entity_guid'];
  }
}
