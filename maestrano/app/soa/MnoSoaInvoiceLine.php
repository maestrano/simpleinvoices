<?php

/**
 * Mno InvoiceLine Class
 */
class MnoSoaInvoiceLine extends MnoSoaBaseInvoiceLine
{
  protected $_local_entity_name = "INVOICE_LINE";

  public function saveLocalEntity($invoice_local_id, $invoice_mno_id, $invoice_lines, $push_to_maestrano) {
    if(!empty($invoice_lines)) {
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
        if(!$this->isValidIdentifier($local_line_id)) {
          $local_id = insertInvoiceItem($invoice_local_id, $line->quantity, $local_item_id->_id, 0, $line_item_tax_id, $line->description, $line->unitPrice->netAmount, $push_to_maestrano);
          if ($local_id > 0) {
            $this->addIdMapEntry($local_id, $invoice_line_mno_id);
          }
        } else {

          updateInvoiceItem($local_line_id->_id, $line->quantity, $local_item_id->_id, 0, $line_item_tax_id, $line->description, $line->unitPrice->netAmount, $push_to_maestrano);
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
