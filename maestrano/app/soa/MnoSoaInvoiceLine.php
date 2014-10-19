<?php

/**
 * Mno InvoiceLine Class
 */
class MnoSoaInvoiceLine extends MnoSoaBaseInvoiceLine
{
  protected $_local_entity_name = "INVOICE_LINE";

  public function saveLocalEntity($invoice_local_id, $invoice_lines, $push_to_maestrano) {
    if(!empty($invoice_lines)) {
      foreach($invoice_lines as $line_id => $line) {
        $local_line_id = $this->getLocalIdByMnoIdName($line_id, "INVOICE_LINE");
        if($this->isDeletedIdentifier($local_line_id)) {
          continue;
        }

        // Map item
        if(!empty($line->item)) {
          $local_item_id = $this->getLocalIdByMnoIdName($line->item->id, "ITEMS");
        }
        $line_item_tax_id = $this->applicableTaxes($line);
        if(!$this->isValidIdentifier($local_line_id)) {
          $local_id = insertInvoiceItem($invoice_local_id, $line->quantity, $local_item_id->_id, 0, $line_item_tax_id, "", $line->unitPrice->netAmount, $push_to_maestrano);
          if ($local_id > 0) {
            $this->addIdMapEntry($local_id, $line_id);
          }
        } else {
          updateInvoiceItem($local_line_id->_id, $line->quantity, $local_item_id->_id, 0, $line_item_tax_id, "", $line->unitPrice->netAmount, $push_to_maestrano);
        }
      }
    }
  }

  protected function applicableTaxes($line) {
    $taxes = array();
    if(isset($line->taxes)) {
      foreach ($line->taxes as $tax_label => $mno_tax) {
        if(!isset($mno_tax->rate)) { continue; }
        $local_tax = $this->findTaxByLabel($tax_label);
        $taxes[$local_tax['tax_id']] = $local_tax['tax_id'];
      }
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
}

?>
