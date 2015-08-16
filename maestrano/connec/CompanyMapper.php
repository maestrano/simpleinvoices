<?php

require_once 'BaseMapper.php';
require_once 'MnoIdMap.php';

/**
* Map Connec Company representation to/from OrangeHRM Organization
* Note: this mapper is configured to subscribe only. It will never push
* information back to Connec! (mapModelToConnecResource not defined)
*/
class CompanyMapper extends BaseMapper {
  public function __construct() {
    parent::__construct();

    $this->connec_entity_name = 'Company';
    $this->local_entity_name = 'company';
    $this->connec_resource_name = 'company';
    $this->connec_resource_endpoint = 'company';
  }

  // Singleton resource, id does not matter
  public function getId($model) {
    return $model->id;
  }

  // Initialize a new model and assign id
  public function loadModelById($local_id) {
    $model = $this->initializeNewModel();
    $model->id = $local_id;
    return $model;
  }

  // Overwrite me!
  // Optional: define how to initialize a new model in SimpleInvoices
  protected function initializeNewModel() {
    return (object) array();
  }

  // Map the OrangeHRM model to a Connec resource hash
  protected function mapModelToConnecResource($model) {
    // do nothing
    return null;
  }

  // Map the Connec resource attributes onto the OrangeHRM Organization
  protected function mapConnecResourceToModel($cnc_hash, $model) {
    // Map hash attributes to Organization
    if(!is_null($cnc_hash['name'])) { $model->name = $cnc_hash['name']; }

    // Address
    if(!is_null($cnc_hash['address']) && !is_null($cnc_hash['address']['billing'])) {
      if(!is_null($cnc_hash['address']['billing']['line1'])) { $model->street1 = $cnc_hash['address']['billing']['line1']; }
      if(!is_null($cnc_hash['address']['billing']['line2'])) { $model->street2 = $cnc_hash['address']['billing']['line2']; }
      if(!is_null($cnc_hash['address']['billing']['city'])) { $model->city = $cnc_hash['address']['billing']['city']; }
      if(!is_null($cnc_hash['address']['billing']['postal_code'])) { $model->zipCode = $cnc_hash['address']['billing']['postal_code']; }
      if(!is_null($cnc_hash['address']['billing']['country'])) { $model->country = $cnc_hash['address']['billing']['country']; }
      if(!is_null($cnc_hash['address']['billing']['region'])) { $model->state = $cnc_hash['address']['billing']['region']; }
    }

    // Phone
    if(!is_null($cnc_hash['phone'])) {
      if(!is_null($cnc_hash['phone']['landline'])) { $model->phone = $cnc_hash['phone']['landline']; }
      if(!is_null($cnc_hash['phone']['fax'])) { $model->fax = $cnc_hash['phone']['fax']; }
      if(!is_null($cnc_hash['phone']['mobile'])) { $model->mobile = $cnc_hash['phone']['mobile']; }
    }

    // Email
    if(!is_null($cnc_hash['email'])) {
      if(!is_null($cnc_hash['email']['address'])) { $model->email = $cnc_hash['email']['address']; }
    }

    // Website
    if(!is_null($cnc_hash['website'])) {
      if(!is_null($cnc_hash['website']['url'])) { $model->website = $cnc_hash['website']['url']; }
    }

    // Logo
    if(!is_null($cnc_hash['logo'])) {
      if(!is_null($cnc_hash['logo']['logo'])) { $model->logo = $cnc_hash['logo']['logo']; }
    }
  }

  // Persist the SimpleInvoices Company
  protected function persistLocalModel($model, $cnc_hash) {
    $this->saveLogo($model);
    $this->upsertBiller($model);
  }

  // Save the company as a biller
  protected function upsertBiller($model) {
    $_POST[name] = $model->name;
    $_POST[street_address] = $model->street1;
    $_POST[street_address2] = $model->street2;
    $_POST[city] = $model->city;
    $_POST[state] = $model->state;
    $_POST[zip_code] = $model->zipCode;
    $_POST[country] = $model->country;
    $_POST[phone] = $model->phone;
    $_POST[mobile_phone] = $model->mobile;
    $_POST[fax] = $model->fax;
    $_POST[email] = $model->email;
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

    if ($this->getId($model)) {
      $_GET[id] = $this->getId($model);
      updateBiller();
    } else {
      insertBiller();
      $model->id = lastInsertId();
    }
  }

  // Save the company logo on file system
  protected function saveLogo($model) {
    if(isset($model->logo)) {
      // Save logo file locally
      $filename = 'mycompany.jpg';
      $dir = "./templates/invoices/logos/";
      $tmpLogoFilePath = $dir . $filename;
      error_log("saveLogo entity=$this->connec_entity_name $model->logo");
      file_put_contents($tmpLogoFilePath, file_get_contents($model->logo));
    }
  }
}
