<?php

require_once 'BaseMapper.php';

/**
* Map Connec PaymentMethod representation to/from SimpleInvoices Payment Type
*/
class PaymentMethodMapper extends BaseMapper {

  public function __construct() {
    parent::__construct();

    $this->connec_entity_name = 'PaymentMethod';
    $this->local_entity_name = 'payment_method';
    $this->connec_resource_name = 'payment_methods';
    $this->connec_resource_endpoint = 'payment_methods';
  }

  // Return the Customer local id
  protected function getId($model) {
    return $model->pt_id;
  }

  // Initialize a new model and assign id
  public function loadModelById($local_id) {
    $model = (object) $this->getPaymentType($local_id);
    return $model;
  }

  // Overwrite me!
  // Optional: define how to initialize a new model in SimpleInvoices
  protected function initializeNewModel() {
    return (object) array();
  }

  // Map the Connec resource attributes onto the model
  protected function mapConnecResourceToModel($cnc_hash, $model) {
    // Map hash attributes to Customer
    if(!is_null($cnc_hash['name'])) { $model->pt_description = $cnc_hash['name']; }
  }

  // Map the model to a Connec resource hash
  protected function mapModelToConnecResource($model) {
    $cnc_hash = array();

    // Map Customer to Connec hash
    if(!is_null($model->pt_description)) { $cnc_hash['name'] = $model->pt_description; }

    return $cnc_hash;
  }

  // Persist the SimpleInvoices model
  protected function persistLocalModel($model, $cnc_hash) {
    // Try to match on payment type name
    $pymt_type = $this->findPaymentTypeByLabel($model->pt_description);
    if(!is_null($pymt_type) && $this->is_set($pymt_type['pt_id'])) { $model->pt_id = $pymt_type['pt_id']; }

    if ($this->getId($model)) {
      $this->updatePaymentMethod($model->pt_id, $model->pt_description);
    } else {
      $res = $this->insertPaymentMethod($model->pt_description);
      $model->pt_id = lastInsertId();
    }
  }


  //===========================================================================
  // Private methods
  //===========================================================================
  private function getPaymentType($id) {
  	global $dbh;
  	$sql = "SELECT * FROM ".TB_PREFIX."payment_types WHERE pt_id = :id";
  	$sth = dbQuery($sql, ':id', $id);
  	$paymentType = $sth->fetch();
  	return $paymentType;
  }

  private function insertPaymentMethod($payment_method_name) {
    global $db;
    $sql = "INSERT INTO ".TB_PREFIX."payment_types (pt_description, pt_enabled, domain_id) ";
    $sql.= "VALUES ('$payment_method_name', 1, 1)";
    $db->query($sql);
  }

  private function updatePaymentMethod($payment_method_id,  $payment_method_name) {
    global $db;
    $sql = "UPDATE ".TB_PREFIX."payment_types ";
    $sql.= " SET pt_description='$payment_method_name' ";
    $sql.= " WHERE pt_id=$payment_method_id";
    $db->query($sql);
  }

  private function findPaymentTypeByLabel($payment_method_name) {
    global $dbh;
  	$sql = "SELECT * FROM ".TB_PREFIX."payment_types WHERE pt_description = :description LIMIT 1";
  	$sth = dbQuery($sql, ':description', $payment_method_name);
  	$paymentType = $sth->fetch();
  	return $paymentType;
  }
}
