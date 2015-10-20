ALTER TABLE  `si_invoices` ADD  `currency` VARCHAR( 3 ) NULL DEFAULT NULL ;
ALTER TABLE  `si_invoice_items` ADD  `currency` VARCHAR( 3 ) NULL DEFAULT NULL ;
ALTER TABLE  `si_payment` ADD  `currency` VARCHAR( 3 ) NULL DEFAULT NULL ;
