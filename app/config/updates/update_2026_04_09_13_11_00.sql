START TRANSACTION;

ALTER TABLE `bookings` 
ADD `commission_type` VARCHAR(255) NULL AFTER `commission`,
ADD `commission_amount` VARCHAR(255) NULL AFTER `commission_type`;

INSERT INTO `plugin_base_fields` VALUES (NULL, 'script_menu_commission', 'backend', 'Label / Commission', 'script', '2026-04-09 10:53:31');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Commission', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'infoCommissionTitle', 'backend', 'Infobox / Commission options', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Commission options', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'infoCommissionDesc', 'backend', 'Infobox / Commission options', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Set the commission percentage that will be applied to each booking.', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'opt_o_commission_type', 'backend', 'Options / Commission type', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Commission type', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'opt_o_commission_amount', 'backend', 'Options / Commission amount', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Commission amount', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'opt_o_commission_percentage', 'backend', 'Options / Commission percentage', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Commission percentage', 'script');

INSERT INTO `options` (`foreign_id`, `key`, `tab_id`, `value`, `label`, `type`, `order`, `is_visible`, `style`) VALUES
(1, 'o_commission_type', 6, 'percent|fixed::fixed', 'Percent|Fixed', 'enum', 1, 1, NULL),
(1, 'o_commission_amount', 6, '50', NULL, 'float', 2, 1, NULL);

INSERT INTO `plugin_base_fields` VALUES (NULL, 'error_titles_ARRAY_AO06', 'arrays', 'error_titles_ARRAY_AO06', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Commission options have been changed.', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, '_commission_type_ARRAY_percent', 'arrays', '_commission_type_ARRAY_percent', 'script', '2026-04-09 10:34:18');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Percent', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, '_commission_type_ARRAY_fixed', 'arrays', '_commission_type_ARRAY_fixed', 'script', '2026-04-09 10:34:34');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Fixed', 'script');


COMMIT;