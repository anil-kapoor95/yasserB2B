
START TRANSACTION;

INSERT INTO `plugin_base_fields` VALUES (NULL, 'recipients_ARRAY_suppliers', 'arrays', 'recipients_ARRAY_suppliers', 'script', '2022-02-14 09:37:04');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Supplier', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'notifications_msg_to_suppliers', 'backend', 'notifications_msg_to_suppliers', 'script', '2022-02-14 09:55:23');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Messages sent to Suppliers', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'notifications_ARRAY_suppliers_email_forgot', 'arrays', 'notifications_ARRAY_suppliers_email_forgot', 'script', '2022-02-14 10:39:13');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Send forgot password email', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'notifications_titles_ARRAY_suppliers_email_forgot', 'arrays', 'notifications_titles_ARRAY_suppliers_email_forgot', 'script', '2022-02-14 10:54:07');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Send forgot password email to Supplier', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'notifications_subtitles_ARRAY_suppliers_email_forgot', 'arrays', 'notifications_subtitles_ARRAY_suppliers_email_forgot', 'script', '2022-02-14 10:55:16');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'This message is sent to supplier when he requests for password recovery.', 'script'); 

ALTER TABLE `notifications`
MODIFY `recipient` ENUM('client','admin','drivers','suppliers') DEFAULT NULL;

INSERT INTO `notifications` VALUES (NULL, 'suppliers', 'email', 'forgot', 1);
INSERT INTO `notifications` VALUES (NULL, 'admin', 'email', 'accountactive', 1);

INSERT INTO `plugin_base_fields` VALUES (NULL, 'notifications_ARRAY_admin_email_accountactive', 'arrays', 'notifications_ARRAY_admin_email_accountactive', 'script', '2022-02-14 10:39:13');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Supplier account active email', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'notifications_ARRAY_suppliers_email_account', 'arrays', 'notifications_ARRAY_suppliers_email_account', 'script', '2022-02-14 10:39:13');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'New Supplier account email', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'notifications_titles_ARRAY_suppliers_email_account', 'arrays', 'notifications_titles_ARRAY_suppliers_email_account', 'script', '2022-02-14 10:54:07');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'New account email sent to Supplier', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'notifications_subtitles_ARRAY_suppliers_email_account', 'arrays', 'notifications_subtitles_ARRAY_suppliers_email_account', 'script', '2022-02-14 10:55:16');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'This message is sent to supplier when new account is created.', 'script');

INSERT INTO `notifications` VALUES (NULL, 'suppliers', 'email', 'account', 1);


COMMIT;