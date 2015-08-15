<?php

/**
 * Mno InvoiceLine Class
 */
class MnoSoaInvoiceLine extends MnoSoaBaseInvoiceLine
{
  protected $_local_entity_name = "INVOICE_LINE";

  public function saveLocalEntity($invoice_local_id, $invoice_mno_id, $invoice_lines, $push_to_maestrano) {
    if(!empty($invoice_lines)) {
      $processed_lines_local_ids = array();
      foreach($invoice_lines as $line_id => $line) {
        $invoice_line_mno_id = $invoice_mno_id . "#" . $line_id;
        $local_line_id = $this->getLocalIdByMnoIdName($invoice_line_mno_id, "INVOICE_LINE");
        $this->_log->debug(__FUNCTION__ . " saving invoice line " . json_encode($line) . " with local id " . json_encode($local_line_id));
        if($this->isDeletedIdentifier($local_line_id)) {
          continue;
        }

        // Map item
        if(!empty($line->item)) {
          $local_item_id = $this->getLocalIdByMnoIdName($line->item->id, "ITEMS");
        }
        $line_item_tax_id = $this->applicableTaxes($line);
        $this->_log->debug(__FUNCTION__ . " invoice line applicable taxes: " . json_encode($line_item_tax_id));
        
        // Compute unit price including discounts
        // Sometimes Unit Price is rounded, so use Total Price / Quantity to be more accurate
        $unit_price = $line->totalPrice->netAmount / $line->quantity;

        if(!$this->isValidIdentifier($local_line_id)) {
          $local_id = insertInvoiceItem($invoice_local_id, $line->quantity, $local_item_id->_id, 0, $line_item_tax_id, $line->description, $unit_price, $push_to_maestrano);
          if ($local_id > 0) {
            $this->addIdMapEntry($local_id, $invoice_line_mno_id);
          }
        } else {
          $local_id = $local_line_id->_id;
          updateInvoiceItem($local_id, $line->quantity, $local_item_id->_id, 0, $line_item_tax_id, $line->description, $unit_price, $push_to_maestrano);
        }

        // Keep track of received line IDs to remove missing ones
        array_push($processed_lines_local_ids, $local_id);
      }

      // Delete local invoice lines that have been removed
      $local_invoice_lines = invoice::getInvoiceItems($invoice_local_id);
      foreach ($local_invoice_lines as $local_invoice_line) {
        if(!in_array($local_invoice_line['id'], $processed_lines_local_ids)) {
          $this->_log->debug(__FUNCTION__ . " invoice line " . $local_invoice_line['id'] . " marked for deletion");
          delete('invoice_items','id',$local_invoice_line['id']);
          $this->_mno_soa_db_interface->hardDeleteIdMapEntry($local_invoice_line['id'], "INVOICE_LINE");
        }
      }
    }
  }

  protected function applicableTaxes($line) {
    $taxes = array();
    if(isset($line->taxCode)) {
      $local_tax_id = $this->getLocalIdByMnoIdName($line->taxCode->id, "tax_codes");
      $this->_log->debug(__FUNCTION__ . " item tax local_id = " . json_encode($local_tax_id));
      $taxes[$local_tax_id->_id] = $local_tax_id->_id;
    }
    return $taxes;
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
