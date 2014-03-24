<?php

/**
 * Mno Organization Class
 */
class MnoSoaPerson extends MnoSoaBasePerson
{
    protected $_local_entity_name = "customer";
    
    protected function pushId() {
        $this->_log->debug(__CLASS__ . ' ' . __FUNCTION__ . " start");
	$id = $this->getLocalEntityIdentifier();
	
	if (!empty($id)) {
	    error_log("id is not empty, id = " . $id);
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
    
    protected function pushName() {
        $this->_log->debug(__CLASS__ . ' ' . __FUNCTION__ . " start ");
        $this->_name->familyName = $this->push_set_or_delete_value($this->_local_entity["name"]);
        $this->_log->debug(__CLASS__ . ' ' . __FUNCTION__ . " end ");
    }
    
    
    
    protected function pullName() {
        $this->_log->debug(__CLASS__ . ' ' . __FUNCTION__ . " start ");
        $this->_local_entity["name"] = $this->pull_set_or_delete_value($this->_name->familyName);
        $this->_log->debug(__CLASS__ . ' ' . __FUNCTION__ . " end ");
    }
    
    protected function pushBirthDate() {
        // DO NOTHING
    }
    
    protected function pullBirthDate() {
        // DO NOTHING
    }
    
    protected function pushGender() {
	// DO NOTHING
    }
    
    protected function pullGender() {
	// DO NOTHING
    }
    
    protected function pushAddresses() {
        $this->_log->debug(__CLASS__ . ' ' . __FUNCTION__ . " start " . $this->_local_entity["street_address"]);
        // ADDRESS -> POSTAL ADDRESS
        error_log("1");
        $address = $this->_local_entity["street_address"] . " " . $this->_local_entity["street_address2"];
        $this->_address->work->postalAddress->streetAddress = $this->push_set_or_delete_value($address);
        error_log("2");
        $this->_address->work->postalAddress->locality = $this->push_set_or_delete_value($this->_local_entity["city"]);
        error_log("3");
        $this->_address->work->postalAddress->region = $this->push_set_or_delete_value($this->_local_entity["state"]);
        error_log("4");
        $this->_address->work->postalAddress->postalCode = $this->push_set_or_delete_value($this->_local_entity["zip_code"]);
        error_log("5");
        $this->_log->debug(__CLASS__ . ' ' . __FUNCTION__ . " end ");
    }
    
    protected function pullAddresses() {
        $this->_log->debug(__CLASS__ . ' ' . __FUNCTION__ . " start ");
        // POSTAL ADDRESS -> PRIMARY ADDRESS
        $street_address = $this->pull_set_or_delete_value($this->_address->work->postalAddress->streetAddress);
        if ($street_address > 255) {
            $ww = wordwrap($street_address, 255, "\n", true);
            $pieces = explode(" ", $ww);
            $this->_local_entity["street_address"] = $pieces[0];
            $this->_local_entity["street_address2"] = $pieces[1];
        } else {
            $this->_local_entity["street_address"] = $street_address;
        }
        $this->_local_entity["city"] = $this->pull_set_or_delete_value($this->_address->work->postalAddress->locality);
        $this->_local_entity["state"] = $this->pull_set_or_delete_value($this->_address->work->postalAddress->region);
        $this->_local_entity["zip_code"] = $this->pull_set_or_delete_value($this->_address->work->postalAddress->postalCode);
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
        $this->_telephone->work->voice = $this->push_set_or_delete_value($this->_local_entity["phone"]);
        $this->_telephone->home->mobile = $this->push_set_or_delete_value($this->_local_entity["mobile_phone"]);
        $this->_telephone->work->fax = $this->push_set_or_delete_value($this->_local_entity["fax"]);
        $this->_log->debug(__CLASS__ . ' ' . __FUNCTION__ . " end ");
    }
    
    protected function pullTelephones() {
        $this->_log->debug(__CLASS__ . ' ' . __FUNCTION__ . " start ");
        $this->_local_entity["phone"] = $this->pull_set_or_delete_value($this->_telephone->work->voice);
        $this->_local_entity["mobile_phone"] = $this->pull_set_or_delete_value($this->_telephone->home->mobile);
        $this->_local_entity["fax"] = $this->pull_set_or_delete_value($this->_telephone->work->fax);
        $this->_log->debug(__CLASS__ . ' ' . __FUNCTION__ . " end ");
    }
    
    protected function pushWebsites() {
	// DO NOTHING
    }
    
    protected function pullWebsites() {
	// DO NOTHING
    }
    
    protected function pushEntity() {
	$this->_log->debug(__CLASS__ . ' ' . __FUNCTION__ . " start ");
        $this->_entity->customer = true;
        $this->_log->debug(__CLASS__ . ' ' . __FUNCTION__ . " end ");
    }
    
    protected function pullEntity() {
	// DO NOTHING
    }
    
    protected function pushCreditCard() {
        // DO NOTHING
        /*
        global $config;
        
        $this->_log->debug(__CLASS__ . ' ' . __FUNCTION__ . " start ");
        $this->_credit_card->holder_name = $this->push_set_or_delete_value($this->_local_entity["credit_card_holder_name"]);
        
        
         if (empty($this->_local_entity["credit_card_number"])) {
            $this->_credit_card->number = "";
        } else {
            $credit_card_number = $this->encrypt($this->_local_entity["credit_card_number"]);
            $this->_credit_card->number = $this->push_set_or_delete_value($credit_card_number);
        }
         
        
        $this->_credit_card->expiry_month = $this->push_set_or_delete_value($this->_local_entity["credit_card_expiry_month"]);
        $this->_credit_card->expiry_year = $this->push_set_or_delete_value($this->_local_entity["credit_card_expiry_year"]);
        $this->_log->debug(__CLASS__ . ' ' . __FUNCTION__ . " end ");
         */
    }
    
    protected function pullCreditCard() {
        // DO NOTHING
        /*
        global $config;
        
        $this->_log->debug(__CLASS__ . ' ' . __FUNCTION__ . " start ");
        $this->_local_entity["credit_card_holder_name"] = $this->pull_set_or_delete_value($this->_credit_card->holder_name);
        
        if (empty($this->_credit_card->number)) {
            $this->_local_entity["credit_card_number"] = "";
        } else {
            $enc = new encryption();
            $key = $config->encryption->default->key;	
            $credit_card_number = $this->decrypt($this->_credit_card->number);
            $this->_local_entity["credit_card_number"] = $this->pull_set_or_delete_value($credit_card_number);
        }
        
        $this->_local_entity["credit_card_expiry_month"] = $this->pull_set_or_delete_value($this->_credit_card->expiry_month);
        $this->_local_entity["credit_card_expiry_year"] = $this->pull_set_or_delete_value($this->_credit_card->expiry_year);
        $this->_log->debug(__CLASS__ . ' ' . __FUNCTION__ . " end ");
        */
    }
    
    protected function pushRole() {
        // DO NOTHING
    }
    
    protected function pullRole() {
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
    
    protected function getLocalEntityIdentifier() {
        return $this->_local_entity["id"];
    }
}

?>