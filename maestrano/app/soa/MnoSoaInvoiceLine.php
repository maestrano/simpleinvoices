<?php

/**
 * Mno InvoiceLine Class
 */
class MnoSoaInvoiceLine extends MnoSoaBaseInvoiceLine
{
  protected $_local_entity_name = "INVOICE_LINE";

  public function saveLocalEntity($invoice_local_id, $invoice_lines, $push_to_maestrano) {
$this->_log->debug("SAVE INVOICE LINES: " . $invoice_local_id);
    if(!empty($invoice_lines)) {
      foreach($invoice_lines as $line_id => $line) {
        $local_line_id = $this->getLocalIdByMnoIdName($line_id, "INVOICE_LINE");
        if($this->isDeletedIdentifier($local_line_id)) {
          continue;
        }

        // Map item
        if(!empty($line->item)) {
          $local_item_id = $this->getLocalIdByMnoIdName($line->item->id, "ITEMS");
          $invoice_line->fk_product = $local_item_id->_id;
        }

        if(!$this->isValidIdentifier($local_line_id)) {
$this->_log->debug("INSERT INVOICE LINE: " . json_encode($invoice_line));
          $local_id = insertInvoiceItem($invoice_local_id, $line->quantity, $local_item_id->_id, 0, $line_item_tax_id, "", $invoice_line->subprice, $push_to_maestrano);
          if ($local_id > 0) {
            $this->addIdMapEntry($local_id, $line_id);
          }
        } else {
$this->_log->debug("UPDATE INVOICE LINE: " . json_encode($invoice_line));
          updateInvoiceItem($local_line_id->_id, $line->quantity, $local_item_id->_id, 0, $line_item_tax_id, "", $invoice_line->subprice, $push_to_maestrano);
        }
      }
    }
  }
}

?>