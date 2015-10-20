<?php

require_once 'BaseMapper.php';
require_once 'MnoIdMap.php';

/**
* Map Connec Organization representation to/from OrangeHRM Customer
*/
class PersonMapper extends BaseMapper {

  public function __construct() {
    parent::__construct();

    $this->connec_entity_name = 'Person';
    $this->local_entity_name = 'customer';
    $this->connec_resource_name = 'people';
    $this->connec_resource_endpoint = 'people';
  }

  // Return the Customer local id
  protected function getId($model) {
    return $model->id;
  }

  // Initialize a new model and assign id
  public function loadModelById($local_id) {
    $model = (object) getCustomer($local_id);
    return $model;
  }

  // Overwrite me!
  // Optional: define how to initialize a new model in SimpleInvoices
  protected function initializeNewModel() {
    return (object) array();
  }

  // Map the Connec resource attributes onto the OrangeHRM Customer
  protected function mapConnecResourceToModel($cnc_hash, $model) {
    // Map hash attributes to Customer
    $model->name = implode(" ",array_filter(array($cnc_hash['first_name'],$cnc_hash['last_name'])));

    // Address
    if(!is_null($cnc_hash['address_work']) && !is_null($cnc_hash['address']['billing'])) {
      if(!is_null($cnc_hash['address_work']['billing']['line1'])) { $model->street_address = $cnc_hash['address']['billing']['line1']; }
      if(!is_null($cnc_hash['address_work']['billing']['line2'])) { $model->street_address2 = $cnc_hash['address']['billing']['line2']; }
      if(!is_null($cnc_hash['address_work']['billing']['city'])) { $model->city = $cnc_hash['address']['billing']['city']; }
      if(!is_null($cnc_hash['address_work']['billing']['postal_code'])) { $model->zip_code = $cnc_hash['address']['billing']['postal_code']; }
      if(!is_null($cnc_hash['address_work']['billing']['country'])) { $model->country = $cnc_hash['address']['billing']['country']; }
      if(!is_null($cnc_hash['address_work']['billing']['region'])) { $model->state = $cnc_hash['address']['billing']['region']; }
    }

    // Phone
    if(!is_null($cnc_hash['phone_work'])) {
      if(!is_null($cnc_hash['phone_work']['landline'])) { $model->phone = $cnc_hash['phone']['landline']; }
      if(!is_null($cnc_hash['phone_work']['fax'])) { $model->fax = $cnc_hash['phone']['fax']; }
      if(!is_null($cnc_hash['phone_work']['mobile'])) { $model->mobile_phone = $cnc_hash['phone']['mobile']; }
    }

    // Email
    if(!is_null($cnc_hash['email'])) {
      if(!is_null($cnc_hash['email']['address'])) { $model->email = $cnc_hash['email']['address']; }
    }

    // Website
    if(!is_null($cnc_hash['website'])) {
      if(!is_null($cnc_hash['website']['url'])) { $model->website = $cnc_hash['website']['url']; }
    }
  }

  // Map the OrangeHRM Customer to a Connec resource hash
  protected function mapModelToConnecResource($model) {
    $cnc_hash = array();

    // Map Customer to Connec hash
    // Explode name into first and last name
    if(!is_null($model->name)) {
      $names = explode(' ', $model->name);
      $cnc_hash['last_name'] = array_pop($names);
      $cnc_hash['first_name'] = implode(" ",$names);
    }

    // Address
    if(!is_null($model->street_address)) { $cnc_hash['address_work']['billing']['line1'] = $model->street_address; }
    if(!is_null($model->street_address2)) { $cnc_hash['address_work']['billing']['line2'] = $model->street_address2; }
    if(!is_null($model->city)) { $cnc_hash['address_work']['billing']['city'] = $model->city; }
    if(!is_null($model->zip_code)) { $cnc_hash['address_work']['billing']['postal_code'] = $model->zip_code; }
    if(!is_null($model->country)) { $cnc_hash['address_work']['billing']['country'] = $model->country; }
    if(!is_null($model->state)) { $cnc_hash['address_work']['billing']['region'] = $model->state; }

    // Phone
    if(!is_null($model->phone)) { $cnc_hash['phone']['landline'] = $model->phone; }
    if(!is_null($model->fax)) { $cnc_hash['phone']['fax'] = $model->fax; }
    if(!is_null($model->mobile_phone)) { $cnc_hash['phone']['mobile'] = $model->mobile_phone; }

    // Email
    if(!is_null($model->email)) { $cnc_hash['email']['address'] = $model->email; }

    // Website
    if(!is_null($model->website)) { $cnc_hash['website']['url'] = $model->website; }

    // Flag as customer
    $cnc_hash['is_customer'] = true;

    return $cnc_hash;
  }

  // Persist the OrangeHRM Customer
  protected function persistLocalModel($model, $cnc_hash) {
    $hash = json_decode(json_encode($model), true);
    if ($this->getId($model)) {
      updateCustomerByObject($model->id, $hash, false);
    } else {
      $hash["enabled"] = 1;
      $hash["type"] = "person";
      insertCustomerByObject($hash, false);
      $model->id = $hash['id'];
    }
  }
}
