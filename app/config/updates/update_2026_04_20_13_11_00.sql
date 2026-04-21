START TRANSACTION;

INSERT INTO `plugin_base_fields` VALUES (NULL, 'dash_b2b_rides_overview', 'backend', 'Label / B2B Rides Overview', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '1', 'title', 'B2B Rides Overview', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'dash_b2b_earnings', 'backend', 'Label / B2B Earnings', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '1', 'title', 'B2B Earnings', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'supplier_dash_rides_overview', 'backend', 'Label / Rides Overview', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '1', 'title', 'Rides Overview', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'supplier_dash_trend_analysis', 'backend', 'Label / Trend Analysis', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '1', 'title', 'Trend Analysis', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'supplier_dash_earning_overview', 'backend', 'Label / Earning Overview', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '1', 'title', 'Earning Overview', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'plugin_base_lbl_vehicle_cat', 'frontend', 'Label / Vehicle Category', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Vehicle Category', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'plugin_base_lbl_select_cat', 'frontend', 'Label / Select Category', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Select Category', 'script');


COMMIT;