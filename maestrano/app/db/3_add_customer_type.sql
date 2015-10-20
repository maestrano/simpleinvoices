ALTER TABLE  `si_customers` ADD  `type` VARCHAR( 255 ) NULL DEFAULT NULL ;
UPDATE `si_customers` SET type="person" WHERE id IN (SELECT app_entity_id FROM mno_id_map WHERE app_entity_name = "CUSTOMER");
UPDATE `si_customers` SET type="organization" WHERE id IN (SELECT app_entity_id FROM mno_id_map WHERE app_entity_name = "ORG_CUSTOMER");
