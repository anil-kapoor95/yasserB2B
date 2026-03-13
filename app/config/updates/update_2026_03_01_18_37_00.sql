
START TRANSACTION;

-- INSERT INTO `taxi_plugin_base_fields` VALUES (NULL, 'dash_total_bookings', 'backend', 'Label / Total Bookings', 'script', NULL);
-- SET @id := (SELECT LAST_INSERT_ID());
-- INSERT INTO `taxi_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '1', 'title', 'Total Bookings', 'script');

-- INSERT INTO `taxi_plugin_base_fields` VALUES (NULL, 'dash_total_revenue', 'backend', 'Label / Total Revenue', 'script', NULL);
-- SET @id := (SELECT LAST_INSERT_ID());
-- INSERT INTO `taxi_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '1', 'title', 'Total Revenue', 'script');

-- INSERT INTO `taxi_plugin_base_fields` VALUES (NULL, 'dash_completed_bookings', 'backend', 'Label / Completed Bookings', 'script', NULL);
-- SET @id := (SELECT LAST_INSERT_ID());
-- INSERT INTO `taxi_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '1', 'title', 'Completed Bookings', 'script');

-- INSERT INTO `taxi_plugin_base_fields` VALUES (NULL, 'dash_cancelled_bookings', 'backend', 'Label / Cancelled Bookings', 'script', NULL);
-- SET @id := (SELECT LAST_INSERT_ID());
-- INSERT INTO `taxi_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '1', 'title', 'Cancelled Bookings', 'script');

-- INSERT INTO `taxi_plugin_base_fields` VALUES (NULL, 'dash_new_customers', 'backend', 'Label / New Customers', 'script', NULL);
-- SET @id := (SELECT LAST_INSERT_ID());
-- INSERT INTO `taxi_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '1', 'title', 'New Customers', 'script');

-- INSERT INTO `taxi_plugin_base_fields` VALUES (NULL, 'dash_total_customers', 'backend', 'Label / Total Customers', 'script', NULL);
-- SET @id := (SELECT LAST_INSERT_ID());
-- INSERT INTO `taxi_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '1', 'title', 'Total Customers', 'script');

-- INSERT INTO `taxi_plugin_base_fields` VALUES (NULL, 'dash_revenue_trend', 'backend', 'Label / Revenue Trend', 'script', NULL);
-- SET @id := (SELECT LAST_INSERT_ID());
-- INSERT INTO `taxi_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '1', 'title', 'Revenue Trend', 'script');

-- INSERT INTO `taxi_plugin_base_fields` VALUES (NULL, 'dash_payment_methods', 'backend', 'Label / Payment Methods', 'script', NULL);
-- SET @id := (SELECT LAST_INSERT_ID());
-- INSERT INTO `taxi_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '1', 'title', 'Payment Methods', 'script');

-- INSERT INTO `taxi_plugin_base_fields` VALUES (NULL, 'dash_paid_unpaid', 'backend', 'Label / Paid vs Unpaid', 'script', NULL);
-- SET @id := (SELECT LAST_INSERT_ID());
-- INSERT INTO `taxi_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '1', 'title', 'Paid vs Unpaid', 'script');

-- INSERT INTO `taxi_plugin_base_fields` VALUES (NULL, 'dash_booking_status', 'backend', 'Label / Booking Status', 'script', NULL);
-- SET @id := (SELECT LAST_INSERT_ID());
-- INSERT INTO `taxi_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '1', 'title', 'Booking Status', 'script');

-- INSERT INTO `taxi_plugin_base_fields` VALUES (NULL, 'dash_revenue_vehicle_type', 'backend', 'Label / Revenue by Vehicle Type', 'script', NULL);
-- SET @id := (SELECT LAST_INSERT_ID());
-- INSERT INTO `taxi_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '1', 'title', 'Revenue by Vehicle Type', 'script');

-- INSERT INTO `taxi_plugin_base_fields` VALUES (NULL, 'dash_peak_booking_hours', 'backend', 'Label / Peak Booking Hours', 'script', NULL);
-- SET @id := (SELECT LAST_INSERT_ID());
-- INSERT INTO `taxi_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '1', 'title', 'Peak Booking Hours', 'script');

-- INSERT INTO `taxi_plugin_base_fields` VALUES (NULL, 'dash_summary_by_date', 'backend', 'Label / Summary By Date', 'script', NULL);
-- SET @id := (SELECT LAST_INSERT_ID());
-- INSERT INTO `taxi_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '1', 'title', 'Summary By Date', 'script');

-- INSERT INTO `taxi_plugin_base_fields` VALUES (NULL, 'payment_statuses_ARRAY_stripe', 'arrays', 'payment_statuses_ARRAY_stripe', 'script', NULL);
-- SET @id := (SELECT LAST_INSERT_ID());
-- INSERT INTO `taxi_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '1', 'title', 'Stripe', 'script');

-- INSERT INTO `taxi_plugin_base_fields` VALUES (NULL, 'payment_statuses_ARRAY_cash', 'arrays', 'payment_statuses_ARRAY_cash', 'script', NULL);
-- SET @id := (SELECT LAST_INSERT_ID());
-- INSERT INTO `taxi_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '1', 'title', 'Cash', 'script');

-- INSERT INTO `taxi_plugin_base_fields` VALUES (NULL, 'payment_statuses_ARRAY_bank', 'arrays', 'payment_statuses_ARRAY_bank', 'script', NULL);
-- SET @id := (SELECT LAST_INSERT_ID());
-- INSERT INTO `taxi_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '1', 'title', 'Bank', 'script');

-- INSERT INTO `taxi_plugin_base_fields` VALUES (NULL, 'dash_daily', 'backend', 'Label / Daily', 'script', NULL);
-- SET @id := (SELECT LAST_INSERT_ID());
-- INSERT INTO `taxi_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '1', 'title', 'Daily', 'script');

-- INSERT INTO `taxi_plugin_base_fields` VALUES (NULL, 'dash_weekly', 'backend', 'Label / Weekly', 'script', NULL);
-- SET @id := (SELECT LAST_INSERT_ID());
-- INSERT INTO `taxi_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '1', 'title', 'Weekly', 'script');

-- INSERT INTO `taxi_plugin_base_fields` VALUES (NULL, 'dash_monthly', 'backend', 'Label / Monthly', 'script', NULL);
-- SET @id := (SELECT LAST_INSERT_ID());
-- INSERT INTO `taxi_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '1', 'title', 'Monthly', 'script');

-- INSERT INTO `taxi_plugin_base_fields` VALUES (NULL, 'dash_booking_per_day', 'backend', 'Label / Bookings Per Day', 'script', NULL);
-- SET @id := (SELECT LAST_INSERT_ID());
-- INSERT INTO `taxi_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '1', 'title', 'Bookings Per Day', 'script');

-- INSERT INTO `taxi_plugin_base_fields` VALUES (NULL, 'dash_peek_booking', 'backend', 'Label / Peek bookings', 'script', NULL);
-- SET @id := (SELECT LAST_INSERT_ID());
-- INSERT INTO `taxi_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '1', 'title', 'Peek bookings', 'script');

-- INSERT INTO `taxi_plugin_base_fields` VALUES (NULL, 'lblAllStatus', 'backend', 'Label / All Status', 'script', NULL);
-- SET @id := (SELECT LAST_INSERT_ID());
-- INSERT INTO `taxi_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '1', 'title', 'All Status', 'script');

-- INSERT INTO `taxi_plugin_base_fields` VALUES (NULL, 'lblALlPayments', 'backend', 'Label / All Payments', 'script', NULL);
-- SET @id := (SELECT LAST_INSERT_ID());
-- INSERT INTO `taxi_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '1', 'title', 'All Payments', 'script');

-- INSERT INTO `taxi_plugin_base_fields` VALUES (NULL, 'lblFilter', 'backend', 'Label / Filter', 'script', NULL);
-- SET @id := (SELECT LAST_INSERT_ID());
-- INSERT INTO `taxi_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '1', 'title', 'Filter', 'script');

-- INSERT INTO `taxi_plugin_base_fields` VALUES (NULL, 'lblTimeType', 'backend', 'Label / Time Type', 'script', NULL);
-- SET @id := (SELECT LAST_INSERT_ID());
-- INSERT INTO `taxi_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '1', 'title', 'Time Type', 'script');

-- INSERT INTO `taxi_plugin_base_fields` VALUES (NULL, 'time_type_ARRAY_present', 'arrays', 'time_type_ARRAY_present', 'script', NULL);
-- SET @id := (SELECT LAST_INSERT_ID());
-- INSERT INTO `taxi_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '1', 'title', 'Present', 'script');

-- INSERT INTO `taxi_plugin_base_fields` VALUES (NULL, 'time_type_ARRAY_past', 'arrays', 'time_type_ARRAY_past', 'script', NULL);
-- SET @id := (SELECT LAST_INSERT_ID());
-- INSERT INTO `taxi_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '1', 'title', 'Past', 'script');

-- INSERT INTO `taxi_plugin_base_fields` VALUES (NULL, 'time_type_ARRAY_future', 'arrays', 'time_type_ARRAY_future', 'script', NULL);
-- SET @id := (SELECT LAST_INSERT_ID());
-- INSERT INTO `taxi_plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '1', 'title', 'Future', 'script');

COMMIT;