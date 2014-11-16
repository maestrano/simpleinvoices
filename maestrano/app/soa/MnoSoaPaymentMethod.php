<?php

/**
 * Mno PaymentMethod Class
 */
class MnoSoaPaymentMethod extends MnoSoaBasePaymentMethod {
  protected $_local_entity_name = "PAYMENT_METHOD";

  protected function pushPaymentMethod() {
    $this->_log->debug("start pushPaymentMethod " . json_encode($this->_local_entity));

    $id = $this->getLocalEntityIdentifier();
    if (empty($id)) { return; }

    $mno_id = $this->getMnoIdByLocalIdName($id, $this->_local_entity_name);
    $this->_id = ($this->isValidIdentifier($mno_id)) ? $mno_id->_id : null;

    $this->_name = $this->_local_entity['pt_description'];

    $this->_log->debug("after pushPaymentMethod");
  }

  protected function pullPaymentMethod() {
    MnoSoaLogger::debug("start " . __FUNCTION__ . " for " . json_encode($this->_id));
        
    if (!empty($this->_id)) {
      $local_id = $this->getLocalIdByMnoId($this->_id);
      MnoSoaLogger::debug(__FUNCTION__ . " this->getLocalIdByMnoId(this->_id) = " . json_encode($local_id));
      
      if ($this->isValidIdentifier($local_id)) {
        MnoSoaLogger::debug(__FUNCTION__ . " updating payment method " . json_encode($local_id));
        $status = constant('MnoSoaBaseEntity::STATUS_EXISTING_ID');
      } else if ($this->isDeletedIdentifier($local_id)) {
        MnoSoaLogger::debug(__FUNCTION__ . " is STATUS_DELETED_ID");
        $status = constant('MnoSoaBaseEntity::STATUS_DELETED_ID');
      } else {
        MnoSoaLogger::debug(__FUNCTION__ . " creating payment method rate " . json_encode($local_id));
        $status = constant('MnoSoaBaseEntity::STATUS_NEW_ID');
      }
    } else {
      $status = constant('MnoSoaBaseEntity::STATUS_ERROR');
    }

    return $status;
  }

  protected function saveLocalEntity($push_to_maestrano, $status) {
    $this->_log->debug("start saveLocalEntity status=$status");

    $local_id = $this->getLocalIdByMnoId($this->_id);
    $payment_method_name = $this->pull_set_or_delete_value($this->_name);

    $this->_log->debug("creating or updating payment method $payment_method_name with id " . json_encode($local_id));

    if($status == constant('MnoSoaBaseEntity::STATUS_NEW_ID')) {
      // Try to find any existing tax rate with same name
      $local_payment_type = $this->findPaymentMethodByCodeOrName($payment_method_name);
      if(isset($local_payment_type)) {
        $pt_id = $local_payment_type['pt_id'];
        $this->updatePaymentMethod($pt_id,  $payment_method_name);
      } else {
        $this->insertPaymentMethod($payment_method_name);
        $pt_id = lastInsertId();
      }

      // Map Payment Method ID
      $this->addIdMapEntry($pt_id, $this->_id);
    }

    if($status == constant('MnoSoaBaseEntity::STATUS_EXISTING_ID')) {
      // Update tax rate
      if(isset($local_id->_id)) {
        $this->updatePaymentMethod($local_id->_id,  $payment_method_name);
      }
    }
  }

  public function getLocalEntityIdentifier() {
    return $this->_local_entity['id'];
  }

  private function insertPaymentMethod($payment_method_name) {
    $sql = "INSERT INTO ".TB_PREFIX."payment_types (pt_description, pt_enabled, domain_id) ";
    $sql.= "VALUES ('$payment_method_name', 1, 1)";
    $this->_db->query($sql);
  }

  private function updatePaymentMethod($payment_method_id,  $payment_method_name) {
    $sql = "UPDATE ".TB_PREFIX."payment_types ";
    $sql.= " SET pt_description='$payment_method_name' ";
    $sql.= " WHERE id=$payment_method_id";
    $this->_db->query($sql);
  }

  private function findPaymentMethodByCodeOrName($payment_method_code, $payment_method_name) {
    $payment_methods = $this->fetchPaymentMethods();
    foreach ($payment_methods as $payment_method) {
      if($payment_method['code'] == $payment_method_code || $payment_method['libelle'] == $payment_method_name) {
        return $payment_method;
      }
    }

    return null;
  }

  private function findPaymentTypeByLabel($payment_method_name) {
    $payment_types = getPaymentTypes();
    foreach ($payment_types as $payment_type) {
      if($payment_type['pt_description'] == $payment_method_name) {
        return $payment_type;
      }
    }
    return null;
  }

  private function mapMnoPaymentMethodCode($payment_method_code) {
    $mapping = array (
      'TIP' => 'TIP',
      'VIR' => 'DEP',
      'PRE' => 'ORD',
      'LIQ' => 'CASH',
      'CB'  => 'CC',
      'CHQ' => 'CHECK',
      'VAD' => 'VAD',
      'TRA' => 'TRA',
      'LCR' => 'LCR',
      'FAC' => 'FAC',
      'PRO' => 'PRO'
    );

    return $mapping[$payment_method_code];
  }

  private function mapLocalPaymentMethodCode($payment_method_code) {
    $mapping = array (
      'TIP'   => 'TIP',
      'DEP'   => 'VIR',
      'ORD'   => 'PRE',
      'CASH'  => 'LIQ',
      'CC'    => 'CB',
      'CHECK' => 'CHQ',
      'VAD'   => 'VAD',
      'TRA'   => 'TRA',
      'LCR'   => 'LCR',
      'FAC'   => 'FAC',
      'PRO'   => 'PRO'
    );

    return $mapping[$payment_method_code];
  }
}

?>
