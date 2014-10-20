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
    $this->_local_entity->address = $this->_address;
    $this->_local_entity->postcode = $this->_postcode;
    $this->_local_entity->state = $this->_state;
    $this->_local_entity->city = $this->_city;
    $this->_local_entity->country = $this->_country;
    $this->_local_entity->website = $this->_website;
    $this->_local_entity->phone = $this->_phone;

    $this->_log->debug(__FUNCTION__ . " end " . $this->_id);
  }

  protected function saveLocalEntity($push_to_maestrano) {
    $this->_log->debug(__FUNCTION__ . " start " . json_encode($this->_local_entity));

    // Save logo
    $this->saveLogo();

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

      // Update billers to use logo
      $sql = "UPDATE ".TB_PREFIX."biller SET logo = :logo";
      dbQuery($sql,':logo', $filename);
    }
  }

}

?>
