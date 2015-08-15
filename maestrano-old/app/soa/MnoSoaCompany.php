<?php

/**
 * Mno Company Class
 */
class MnoSoaCompany extends MnoSoaBaseCompany
{
  protected $_local_entity_name = "company";

  protected function pushCompany() {
    $this->_log->debug(__FUNCTION__ . " start");
    // Nothing to do
    $this->_log->debug(__FUNCTION__ . " end");
  }

  protected function pullCompany() {
    $this->_log->debug(__FUNCTION__ . " start " . $this->_id);

    $this->_local_entity = (object) array();
    $this->_local_entity->name = $this->_name;
    $this->_local_entity->currency = $this->_currency;
    $this->_local_entity->logo = $this->_logo;
    $this->_local_entity->email = $this->_email;
    $this->_local_entity->website = $this->_website;
    $this->_local_entity->address = $this->_address;
    $this->_local_entity->phone = $this->_phone;

    $local_id = $this->getLocalIdByMnoIdName($this->_id, $this->_mno_entity_name);
    if ($this->isValidIdentifier(($local_id))) {
      $return_status = constant('MnoSoaBaseEntity::STATUS_EXISTING_ID'); 
      $this->_local_entity->id = $local_id->_id;
    } else {
      $return_status = constant('MnoSoaBaseEntity::STATUS_NEW_ID');
    }

    $this->_log->debug(__FUNCTION__ . " end " . $this->_id);

    return $return_status;
  }

  protected function saveLocalEntity($push_to_maestrano, $status) {
    $this->_log->debug(__FUNCTION__ . " start " . json_encode($this->_local_entity));

    // Save logo first
    $this->saveLogo();

    // Save biller using company detail
    $this->_log->debug("saving _local_entity=" . json_encode($this->_local_entity));

    $_POST[name] = $this->_name;
    $_POST[street_address] = $this->_address->streetAddress;
    $_POST[street_address2] = $this->_address->streetAddress2;
    $_POST[city] = $this->_address->locality;
    $_POST[state] = $this->_address->region;
    $_POST[zip_code] = $this->_address->postalCode;
    $_POST[country] = $this->_address->country;
    $_POST[phone] = $this->_phone->voice;
    $_POST[mobile_phone] = $this->_phone->mobile;
    $_POST[fax] = $this->_phone->fax;
    $_POST[email] = $this->_email;
    $_POST[logo] = 'mycompany.jpg';
    $_POST[footer] = '';
    $_POST[paypal_business_name] = '';
    $_POST[paypal_notify_url] = '';
    $_POST[paypal_return_url] = '';
    $_POST[eway_customer_id] = '';
    $_POST[notes] = '';
    $_POST[custom_field1] = '';
    $_POST[custom_field2] = '';
    $_POST[custom_field3] = '';
    $_POST[custom_field4] = '';
    $_POST['enabled'] = 1;

    if ($status == constant('MnoSoaBaseEntity::STATUS_NEW_ID')) {
      insertBiller();
      $this->_local_entity->id = lastInsertId();
    } else if ($status == constant('MnoSoaBaseEntity::STATUS_EXISTING_ID')) {
      $local_id = $this->getLocalIdByMnoIdName($this->_id, $this->_mno_entity_name);
      $this->_local_entity->id = $local_id->_id;
      $_GET[id] = $local_id->_id;
      updateBiller();
    }

    $this->_log->debug(__FUNCTION__ . " end ");
  }

  protected function saveLogo() {
    if(isset($this->_local_entity->logo->logo)) {
      // Save logo file locally
      $filename = 'mycompany.jpg';
      $dir = "./templates/invoices/logos/";
      $tmpLogoFilePath = $dir . $filename;
      $this->_log->debug("saving company logo " . $tmpLogoFilePath . ' from ' . $this->_local_entity->logo->logo);
      file_put_contents($tmpLogoFilePath, file_get_contents($this->_local_entity->logo->logo));
    }
  }

  public function getLocalEntityIdentifier() {
    return $this->_local_entity->id;
  }

}

?>
