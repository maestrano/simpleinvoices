<?php

/**
 * Mno Organization Class
 */
class MnoSoaOrganization extends MnoSoaBaseOrganization
{
  protected $_local_entity_name = "org_customer";

  protected function pushId() {
    $this->_log->debug(__CLASS__ . ' ' . __FUNCTION__ . " start");
    $id = $this->getLocalEntityIdentifier();

    if (!empty($id)) {
      $mno_id = $this->getMnoIdByLocalId($id);

      if ($this->isValidIdentifier($mno_id)) {
        $this->_log->debug(__FUNCTION__ . " this->getMnoIdByLocalId(id) = " . json_encode($mno_id));
        $this->_id = $mno_id->_id;
      }
    }
    $this->_log->debug(__CLASS__ . ' ' . __FUNCTION__ . " end");
  }

  protected function pullId() {
    $this->_log->debug(__CLASS__ . ' ' . __FUNCTION__ . " start");
    if (!empty($this->_id)) {
      $local_id = $this->getLocalIdByMnoId($this->_id);
      $this->_log->debug(__FUNCTION__ . " this->getLocalIdByMnoId(this->_id) = " . json_encode($local_id));

      if ($this->isValidIdentifier($local_id)) {
        $this->_local_entity = getCustomer($local_id->_id);
        $this->_log->debug(__CLASS__ . ' ' . __FUNCTION__ . " is STATUS_EXISTING_ID");
        return constant('MnoSoaBaseEntity::STATUS_EXISTING_ID');
      } else if ($this->isDeletedIdentifier($local_id)) {
        $this->_log->debug(__CLASS__ . ' ' . __FUNCTION__ . " is STATUS_DELETED_ID");
        return constant('MnoSoaBaseEntity::STATUS_DELETED_ID');
      } else {
        $this->_local_entity["enabled"] = 1;
        return constant('MnoSoaBaseEntity::STATUS_NEW_ID');
      }
    }
    $this->_log->debug(__CLASS__ . ' ' . __FUNCTION__ . " return STATUS_ERROR");
    return constant('MnoSoaBaseEntity::STATUS_ERROR');
  }

    # Do not push name on subsequent updates
  protected function pushName() {
    $this->_log->debug(__CLASS__ . ' ' . __FUNCTION__ . " start ");

    if (empty($this->_id)) {
      $this->_name = $this->push_set_or_delete_value($this->_local_entity["name"]);
    }

    $this->_log->debug(__CLASS__ . ' ' . __FUNCTION__ . " end ");
  }

  protected function pullName() {
    $this->_log->debug(__CLASS__ . ' ' . __FUNCTION__ . " start ");
    $this->_local_entity["name"] = $this->pull_set_or_delete_value($this->_name);
    $this->_log->debug(__CLASS__ . ' ' . __FUNCTION__ . " end ");
  }

  protected function pushIndustry() {
    // Do nothing
  }

  protected function pullIndustry() {
    // Do nothing
  }

  protected function pushAnnualRevenue() {
    // Do nothing
  }

  protected function pullAnnualRevenue() {
    // Do nothing
  }

  protected function pushCapital() {
    // Do nothing
  }

  protected function pullCapital() {
    // Do nothing
  }

  protected function pushNumberOfEmployees() {
    // Do nothing
  }

  protected function pullNumberOfEmployees() {
    // Do nothing
  }

  protected function pushAddresses() {
    $this->_log->debug(__CLASS__ . ' ' . __FUNCTION__ . " start " . $this->_local_entity["street_address"]);
    // ADDRESS -> POSTAL ADDRESS
    $this->_address->postalAddress->streetAddress = $this->push_set_or_delete_value($this->_local_entity["street_address"]);
    $this->_address->postalAddress->streetAddress2 = $this->push_set_or_delete_value($this->_local_entity["street_address2"]);
    $this->_address->postalAddress->locality = $this->push_set_or_delete_value($this->_local_entity["city"]);
    $this->_address->postalAddress->region = $this->push_set_or_delete_value($this->_local_entity["state"]);
    $this->_address->postalAddress->postalCode = $this->push_set_or_delete_value($this->_local_entity["zip_code"]);
    $this->_log->debug(__CLASS__ . ' ' . __FUNCTION__ . " end ");
  }

  protected function pullAddresses() {
    $this->_log->debug(__CLASS__ . ' ' . __FUNCTION__ . " start ");
          // POSTAL ADDRESS -> PRIMARY ADDRESS
    $this->_local_entity["street_address"] = $this->pull_set_or_delete_value($this->_address->postalAddress->streetAddress);
    $this->_local_entity["street_address2"] = $this->pull_set_or_delete_value($this->_address->postalAddress->streetAddress2);
    $this->_local_entity["city"] = $this->pull_set_or_delete_value($this->_address->postalAddress->locality);
    $this->_local_entity["state"] = $this->pull_set_or_delete_value($this->_address->postalAddress->region);
    $this->_local_entity["zip_code"] = $this->pull_set_or_delete_value($this->_address->postalAddress->postalCode);
    $this->_log->debug(__CLASS__ . ' ' . __FUNCTION__ . " end ");
  }

  protected function pushEmails() {
    $this->_log->debug(__CLASS__ . ' ' . __FUNCTION__ . " start ");
    $this->_email->emailAddress = $this->push_set_or_delete_value($this->_local_entity["email"]);
    $this->_log->debug(__CLASS__ . ' ' . __FUNCTION__ . " end ");
  }

  protected function pullEmails() {
    $this->_log->debug(__CLASS__ . ' ' . __FUNCTION__ . " start ");
    $this->_local_entity["email"] = $this->pull_set_or_delete_value($this->_email->emailAddress);
    $this->_log->debug(__CLASS__ . ' ' . __FUNCTION__ . " end ");
  }


  protected function pushTelephones() {
    $this->_log->debug(__CLASS__ . ' ' . __FUNCTION__ . " start ");
    $this->_telephone->voice = $this->push_set_or_delete_value($this->_local_entity["phone"]);
    $this->_telephone->home->mobile = $this->push_set_or_delete_value($this->_local_entity["mobile_phone"]);
    $this->_telephone->fax = $this->push_set_or_delete_value($this->_local_entity["fax"]);
    $this->_log->debug(__CLASS__ . ' ' . __FUNCTION__ . " end ");
  }

  protected function pullTelephones() {
    $this->_log->debug(__CLASS__ . ' ' . __FUNCTION__ . " start ");
    $this->_local_entity["phone"] = $this->pull_set_or_delete_value($this->_telephone->voice);
    $this->_local_entity["mobile_phone"] = $this->pull_set_or_delete_value($this->_telephone->home->mobile);
    $this->_local_entity["fax"] = $this->pull_set_or_delete_value($this->_telephone->fax);
    $this->_log->debug(__CLASS__ . ' ' . __FUNCTION__ . " end ");
  }

  protected function pushWebsites() {
  	// DO NOTHING
  }

  protected function pullWebsites() {
  	// DO NOTHING
  }

  protected function pushEntity() {
    // DO NOTHING
  }

  protected function pullEntity() {
    // DO NOTHING
  }

  protected function saveLocalEntity($push_to_maestrano, $status) {
    $this->_log->debug("_local_entity=" . json_encode($this->_local_entity));
    if ($status == constant('MnoSoaBaseEntity::STATUS_NEW_ID')) {
      insertCustomerByObject($this->_local_entity, $push_to_maestrano);
    } else if ($status == constant('MnoSoaBaseEntity::STATUS_EXISTING_ID')) {
      updateCustomerByObject($this->getLocalEntityIdentifier(), $this->_local_entity, $push_to_maestrano);
    }
  }

  public function getLocalEntityIdentifier() {
    return $this->_local_entity["id"];
  }
}

?>