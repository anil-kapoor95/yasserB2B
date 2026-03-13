
START TRANSACTION;

-- INSERT INTO `plugin_base_fields` VALUES (NULL, 'plugin_base_lbl_first_name', 'frontend', 'Label / First Name', 'script', NULL);
-- SET @id := (SELECT LAST_INSERT_ID());
-- INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'First Name', 'script');

-- INSERT INTO `plugin_base_fields` VALUES (NULL, 'plugin_base_lbl_last_name', 'frontend', 'Label / Last Name', 'script', NULL);
-- SET @id := (SELECT LAST_INSERT_ID());
-- INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Last Name', 'script');

-- INSERT INTO `plugin_base_fields` VALUES (NULL, 'plugin_base_lbl_phone', 'frontend', 'Label / Phone', 'script', NULL);
-- SET @id := (SELECT LAST_INSERT_ID());
-- INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Phone', 'script');

-- INSERT INTO `plugin_base_fields` VALUES (NULL, 'plugin_base_lbl_company_name', 'frontend', 'Label / Company Name', 'script', NULL);
-- SET @id := (SELECT LAST_INSERT_ID());
-- INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Company Name', 'script');

-- INSERT INTO `plugin_base_fields` VALUES (NULL, 'plugin_base_lbl_city', 'frontend', 'Label / City', 'script', NULL);
-- SET @id := (SELECT LAST_INSERT_ID());
-- INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'City', 'script');

-- INSERT INTO `plugin_base_fields` VALUES (NULL, 'plugin_base_lbl_cnf_pass', 'frontend', 'Label / Confirm Password', 'script', NULL);
-- SET @id := (SELECT LAST_INSERT_ID());
-- INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Confirm Password', 'script');

-- INSERT INTO `plugin_base_fields` VALUES (NULL, 'plugin_base_lbl_vehicles', 'frontend', 'Label / No. of Vehicle', 'script', NULL);
-- SET @id := (SELECT LAST_INSERT_ID());
-- INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'No. of Vehicle', 'script');

-- INSERT INTO `plugin_base_fields` VALUES (NULL, 'btnRegister', 'frontend', 'Register', 'script', NULL);
-- SET @id := (SELECT LAST_INSERT_ID());
-- INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Register', 'script');

-- INSERT INTO `plugin_base_fields` VALUES (NULL, 'plugin_base_login', 'backend', 'Plugin Base / Links / Already have an account? Login', 'plugin', '2017-11-24 04:23:25');
-- SET @id := (SELECT LAST_INSERT_ID());
-- INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Already have an account? Login', 'plugin');

-- INSERT INTO `plugin_base_fields` VALUES (NULL, 'plugin_base_link_register', 'backend', 'Plugin Base / Links / Register', 'plugin', '2017-11-24 04:23:25');
-- SET @id := (SELECT LAST_INSERT_ID());
-- INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Register', 'plugin');

-- INSERT INTO `plugin_base_fields` 
-- VALUES (NULL, 'plugin_base_link_create_account', 'backend', 'Plugin Base / Links / Don''t have an account yet? Create an account', 'plugin', '2017-11-24 04:23:25');
-- SET @id := (SELECT LAST_INSERT_ID());
-- INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Don''t have an account yet? Create an account', 'plugin');

-- DROP TABLE IF EXISTS `suppliers`;
-- CREATE TABLE `suppliers` (
--   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
--   `auth_id` int(10) unsigned NOT NULL,

--   `first_name` varchar(100) NOT NULL,
--   `last_name` varchar(100) DEFAULT NULL,
--   `phone` varchar(50) DEFAULT NULL,

--   `company_name` varchar(150) DEFAULT NULL,
--   `city` varchar(100) DEFAULT NULL,
--   `total_vehicles` int(10) unsigned DEFAULT 0,

--   `status` enum('T','F') DEFAULT 'F',

--   `created` datetime DEFAULT CURRENT_TIMESTAMP,
--   `modified` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

--   PRIMARY KEY (`id`),
--   KEY `auth_id` (`auth_id`)

-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- INSERT INTO `plugin_auth_roles` (`id`, `role`, `is_backend`, `is_admin`, `status`) VALUES
-- (5, 'Supplier', 'T', 'T', 'T');

-- INSERT INTO `plugin_base_fields` VALUES (NULL, 'plugin_base_role_arr_ARRAY_5', 'arrays', 'Plugin Base / Supplier', 'plugin', NULL);
-- SET @id := (SELECT LAST_INSERT_ID());
-- INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Supplier', 'plugin');

-- ALTER TABLE `bookings`
-- ADD `is_auction` TINYINT(1) DEFAULT 0 AFTER `return_status_f`,
-- ADD `auctioned_on` DATETIME NULL AFTER`is_auction`,
-- ADD `supplier_id` INT(10) UNSIGNED NULL AFTER `auctioned_on`;

-- DROP TABLE IF EXISTS `auctions`;
-- CREATE TABLE `auctions` (
--   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
--   `booking_id` int(10) unsigned NULL,
--   `supplier_id` int(10) unsigned NULL,
--   `status` ENUM('active','ended','cancelled') DEFAULT 'active',
--   `created` datetime DEFAULT CURRENT_TIMESTAMP,
--   `modified` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

--   PRIMARY KEY (`id`),
--   KEY `booking_id` (`booking_id`),
--   KEY `supplier_id` (`supplier_id`)

-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- INSERT INTO `plugin_base_fields` VALUES (NULL, 'menuCategory', 'backend', 'Menu / Category', 'script', NULL);
-- SET @id := (SELECT LAST_INSERT_ID());
-- INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Category', 'script');

-- DROP TABLE IF EXISTS `categories`;
-- CREATE TABLE IF NOT EXISTS `categories` (
--   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
--   `category` varchar(100) DEFAULT NULL,
--   PRIMARY KEY (`id`)
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- INSERT INTO `plugin_base_fields` VALUES (NULL, 'infocategiryDesc', 'backend', 'Infobox / Category', 'script', NULL);
-- SET @id := (SELECT LAST_INSERT_ID());
-- INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Below is a list of all categories. If you want to add a new one, click on the "+ Add category" button.', 'script');

-- INSERT INTO `plugin_base_fields` VALUES (NULL, 'btnAddCategory', 'backend', 'Button / Add category', 'script', NULL);
-- SET @id := (SELECT LAST_INSERT_ID());
-- INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Add category', 'script');

-- INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblCategory', 'backend', 'Label / Category', 'script', NULL);
-- SET @id := (SELECT LAST_INSERT_ID());
-- INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Category', 'script');

-- INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblCategoryName', 'backend', 'Label / Category name', 'script', NULL);
-- SET @id := (SELECT LAST_INSERT_ID());
-- INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Category name', 'script');

-- INSERT INTO `plugin_base_fields` VALUES (NULL, 'infoAddCategoryTitle', 'backend', 'Infobx / Add Category', 'script', NULL);
-- SET @id := (SELECT LAST_INSERT_ID());
-- INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Add Category', 'script');

-- INSERT INTO `plugin_base_fields` VALUES (NULL, 'infoUpdateCategoryTitle', 'backend', 'Infobx / Update Category', 'script', NULL);
-- SET @id := (SELECT LAST_INSERT_ID());
-- INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Update Category', 'script');


INSERT INTO `plugin_base_fields` VALUES (NULL, 'menuAvailableBookings', 'backend', 'Menu / Available Bookings', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Available Bookings', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'menuAcceptedBookings', 'backend', 'Menu / Accepted Bookings', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Accepted Bookings', 'script');

COMMIT;