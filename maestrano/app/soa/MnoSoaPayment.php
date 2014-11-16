<?php

/**
 * Mno Payment Class
 */
class MnoSoaPayment extends MnoSoaBasePayment
{
    protected $_local_entity_name = "PAYMENTS";
    
    protected function pushPayment() {
      $this->_log->debug(__FUNCTION__ . " start");

      $id = $this->getLocalEntityIdentifier();
      if (empty($id)) { return; }

      $this->_log->debug(__FUNCTION__ . " end");
    }
    
    protected function pullPayment() {
      $this->_log->debug(__FUNCTION__ . " start");
      
      $return_status = null;
      if (empty($this->_id)) { return constant('MnoSoaBaseEntity::STATUS_ERROR'); }

      // Map each linked invoice of each payment line to a local payment
      foreach($this->_payment_lines as $line_id => $line) {
        $payment_line_amount = floatval($line->amount);
        foreach($line->linkedTransactions as $invoice_id => $linked_transaction) {
          $local_id = $this->getLocalIdByMnoIdName($line_id, $this->_mno_entity_name);

          // Do not update payments, only create new ones
          if ($this->isValidIdentifier(($local_id))) {
            continue;
          }

          $payment = new payment();

          // Map invoice
          $invoice_local_id = $this->getLocalIdByMnoIdName($invoice_id, "INVOICES");
          if ($this->isValidIdentifier($invoice_local_id)) {
            $payment->ac_inv_id = $invoice_local_id->_id;
          } else {
            // Fetch remote invoice if missing
            $notification->entity = "invoices";
            $notification->id = $invoice_id;
            $mno_invoice = new MnoSoaInvoice($this->_db, $this->_log);   
            $status = $mno_invoice->receiveNotification($notification);
            if ($status) {
              $payment->ac_inv_id = $invoice_local_id->_id;
            }
          }

          // Local invoice total amount is not rounded, so Payment total has to match this amount
          $invoice_total_amount = getInvoiceTotal($invoice_local_id->_id);
          if(round($invoice_total_amount, 1) == round($payment_line_amount, 1)) {
            $payment->ac_amount = $invoice_total_amount;
          } else {
            $payment->ac_amount = $payment_line_amount;
          }
          
          
          if(isset($this->_private_note)) {
            $payment->ac_notes = $this->_private_note;
          } else if(isset($this->_public_note)) {
            $payment->ac_notes = $this->_public_note;
          } else if(isset($this->_payment_reference)) {
            $payment->ac_notes = $this->_payment_reference;
          } else {
            $payment->ac_notes = '';
          }

          $payment->ac_date = date('Y-m-d', $this->_transaction_date);
          
          // TODO: Map payment types
          $payment->ac_payment_type = 2;

          $payment->insert();

          $local_payment_id = $this->_db->lastInsertID();
          $this->addIdMapEntry($local_payment_id, $line_id);
        }
      }

      $this->_log->debug(__FUNCTION__ . " end");

      return $return_status;
    }
    
    protected function saveLocalEntity($push_to_maestrano, $status) {
      $this->_log->debug("saving _local_entity=" . json_encode($this->_local_entity));
      if ($status == constant('MnoSoaBaseEntity::STATUS_NEW_ID')) {
        $payment->insert();
      }
    }
    
    public function getLocalEntityIdentifier() {
        return $this->_local_entity['id'];
    }
}

?>