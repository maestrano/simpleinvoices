<?php

/**
 * Mno Item Interface
 */
class MnoSoaBaseItem extends MnoSoaBaseEntity
{
    protected $_mno_entity_name = "items";
    protected $_create_rest_entity_name = "items";
    protected $_create_http_operation = "POST";
    protected $_update_rest_entity_name = "items";
    protected $_update_http_operation = "POST";
    protected $_receive_rest_entity_name = "items";
    protected $_receive_http_operation = "GET";
    protected $_delete_rest_entity_name = "items";
    protected $_delete_http_operation = "DELETE";
    
    protected $_id;
    protected $_code;
    protected $_name;
    protected $_description;
    protected $_type;
    protected $_unit;
    protected $_status;
    protected $_parent;
    protected $_sale;
    protected $_purchase;
    protected $_taxes;

    protected function pushItem() {
      throw new Exception('Function '. __FUNCTION__ . ' must be overriden in MnoItem class!');
    }
    
    protected function pullItem() {
      throw new Exception('Function '. __FUNCTION__ . ' must be overriden in MnoItem class!');
    }
    
    protected function pushParent() {
      throw new Exception('Function '. __FUNCTION__ . ' must be overriden in MnoItem class!');
    }
    
    protected function pullParent() {
      throw new Exception('Function '. __FUNCTION__ . ' must be overriden in MnoItem class!');
    }
    
    protected function pushSale() {
      throw new Exception('Function '. __FUNCTION__ . ' must be overriden in MnoItem class!');
    }
    
    protected function pullSale() {
      throw new Exception('Function '. __FUNCTION__ . ' must be overriden in MnoItem class!');
    }
    
    protected function pushPurchase() {
      throw new Exception('Function '. __FUNCTION__ . ' must be overriden in MnoItem class!');
    }
    
    protected function pullPurchase() {
      throw new Exception('Function '. __FUNCTION__ . ' must be overriden in MnoItem class!');
    }
    
    protected function saveLocalEntity($push_to_maestrano, $status) {
      throw new Exception('Function '. __FUNCTION__ . ' must be overriden in MnoItem class!');
    }
    
    public function getLocalEntityIdentifier() {
      throw new Exception('Function '. __FUNCTION__ . ' must be overriden in MnoItem class!');
    }
    
    /**
    * Build a Maestrano organization message
    * 
    * @return Organization the organization json object
    */
    protected function build() {
        $this->_log->debug("start");
        $this->pushItem();
        $this->_log->debug("after item");
        $this->pushParent();
        $this->_log->debug("after parent");
        $this->pushSale();
        $this->_log->debug("after sale");
        $this->pushPurchase();
        $this->_log->debug("after purchase");
        
        if ($this->_code != null) { $msg['item']->code = $this->_code; }
        if ($this->_name != null) { $msg['item']->name = $this->_name; }
        if ($this->_description != null) { $msg['item']->description = $this->_description; }
        if ($this->_type != null) { $msg['item']->type = $this->_type; }
        if ($this->_unit != null) { $msg['item']->unit = $this->_unit; }
        if ($this->_status != null) { $msg['item']->status = $this->_status; }
        if ($this->_parent != null) { $msg['item']->parent = $this->_parent; }
        if ($this->_sale != null) { $msg['item']->sale = $this->_sale; }
        if ($this->_purchase != null) { $msg['item']->purchase = $this->_purchase; }
        if ($this->_taxes != null) { $msg['item']->taxes = $this->_taxes; }
	
        $this->_log->debug("after creating message array");
        $result = json_encode($msg['item']);

        $this->_log->debug("result = " . $result);

        return json_encode($msg['item']);
    }
    
    protected function persist($mno_entity) {
        $this->_log->debug("start");
        
        if (!empty($mno_entity->item)) {
            $mno_entity = $mno_entity->item;
        }
        
        if (!empty($mno_entity->id)) {
            $this->_id = $mno_entity->id;
            $this->set_if_array_key_has_value($this->_code, 'code', $mno_entity);
            $this->set_if_array_key_has_value($this->_name, 'name', $mno_entity);
            $this->set_if_array_key_has_value($this->_description, 'description', $mno_entity);
            $this->set_if_array_key_has_value($this->_type, 'type', $mno_entity);
            $this->set_if_array_key_has_value($this->_unit, 'unit', $mno_entity);
            $this->set_if_array_key_has_value($this->_status, 'status', $mno_entity);
            $this->set_if_array_key_has_value($this->_parent, 'parent', $mno_entity->parent);
            
            if (!empty($mno_entity->sale)) {
                $this->set_if_array_key_has_value($this->_sale, 'sale', $mno_entity);
            }
            if (!empty($mno_entity->purchase)) {
                $this->set_if_array_key_has_value($this->_purchase, 'purchase', $mno_entity);
            }
            if (!empty($mno_entity->taxes)) {
                $this->set_if_array_key_has_value($this->_taxes, 'taxes', $mno_entity);
            }

            $this->_log->debug("id = " . $this->_id);

            $this->_log->debug("start pull functions");
            $status = $this->pullItem();
            
            if ($status == constant('MnoSoaBaseEntity::STATUS_NEW_ID') || $status == constant('MnoSoaBaseEntity::STATUS_EXISTING_ID')) {
                $this->_log->debug("after item");
                $this->pullParent();
                $this->_log->debug("after parent");
                $this->pullSale();
                $this->_log->debug("after sale");
                $this->pullPurchase();
                $this->_log->debug("after purchase");

                $this->saveLocalEntity(false, $status);

                $local_entity_id = $this->getLocalEntityIdentifier();
                $mno_entity_id = $this->_id;

                if ($status == constant('MnoSoaBaseEntity::STATUS_NEW_ID') && !empty($local_entity_id) && !empty($mno_entity_id)) {
                    $this->addIdMapEntry($local_entity_id, $mno_entity_id);
                }
            }
        }
        $this->_log->debug("end");
    }
}

?>