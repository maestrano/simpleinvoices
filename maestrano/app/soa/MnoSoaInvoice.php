<?php

/**
 * Mno Invoice Class
 */
class MnoSoaInvoice extends MnoSoaBaseInvoice
{
    protected $_local_entity_name = "INVOICES";

    public $_is_new;
    
    protected function pushInvoice()
    {
        $id = $this->getLocalEntityIdentifier();
$this->_log->debug("PUSH INVOICE: " . $id);

        if (empty($id)) { return; }
        $mno_id = $this->getMnoIdByLocalIdName($id, $this->_local_entity_name);
        $this->_id = ($this->isValidIdentifier($mno_id)) ? $mno_id->_id : null;
        $this->_is_new = (empty($this->_id)) ? true : false;

        $this->_transaction_date = strtotime($this->_local_entity['date']);
        $this->_amount = getInvoiceTotal($id);

        // Pull Person ID
        $mno_id = $this->getMnoIdByLocalIdName($this->_local_entity['customer_id'], "CUSTOMER");
$this->_log->debug("FETCH PERSON: " . json_encode($mno_id));
        $this->_person_id = $mno_id->_id;

        // Pull Invoice lines
        $invoiceItems = invoice::getInvoiceItems($id);
$this->_log->debug("FETCH INVOICES: " . json_encode($invoiceItems));
        if(!empty($invoiceItems)) {
          foreach($invoiceItems as $invoiceItem) {
            $invoice_line = array();
            
            // Find mno id if already exists
            $mno_entity = $this->getMnoIdByLocalIdName($invoiceItem['id'], "INVOICE_LINE");
            if($this->isDeletedIdentifier($mno_entity)) {
              $this->markInvoiceLineForDeletion($mno_entity->_id);
              continue;
            } else if (!$this->isValidIdentifier($mno_entity)) {
              // Generate and save ID
              $invoice_line_mno_id = uniqid();
              $this->_mno_soa_db_interface->addIdMapEntry($invoiceItem['id'], "INVOICE_LINE", $invoice_line_mno_id, "INVOICE_LINE");
            } else {
              $invoice_line_mno_id = $mno_entity->_id;
            }

            // Pull Product
            $local_product_id = $this->push_set_or_delete_value($invoiceItem['product_id']);
            $mno_id = $this->getMnoIdByLocalIdName($local_product_id, "ITEMS");
            $invoice_line['item']->id = $mno_id->_id;

            // Pull attributes
            $invoice_line['id'] = $invoice_line_mno_id;
            $invoice_line['amount'] = $invoiceItem['total'];
            $invoice_line['quantity'] = $invoiceItem['quantity'];
            $invoice_line['unitPrice'] = $invoiceItem['unit_price'];
            $invoice_line['status'] = 'ACTIVE';

            $this->_invoice_lines[$invoice_line_mno_id] = $invoice_line;
          }
        }
    }
    
    protected function pullInvoice()
    {
$this->_log->debug("PULL INVOICE " . $this->_id);
        $return_status = null;
        if (empty($this->_id)) { return constant('MnoSoaBaseEntity::STATUS_ERROR'); }
        
        $local_id = $this->getLocalIdByMnoIdName($this->_id, $this->_mno_entity_name);
$this->_log->debug("LOCAL INVOICE ID " . json_encode($local_id));
        $active = ($this->_status == 'INACTIVE') ? 0 : 1;

        // Skip deleted Items
        if ($active == 0 || $this->isDeletedIdentifier($local_id)) { return constant('MnoSoaBaseEntity::STATUS_DELETED_ID'); }

        if ($this->isValidIdentifier(($local_id))) {
$this->_log->debug("UPDATE INVOICE ");
          // Update Invoice
          $return_status = constant('MnoSoaBaseEntity::STATUS_EXISTING_ID'); 
          $this->_local_entity['id'] = $local_id->_id;
        } else {
$this->_log->debug("CREATE INVOICE ");
          // Create new Invoice
          $return_status = constant('MnoSoaBaseEntity::STATUS_NEW_ID');
        }
$this->_log->debug("PROCESSING INVOICE " . $this->_id);
        $this->_local_entity['date'] = date('Y-m-d', $this->_transaction_date);
        $this->_local_entity['preference_id'] = 1;
        $this->_local_entity['biller_id'] = 1;


        // Map local customer
$this->_log->debug("FETCHING PERSON " . $this->_person_id);
        if($this->_person_id) {
          $local_person_id = $this->getLocalIdByMnoIdName($this->_person_id, "PERSONS");
$this->_log->debug("FOUND PERSON " . $local_person_id);
          if ($this->isValidIdentifier($local_person_id)) {
            $this->_local_entity['customer_id'] = $local_person_id->_id;
          } else if ($this->isDeletedIdentifier($local_person_id)) {
            // do not update
          } else {
            // Fetch remote person if missing
            $notification->entity = "persons";
            $notification->id = $this->_person_id;
            $mno_person = new MnoSoaPerson($this->_db, $this->_log);   
            $status = $mno_person->receiveNotification($notification);
            if ($status) {
              $this->_local_entity['customer_id'] = $local_person_id->_id;
            }
          }
        } else {
          $this->_local_entity['customer_id'] = 1;
        }

        return $return_status;
    }
    
    protected function saveLocalEntity($push_to_maestrano, $status) {
      $this->_log->debug("saving _local_entity=" . json_encode($this->_local_entity));
      if ($status == constant('MnoSoaBaseEntity::STATUS_NEW_ID')) {
$this->_log->debug("INSERT NEW INVOICE");
        $invoice_local_id = insertInvoiceByObject($this->_local_entity, 2, false);
$this->_log->debug("CREATED NEW INVOICE " . $invoice_local_id);
      } else if ($status == constant('MnoSoaBaseEntity::STATUS_EXISTING_ID')) {
$this->_log->debug("UPDATE INVOICE " . $this->getLocalEntityIdentifier());
        updateInvoiceByObject($this->_local_entity, $this->getLocalEntityIdentifier(), false);
        $invoice_local_id = $this->getLocalEntityIdentifier();
      }

      $mno_invoice_line = new MnoSoaInvoiceLine($this->_db, $this->_log);
      $mno_invoice_line->saveLocalEntity($invoice_local_id, $this->_invoice_lines, $push_to_maestrano);
    }
    
    public function getLocalEntityIdentifier() {
        return $this->_local_entity['id'];
    }

    public function markInvoiceLineForDeletion($invoice_line_id) {
      $mno_invoice_line = new MnoSoaInvoiceLine($this->_db, $this->_log);
      $mno_invoice_line->sendDeleteNotification($invoice_line_id);

      $mno_entity = $this->getMnoIdByLocalIdName($invoice_line_id, "INVOICE_LINE");
      $invoice_line_mno_id = $mno_entity->_id;
      
      $invoice_line = array();
      $invoice_line['id'] = $invoice_line_mno_id;
      $invoice_line['status'] = 'INACTIVE';
      $this->_invoice_lines[$invoice_line_mno_id] = $invoice_line;
    }
}

?>