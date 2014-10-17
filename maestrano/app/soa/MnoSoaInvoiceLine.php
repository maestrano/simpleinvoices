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

        $line_item_tax_id = array("0" => "1");
        if(!$this->isValidIdentifier($local_line_id)) {
          $local_id = insertInvoiceItem($invoice_local_id, $line->quantity, $local_item_id->_id, 0, $line_item_tax_id, "", $line->unitPrice->price, $push_to_maestrano);
          if ($local_id > 0) {
            $this->addIdMapEntry($local_id, $line_id);
          }
        } else {
          updateInvoiceItem($local_line_id->_id, $line->quantity, $local_item_id->_id, 0, $line_item_tax_id, "", $line->unitPrice->price, $push_to_maestrano);
        }
      }
    }
  }
}

?>
