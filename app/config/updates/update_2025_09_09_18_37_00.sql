
START TRANSACTION;

DROP TABLE IF EXISTS `cities`;
CREATE TABLE `cities` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `status` enum('T','F') DEFAULT 'T',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `fleets_prices`;
CREATE TABLE `fleets_prices` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fleet_id` int(10) unsigned NOT NULL DEFAULT 0,
  `from_city` int(10) unsigned DEFAULT NULL,
  `to_city` int(10) unsigned DEFAULT NULL,
  `price` decimal(9,2) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `fleet_id` (`fleet_id`,`from_city`,`to_city`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblPriceFromCitytoCity', 'backend', 'Label / Price between two cities', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Price between two cities', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblFromCity', 'backend', 'Label / From city', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'From city', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblToCity', 'backend', 'Label / To city', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'To city', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblFixedPrice', 'backend', 'Label / Fixed price', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Fixed price', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'front_transfer_prices', 'backend', 'Label / Transfer price', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Transfer price', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'btnAddCity', 'backend',  'Button / Add City', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Add City', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'infoCitiesTitle', 'backend', 'Menu / Cities', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Cities', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'infoCitiesDesc', 'backend', 'City Desc', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Below is a list of all cities. If you want to add a new one, click the "+ Add City" button.', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblCityName', 'backend', 'Name', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Name', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'pjAdminCities', 'backend', 'pjAdminCities', 'script', '2022-03-31 11:17:58');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'City Module', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'pjAdminCities_pjActionIndex', 'backend', 'pjAdminCities_pjActionIndex', 'script', '2022-03-31 10:21:45');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'City List', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'pjAdminCities_pjActionCreate', 'backend', 'pjAdminCities_pjActionCreate', 'script', '2022-03-31 10:22:11');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Add City', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'pjAdminCities_pjActionUpdate', 'backend', 'pjAdminCities_pjActionUpdate', 'script', '2022-03-31 10:22:30');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Update City', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'pjAdminCities_pjActionDeleteCity', 'backend', 'pjAdminCities_pjActionDeleteCity', 'script', '2022-03-31 10:22:42');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Delete single City', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'pjAdminCities_pjActionDeleteCityBulk', 'backend', 'pjAdminCities_pjActionDeleteCityBulk', 'script', '2022-03-31 10:23:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Delete multile City', 'script');

INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, NULL, 'pjAdminCities');
SET @level_1_id := (SELECT LAST_INSERT_ID());

  INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_1_id, 'pjAdminCities_pjActionIndex');
  SET @level_2_id := (SELECT LAST_INSERT_ID());

    INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_2_id, 'pjAdminCities_pjActionCreate');
    INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_2_id, 'pjAdminCities_pjActionUpdate');
    INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_2_id, 'pjAdminCities_pjActionDeleteCity');
    INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_2_id, 'pjAdminCities_pjActionDeleteCityBulk');

COMMIT;