<?php

require_once 'BaseMapper.php';

/**
* Map Connec Payment::PaymentLine to/from SimpleInvoices Payment
*/
class PaymentLineMapper extends BaseMapper {

  public function __construct() {
    parent::__construct();

    $this->connec_entity_name = 'PaymentLine';
    $this->local_entity_name = 'payment_line';
    $this->connec_resource_name = 'payment_lines';
    $this->connec_resource_endpoint = 'payment_lines';
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
  // Delegate method to PaymentTransactionMapper
  // Note: payment_lines are added to the model when mapConnecResourceToModel
  // gets called
  public function findOrCreateIdMap($cnc_hash, $model) {
    if(!$this->is_set($model->transactions)){ $model->transactions = array(); }

    // For each payment line, delegate method
    // Transaction ID is the actual connec! invoice id which is known by
    // SimpleInvoices
    // No need to pass an actual connec! hash
    foreach($model->transactions as $transaction) {
      $transaction_mapper = new PaymentTransactionMapper();
      $transaction_mapper->findOrCreateIdMap(null,$transaction);

      // Link the Connec PaymentLine to the Connec Transaction inside the IdMap
      // so that things can be traced
      MnoIdMap::upsertMnoIdMap($payment_line->id,$payment_line_mapper->local_entity_name,$cnc_hash['id'],$this->connec_entity_name);
    }
  }

  // Method override
  // The payment line is only a virtual object - it's not directly mapped to a model
  // in SimpleInvoices. Model mapping is delegated to the lowest level which is
  // PaymentTransactionMapper.
  protected function initializeNewModel() {
    return array();
  }

  // Map the Connec payment line to a list of SimpleInvoice payments
  // This method has one additional argument - $cnc_line_hash - which is
  // used to retrieve the right list of transactions
  protected function mapConnecResourceToModel($cnc_payment_hash, $model, $cnc_line_hash = null) {
    if(!$this->is_set($model->transactions)){ $model->transactions = array(); }

    // Map transactions
    foreach($cnc_line_hash['linked_transactions'] as $cnc_transaction_hash) {
      $transaction_mapper = new PaymentTransactionMapper();
      $payment = $transaction_mapper->initializeNewModel();
      $transaction_mapper->mapConnecResourceToModel($cnc_payment_hash,$payment,$cnc_line_hash,$cnc_transaction_hash);
      $model->transactions[] = $payment;
    }
  }

  // Map the SimpleInvoice payment to a connec! payment line
  protected function mapModelToConnecResource($model) {
    $cnc_hash = array();

    // Map payment line attributes
    $cnc_hash['id'] = uniqid();
    $cnc_hash['amount'] = floatval($model->ac_amount);

    // Map transactions
    $transaction_mapper = new PaymentTransactionMapper();
    $cnc_hash['linked_transactions'] = array(
      $transaction_mapper->mapModelToConnecResource($model)
    );

    return $cnc_hash;
  }

  // Persist all transactions attached to the payment line
  // Note: transactions are added to the model when mapConnecResourceToModel
  // is called
  protected function persistLocalModel($model, $cnc_hash) {
    foreach($model->transactions as $payment) {
      $transaction_mapper = new PaymentTransactionMapper();
      $transaction_mapper->persistLocalModel($payment);
    }
  }
}
