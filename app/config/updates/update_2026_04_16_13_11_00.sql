START TRANSACTION;

INSERT INTO `plugin_base_fields` VALUES (NULL, 'menuReport', 'backend', 'Menu / Report', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Report', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'plugin_base_lbl_available_rides', 'backend', 'lbl / Available Rides', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Available Rides', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblReport', 'backend', 'Label / Payout Report', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Payout Report', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'infoReportDesc', 'backend', 'Infobox / Report', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Below is the payout report. You can view total earnings, commissions, and paid amounts etc.', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblAllSuppliers', 'backend', 'Label / All Suppliers', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'All Suppliers', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'plugin_base_lbl_price_after_commission', 'backend', 'lbl / Price After Commission', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Price After Commission', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'plugin_base_lbl_supplier_amount', 'backend', 'lbl / Supplier 
amount', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Supplier 
amount', 'script');


COMMIT;