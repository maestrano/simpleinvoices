<?php

/**
 * Mno Item Class
 */
class MnoSoaItem extends MnoSoaBaseItem
{
    protected $_local_entity_name = "ITEMS";

    public $_is_new;
    
    protected function pushItem() {
        $id = $this->getLocalEntityIdentifier();

        if (empty($id)) { return; }
        $mno_id = $this->getMnoIdByLocalIdName($id, $this->_local_entity_name);
        $this->_id = ($this->isValidIdentifier($mno_id)) ? $mno_id->_id : null;
        $this->_is_new = (empty($this->_id)) ? true : false;

        $this->_name = $this->push_set_or_delete_value($this->_local_entity['description']);
        $this->_status = $this->_local_entity['enabled'] == "1" ? "ACTIVE" : "INACTIVE";
        $this->_type = 'PRODUCT';

        $this->_sale->netAmount = $this->push_set_or_delete_value($this->_local_entity['unit_price']);
        $this->_purchase->price = $this->push_set_or_delete_value($this->_local_entity['cost']);

        // Map tax type
        $this->pushTaxes();
    }
    
    protected function pullItem() {
        $return_status = null;
        if (empty($this->_id)) { return constant('MnoSoaBaseEntity::STATUS_ERROR'); }
        
        $local_id = $this->getLocalIdByMnoIdName($this->_id, $this->_mno_entity_name);
        $active = ($this->_status == 'INACTIVE') ? 0 : 1;
        // Skip deleted Items
        if ($active == 0 || $this->isDeletedIdentifier($local_id)) { return constant('MnoSoaBaseEntity::STATUS_DELETED_ID'); }

        if ($this->isValidIdentifier(($local_id))) {
          // Update Item
          $return_status = constant('MnoSoaBaseEntity::STATUS_EXISTING_ID'); 
          $this->_local_entity['id'] = $local_id->_id;
        } else {
          // Create new Item
          $return_status = constant('MnoSoaBaseEntity::STATUS_NEW_ID');
        }
        
        $this->_local_entity['description'] = $this->_name;
        $this->_local_entity['unit_price'] = floatval($this->_sale->netAmount);
        $this->_local_entity['cost'] = floatval($this->_purchase->price);
        $this->_local_entity['enabled'] = $active;
        
        // Map tax type
        $this->pullTaxes();
        
        return $return_status;
    }
    
    protected function pushParent() {
    }
    
    protected function pullParent() {
    }
    
    protected function pushSale() {
    }
    
    protected function pullSale() {
    }
    
    protected function pushPurchase() {
    }
    
    protected function pullPurchase() {
    }
    
    protected function saveLocalEntity($push_to_maestrano, $status) {
      $this->_log->debug("saveLocalEntity _local_entity=" . json_encode($this->_local_entity));
      if ($status == constant('MnoSoaBaseEntity::STATUS_NEW_ID')) {
        insertProductByObject($this->_local_entity, 1, 1, false);
      } else if ($status == constant('MnoSoaBaseEntity::STATUS_EXISTING_ID')) {
        updateProductByObject($this->_local_entity, false);
      }
    }
    
    public function getLocalEntityIdentifier() {
        return $this->_local_entity['id'];
    }

    protected function pullTaxes() {
      if(isset($this->_sale_tax_code)) {
        $tax_number = 1;
        $this->_log->debug(__FUNCTION__ . " assign item tax_code: " . $this->_sale_tax_code->id);
        $local_id = $this->getLocalIdByMnoIdName($this->_sale_tax_code->id, "tax_codes");
        if ($this->isValidIdentifier($local_id)) {
          $this->_log->debug(__FUNCTION__ . " item tax local_id = " . json_encode($local_id));
          $local_tax = $this->findTaxById($local_id->_id);
          // Add tax type if missing
          if(isset($local_tax)) {
            if($tax_number == 1) {
              $this->_local_entity['default_tax_id'] = $local_tax['tax_id'];
              $tax_number++;
            } else {
              $this->_local_entity['default_tax_id_2'] = $local_tax['tax_id'];
            }
          }
        }
      }
    }

    protected function pushTaxes() {
      $tax_details = getTaxes();
      $taxes = array();
      foreach ($tax_details as $tax_detail) {
        if($tax_detail['tax_id'] == $this->_local_entity['default_tax_id'] || $tax_detail['tax_id'] == $this->_local_entity['default_tax_id_2']) {
          $mno_id = $this->getMnoIdByLocalIdName($tax_detail['tax_id'], 'TAX');
          if(isset($mno_id)) {
            $this->_sale_tax_code = $mno_id->_id;
          }
        }
      }
      $this->_taxes = $taxes;
    }

    private function findTaxByLabel($tax_label) {
      $tax_details = getTaxes();
      foreach ($tax_details as $tax_detail) {
        if($tax_detail['tax_description'] == $tax_label) {
          return $tax_detail;
        }
      }
      return null;
    }

    private function findTaxById($tax_id) {
      $tax_details = getTaxes();
      foreach ($tax_details as $tax_detail) {
        if($tax_detail['tax_id'] == $tax_id) {
          return $tax_detail;
        }
      }
      return null;
    }
}

?>