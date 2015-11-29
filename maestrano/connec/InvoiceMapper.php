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
    // Map regular attributes
    $model->date = $cnc_hash['transaction_date'];
    $model->note = $cnc_hash['note'];
    if (!$this->is_set($model->preference_id)) { $model->preference_id = 1; }
    if (!$this->is_set($model->biller_id)) { $model->biller_id = 1; }

    // Map currency / it is assumed that all invoice lines are
    // in the same currency
    $model->currency = $cnc_hash['lines'][0]['total_price']['currency'] ? $cnc_hash['lines'][0]['total_price']['currency'] : 'USD';

    // Map Customer (Organization or Person)
    if($this->is_set($cnc_hash['organization_id'])) {
      $organizationMapper = new OrganizationMapper();
      // Load customer locally or fetch from Connec!
      $customer = $organizationMapper->loadModelByConnecId($cnc_hash['organization_id']);
    } else {
      $personMapper = new PersonMapper();
      // Load customer locally or fetch from Connec!
      $customer = $personMapper->loadModelByConnecId($cnc_hash['person_id']);
    }
    $model->customer_id = $customer->id;
  }

  // Map the SimpleInvoice Invoice to a Connec resource hash
  protected function mapModelToConnecResource($model) {
    $cnc_hash = array();

    // Missing transaction lines are considered as deleted by Connec!
    $cnc_hash['opts'] = array('sparse' => false);

    // Regular attributes
    $cnc_hash['status'] = 'ACTIVE';
    $cnc_hash['type'] = 'CUSTOMER';
    if(!is_null($model->note)) { $cnc_hash['public_note'] = $model->note; }
    if(!is_null($model->date)) { $cnc_hash['transaction_date'] = date('c',strtotime($model->date)); }

    // Map Customer id based on type
    $customer = (object) getCustomer($model->customer_id);
    if($customer->type == 'organization') {
      $org_mapper = new OrganizationMapper();
      $org = $org_mapper->loadModelById($model->customer_id);
      $mno_id_map = $org_mapper->findIdMapOrPersist($org);
      $cnc_hash['organization_id'] = $mno_id_map['mno_entity_guid'];
    } else {
      $pers_mapper = new PersonMapper();
      $person = $pers_mapper->loadModelById($model->customer_id);
      $mno_id_map = $pers_mapper->findIdMapOrPersist($org);
      $cnc_hash['person_id'] = $mno_id_map['mno_entity_guid'];
    }

    // Map Customer address
    $cnc_hash['billing_address'] = array(
      'line1' => $customer->street_address,
      'line2' => $customer->street_address2,
      'city' => $customer->city,
      'region' => $customer->state,
      'postal_code' => $customer->zip_code,
      'country' => $customer->country
    );

    // Map Invoice Lines
    $cnc_hash['lines'] = array();
    $invoiceItems = invoice::getInvoiceItems($model->id);
    if(!empty($invoiceItems)) {
      $invoiceLineMapper = new InvoiceLineMapper($model);
      foreach($invoiceItems as $invoiceItem) {
        $line = (object) $invoiceItem;
        $cnc_hash['lines'][] = $invoiceLineMapper->mapModelToConnecResource($line);
      }
    }

    return $cnc_hash;
  }

  // Persist the local Invoice
  protected function persistLocalModel($model, $cnc_hash) {
    // Save the Invoice first
    $hash = json_decode(json_encode($model), true);
    if ($this->getId($model)) {
      updateInvoiceByObject($hash, $this->getId($model), false);
    } else {
      $id = insertInvoiceByObject($hash, 2, false);
      $model->id = $id;
    }

    // Persist Invoice lines
    if(!empty($cnc_hash['lines'])) {
      $processed_lines_local_ids = array();

      // Create/Update invoice lines
      foreach($cnc_hash['lines'] as $line_hash) {
        $invoice_line_mapper = new InvoiceLineMapper($model);
        $invoice_line = $invoice_line_mapper->saveConnecResource($line_hash);
        array_push($processed_lines_local_ids, $invoice_line->id);
      }

      // Delete local invoice lines that are not present in Connec!
      $local_invoice_lines = invoice::getInvoiceItems($model->id);
      $invoice_line_mapper = new InvoiceLineMapper();
      foreach ($local_invoice_lines as $local_invoice_line) {
        if(!in_array($local_invoice_line['id'], $processed_lines_local_ids)) {
          $invoice_line_mapper->hardDeleteById($local_invoice_line['id']);
        }
      }
    }
  }
}
