START TRANSACTION;

ALTER TABLE `suppliers` 
ADD `street` VARCHAR(255) NULL AFTER `company_name`,
ADD `state` VARCHAR(255) NULL AFTER `city`,
ADD `zip` VARCHAR(100) NULL AFTER `state`;

INSERT INTO `plugin_base_fields` VALUES (NULL, 'notifications_ARRAY_admin_email_bookingaccept', 'arrays', 'notifications_ARRAY_admin_email_bookingaccept', 'script', '2022-02-14 10:39:13');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Booking Accept by Supplier email', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'notifications_titles_ARRAY_admin_email_bookingaccept', 'arrays', 'notifications_titles_ARRAY_admin_email_bookingaccept', 'script', '2022-02-14 10:54:07');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Booking Accept email sent to Admin', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'notifications_subtitles_ARRAY_admin_email_bookingaccept', 'arrays', 'notifications_subtitles_ARRAY_admin_email_bookingaccept', 'script', '2022-02-14 10:55:16');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'This message is sent to Admin when supplier Accept Booking.', 'script');

INSERT INTO `notifications` VALUES (NULL, 'admin', 'email', 'bookingaccept', 1);

ALTER TABLE `auctions` 
ADD `accepted_on` DATETIME NULL AFTER `status`;

INSERT INTO `plugin_base_fields` VALUES (NULL, 'notifications_ARRAY_suppliers_email_activeaccount', 'arrays', 'notifications_ARRAY_suppliers_email_activeaccount', 'script', '2022-02-14 10:39:13');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Account Active done by Admin', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'notifications_titles_ARRAY_suppliers_email_activeaccount', 'arrays', 'notifications_titles_ARRAY_suppliers_email_activeaccount', 'script', '2022-02-14 10:54:07');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Account Active email sent to suppliers', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'notifications_subtitles_ARRAY_suppliers_email_activeaccount', 'arrays', 'notifications_subtitles_ARRAY_suppliers_email_activeaccount', 'script', '2022-02-14 10:55:16');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'This message is sent to suppliers when Account active is done by Admin.', 'script');

INSERT INTO `notifications` VALUES (NULL, 'suppliers', 'email', 'activeaccount', 1);

COMMIT;