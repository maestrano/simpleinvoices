<?php

require_once 'BaseMapper.php';
require_once 'InvoiceMapper.php';

/**
* Map Connec Payment::PaymentLine to/from SimpleInvoices Payment
*/
class PaymentTransactionMapper extends BaseMapper {

  public function __construct() {
    parent::__construct();

    $this->connec_entity_name = 'Transaction';
    $this->local_entity_name = 'payments';
    $this->connec_resource_name = 'transactions';
    $this->connec_resource_endpoint = 'transactions';
  }

  // Return the Customer local id
  protected function getId($model) {
    return $model->id;
  }

  // Fetch a local model by id
  public function loadModelById($local_id) {
    $model = (object) getPayment($local_id);
    return $model;
  }

  // Method override
  // Link the local payment to the Connec! Transaction using the Connec!
  // Invoice id it relates to
  public function findOrCreateIdMap($cnc_hash, $model) {
    // Ensure the linked invoice is persisted in Connec!
    $invoice_mapper = new InvoiceMapper();
    $invoice = $invoice_mapper->loadModelById($model->invoice_id);
    $mno_id_map = $invoice_mapper->findIdMapOrPersist($invoice);

    // Create a Connec! transaction hash using the invoice id
    $cnc_transaction_hash = array( 'id' => $mno_id_map['mno_entity_guid']);

    // Fallback on BaseMapper logic
    return parent::findOrCreateIdMap($cnc_transaction_hash,$model);
  }

  // Method override
  // Return SimpleInvoices payment model
  protected function initializeNewModel() {
    return new payment();
  }

  // Map the Connec payment transaction to a SimpleInvoice payment
  // This method has one additional argument - $cnc_line_hash, $cnc_transaction_hash - compared to
  // usual implementations, which is used to map the right id, invoice and
  // amount
  protected function mapConnecResourceToModel($cnc_payment_hash, $model, $cnc_line_hash = null, $cnc_transaction_hash= null) {
    // Map payment attributes
    if($this->is_set($cnc_payment_hash['transaction_date'])) {
      $model->ac_date = $cnc_payment_hash['transaction_date'];
    }
    $model->ac_amount = $cnc_line_hash['amount']['total_amount'] ? $cnc_line_hash['amount']['total_amount'] : 0;
    $model->currency = $cnc_line_hash['amount']['currency'] ? $cnc_line_hash['amount']['currency'] : 'USD';

    // Map Payment Method
    if($this->is_set($cnc_payment_hash['payment_method'])) {
      $pymt_type_mapper = new PaymentMethodMapper();
      $pymt_type = $pymt_type_mapper->loadModelByConnecId($cnc_payment_hash['payment_method']['id']);
      $model->ac_payment_type = $pymt_type->id;
    }

    // Map payment note
    if($this->is_set($cnc_payment_hash['private_note'])) {
      $model->ac_notes = $cnc_payment_hash['private_note'];
    } else if($this->is_set($cnc_payment_hash['public_note'])) {
      $model->ac_notes = $cnc_payment_hash['public_note'];
    } else if($this->is_set($cnc_payment_hash['payment_reference'])) {
      $model->ac_notes = $cnc_payment_hash['payment_reference'];
    } else {
      $model->ac_notes = '';
    }

    // Map invoice
    $invoice_mapper = new InvoiceMapper();
    $invoice = $invoice_mapper->loadModelByConnecId($cnc_transaction_hash['id']);
    $model->ac_inv_id = $invoice->id;
    $model->invoice_id = $invoice->id;
  }

  // Map the SimpleInvoice payment to a connec! transaction
  protected function mapModelToConnecResource($model) {
    $cnc_hash = array();

    // Ensure the linked invoice is persisted in Connec!
    $invoice_mapper = new InvoiceMapper();
    $invoice = $invoice_mapper->loadModelById($model->invoice_id);
    $mno_id_map = $invoice_mapper->findIdMapOrPersist($invoice);

    // Map invoice id
    $cnc_hash['id'] = $mno_id_map['mno_entity_guid'];
    $cnc_hash['class'] = 'Invoice';

    return $cnc_hash;
  }

  // Persist the SimpleInvoices Payment
  protected function persistLocalModel($model, $cnc_hash) {
    if ($this->getId($model)) {
      // do nothing - we don't want to update payments
    } else {
      // Insert model
      $query = $model->insert();
      $model->id = $query['id'];
    }
  }
}
