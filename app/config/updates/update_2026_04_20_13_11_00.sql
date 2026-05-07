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

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblRegister', 'backend', 'Label / For Register', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title',  'For Register', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'notifications_ARRAY_suppliers_email_confirmation', 'arrays', 'notifications_ARRAY_suppliers_email_confirmation', 'script', '2022-02-14 10:39:13');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'New enquiry received email', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'notifications_titles_ARRAY_suppliers_email_confirmation', 'arrays', 'notifications_titles_ARRAY_suppliers_email_confirmation', 'script', '2022-02-14 10:54:07');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'New Enquiry Received email sent to supplier', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'notifications_subtitles_ARRAY_suppliers_email_confirmation', 'arrays', 'notifications_subtitles_ARRAY_suppliers_email_confirmation', 'script', '2022-02-14 10:55:16');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'This message is sent to the supplier when a new supplier accept the Ride.', 'script');

INSERT INTO `notifications` VALUES (NULL, 'suppliers', 'email', 'confirmation', 1);

DROP TABLE IF EXISTS `supplier_documents`;
CREATE TABLE `supplier_documents` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `supplier_id` int(10) unsigned NULL,
  `file_name` VARCHAR(255) NOT NULL,
  `original_name` VARCHAR(255) NOT NULL,
  `file_type` VARCHAR(255) NOT NULL,
  `file_size` INT DEFAULT NULL,
  `thumb_path` VARCHAR(255) DEFAULT NULL,
  `source_path` VARCHAR(255) NOT NULL,
  `file_category` VARCHAR(255) DEFAULT 'general',
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  `modified` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `supplier_id` (`supplier_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblBookingStreet', 'backend', 'Label / Street', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Street', 'script');

ALTER TABLE `plugin_auth_users` 
ADD `device_token` VARCHAR(255) NULL AFTER `is_approved`,
ADD `device_type` VARCHAR(255) NULL AFTER `device_token`;

COMMIT;