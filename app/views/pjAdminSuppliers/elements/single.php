<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alpen Heir KG - Invoice</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.5;
            color: #333;
            background: #fff;
            font-size: 14px;
        }

        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
            border-bottom: 1px solid #eee;
            padding-bottom: 30px;
        }

        .company-name {
            font-size: 28px;
            font-weight: 600;
            color: #222;
            margin-bottom: 8px;
        }

        .invoice-title {
            font-size: 16px;
            color: #666;
            font-weight: 400;
        }

        .invoice-details {
            text-align: right;
            margin-bottom: 40px;
        }

        .invoice-details h3 {
            font-size: 18px;
            margin-bottom: 10px;
            color: #333;
        }

        .content-wrapper {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-bottom: 40px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table thead th {
            background: #f8f9fa;
            padding: 15px 20px;
            text-align: left;
            font-weight: 600;
            color: #333;
            border-bottom: 2px solid #dee2e6;
            font-size: 16px;
        }

        .table tbody td {
            padding: 12px 20px;
            border-bottom: 1px solid #f1f1f1;
            vertical-align: top;
        }

        .table tbody td:first-child {
            font-weight: 500;
            color: #555;
            width: 40%;
        }

        .table tbody td:last-child {
            color: #333;
            width: 60%;
        }

        .table tbody tr.bold td {
            font-weight: 600;
        }

        .table tbody tr:hover {
            background-color: #fafafa;
        }

        @media (max-width: 768px) {
            .content-wrapper {
                grid-template-columns: 1fr;
                gap: 30px;
            }
            
            .invoice-container {
                padding: 20px 15px;
            }

            .table thead th,
            .table tbody td {
                padding: 10px 15px;
            }

            .company-name {
                font-size: 24px;
            }
        }

        @media print {
            body {
                background: #fff;
            }
            
            .invoice-container {
                max-width: none;
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <!-- Header -->
        <div class="header">
            <h1 class="company-name">ALPEN HEIR KG</h1>
            <p class="invoice-title">Transportation Services</p>
        </div>

        <!-- Invoice Details -->
        <div class="invoice-details">
            <div>
                <h3>Invoice #<?php echo isset($pickup_arr['id']) ? $pickup_arr['id'] : ''; ?></h3>
                <p><?php echo isset($pickup_arr['created']) ? date('F j, Y', strtotime($pickup_arr['created'])) : date('F j, Y'); ?></p>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="content-section">
                <div class="left-column">
                    <table class="table" cellspacing="0" cellpadding="0">
                        <thead>
                            <tr>
                                <th colspan="2"><?php __('lblClientDetails');?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php

                             // echo "<pre>"; print_r($pickup_arr); echo "</pre>";
                            $statuses = __('booking_statuses', true, false);
                            $payment_methods = $tpl['payment_titles'];
                            $name_titles = __('personal_titles', true, false);

                             // echo "<pre>"; print_r($payment_methods); echo "</pre>";
                            $remainingBalance = '';
                            $deposit = '';

                            if (in_array($pickup_arr['payment_method'], ['bank', 'cash'])) {
                                // For offline payment
                                $remainingBalance = $pickup_arr['total']; // OR deposit? depends on your rule
                                $deposit = '0.00';
                            } else {
                                // For online payment
                                $remainingBalance = $pickup_arr['remainingBalance'];
                                $deposit = $pickup_arr['deposit'];
                            }


                            $client_name_arr = array();
                            if(!empty($pickup_arr['c_title']) || !empty($pickup_arr['title']))
                            {
                                $client_name_arr[] = !empty($pickup_arr['client_id']) ? $name_titles[$pickup_arr['title']] : $name_titles[$pickup_arr['c_title']];
                            }
                            $fname = trim($pickup_arr['c_fname']);
                            $lname = trim($pickup_arr['c_lname']);

                            // If both first and last name from c_ fields are empty → use name
                            if ($fname === '' && $lname === '') {
                                $client_name_arr[] = pjSanitize::clean($pickup_arr['name']);
                            } else {
                                // Otherwise use c_fname + c_lname
                                $client_name_arr[] = pjSanitize::clean($fname . ' ' . $lname);
                            }

                            // if(!empty($pickup_arr['name']) || !empty($pickup_arr['c_fname']))
                            // {
                            //     $client_name_arr[] = !empty($pickup_arr['client_id']) ? pjSanitize::clean($pickup_arr['name']) : pjSanitize::clean($pickup_arr['c_fname']) . ' ' . pjSanitize::clean($pickup_arr['c_lname']);
                            // }
                            if(!empty($client_name_arr))
                            {
                                ?>
                                <tr class="bold">
                                    <td><?php __('lblName', false, false);?></td>
                                    <td><?php echo join(' ', $client_name_arr);?></td>
                                </tr>
                                <?php
                            }
                            if(!empty($pickup_arr['c_phone']) || !empty($pickup_arr['phone']))
                            {
                                ?>
                                <tr>
                                    <td><?php __('lblBookingPhone', false, false);?></td>
                                    <td><?php echo pjSanitize::clean(!empty($pickup_arr['c_phone']) ? $pickup_arr['c_phone'] : $pickup_arr['phone']); ?></td>
                                </tr>
                                <?php
                            }
                            if(!empty($pickup_arr['email']))
                            {
                                ?>
                                <tr>
                                    <td><?php __('lblBookingEmail', false, false);?></td>
                                    <td><?php echo pjSanitize::clean($pickup_arr['email']);?></td>
                                </tr>
                                <?php
                            }
                            if(!empty($pickup_arr['company']))
                            {
                                ?>
                                <tr>
                                    <td><?php __('lblBookingCompany', false, false);?></td>
                                    <td><?php echo pjSanitize::clean($pickup_arr['company']);?></td>
                                </tr>
                                <?php
                            }
                            if(!empty($pickup_arr['address']))
                            {
                                ?>
                                <tr>
                                    <td><?php __('lblBookingAddress', false, false);?></td>
                                    <td><?php echo pjSanitize::clean($pickup_arr['address']);?></td>
                                </tr>
                                <?php
                            }
                            if(!empty($pickup_arr['city']))
                            {
                                ?>
                                <tr>
                                    <td><?php __('lblBookingCity', false, false);?></td>
                                    <td><?php echo pjSanitize::clean($pickup_arr['city']);?></td>
                                </tr>
                                <?php
                            }
                            if(!empty($pickup_arr['state']))
                            {
                                ?>
                                <tr>
                                    <td><?php __('lblBookingState', false, false);?></td>
                                    <td><?php echo pjSanitize::clean($pickup_arr['state']);?></td>
                                </tr>
                                <?php
                            }
                            if(!empty($pickup_arr['zip']))
                            {
                                ?>
                                <tr>
                                    <td><?php __('lblBookingZip', false, false);?></td>
                                    <td><?php echo pjSanitize::clean($pickup_arr['zip']);?></td>
                                </tr>
                                <?php
                            }
                            if(!empty($pickup_arr['country']))
                            {
                                ?>
                                <tr>
                                    <td><?php __('lblBookingCountry', false, false);?></td>
                                    <td><?php echo pjSanitize::clean($pickup_arr['country']);?></td>
                                </tr>
                                <?php
                            } 
                            if(!empty($pickup_arr['c_notes']))
                            {
                                ?>
                                <tr>
                                    <td><?php __('lblBookingNotes', false, false);?></td>
                                    <td><?php echo pjSanitize::clean($pickup_arr['c_notes']);?></td>
                                </tr>
                                <?php
                            }
                            if(!empty($pickup_arr['c_airline_company']))
                            {
                                ?>
                                <tr>
                                    <td><?php __('lblBookingAirlineCompany', false, false);?></td>
                                    <td><?php echo pjSanitize::clean($pickup_arr['c_airline_company']);?></td>
                                </tr>
                                <?php
                            }
                            if(!empty($pickup_arr['c_flight_number']))
                            {
                                ?>
                                <tr>
                                    <td><?php __('lblBookingFlightNumber', false, false);?></td>
                                    <td><?php echo pjSanitize::clean($pickup_arr['c_flight_number']);?></td>
                                </tr>
                                <?php
                            }
                            if(!empty($pickup_arr['c_flight_time']))
                            {
                                ?>
                                <tr>
                                    <td><?php __('lblFlightTime', false, false);?></td>
                                    <td><?php echo pjSanitize::clean($pickup_arr['c_flight_time']);?></td>
                                </tr>
                                <?php
                            }

                             if(!empty($pickup_arr['c_departure_airline_company']))
                            {
                                ?>
                                <tr>
                                    <td><?php __('lblDepartureAirlineCompany', false, false);?></td>
                                    <td><?php echo pjSanitize::clean($pickup_arr['c_departure_airline_company']);?></td>
                                </tr>
                                <?php
                            }
                            if(!empty($pickup_arr['c_departure_flight_number']))
                            {
                                ?>
                                <tr>
                                    <td><?php __('lblDepartureFlightNumber', false, false);?></td>
                                    <td><?php echo pjSanitize::clean($pickup_arr['c_departure_flight_number']);?></td>
                                </tr>
                                <?php
                            }
                            if(!empty($pickup_arr['c_departure_flight_time']))
                            {
                                ?>
                                <tr>
                                    <td><?php __('lblFlightDepartureTime', false, false);?></td>
                                    <td><?php echo pjSanitize::clean($pickup_arr['c_departure_flight_time']);?></td>
                                </tr>
                                <?php
                            }
                            
                            if(!empty($pickup_arr['c_destination_address']))
                            {
                                ?>
                                <tr>
                                    <td><?php __('lblBookingDestAddress', false, false);?></td>
                                    <td><?php echo pjSanitize::clean($pickup_arr['c_destination_address']);?></td>
                                </tr>
                                <?php
                            }
                            if(!empty($pickup_arr['c_cruise_ship']))
                            {
                                ?>
                                <tr>
                                    <td><?php __('lblBookingCruiseShip', false, false);?></td>
                                    <td><?php echo pjSanitize::clean($pickup_arr['c_cruise_ship']);?></td>
                                </tr>
                                <?php
                            }
                            if(!empty($pickup_arr['c_terminal']))
                            {
                                ?>
                                <tr>
                                    <td><?php __('lblBookingTerminal', false, false);?></td>
                                    <td><?php echo pjSanitize::clean($pickup_arr['c_terminal']);?></td>
                                </tr>
                                <?php
                            } 
                            ?>
                        </tbody>
                    </table>
                </div>
                <div class="right-column">
                    <table class="table" cellspacing="0" cellpadding="0">
                        <thead>
                            <tr>
                                <th colspan="2"><?php __('lblEnquiryDetails');?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="bold">
                                <td><?php __('lblDateAndTime', false, false);?></td>
                                <td><?php echo date($tpl['option_arr']['o_date_format'] . ', ' . $tpl['option_arr']['o_time_format'], strtotime($pickup_arr['booking_date']));?></td>
                            </tr>
                            <tr>
                                <td><?php __('lblPickupAddress', false, false);?></td>
                                <td><?php echo $pickup_arr['pickup_address'];?></td>
                            </tr>
                            <tr>
                                <td><?php __('lblDropoffAddress', false, false);?></td>
                                <td><?php echo $pickup_arr['return_address'];?></td>
                            </tr>
                            <tr class="bold">
                                <td><?php __('lblVehicle', false, false);?></td>
                                <td><?php echo pjSanitize::clean($pickup_arr['fleet']);?></td>
                            </tr>
                            <tr class="bold">
                                <td><?php __('lblDistance', false, false);?></td>
                                <td><?php echo pjSanitize::clean($pickup_arr['distance']);?> km</td>
                            </tr>
                            <?php
                            if(isset($tpl['extras']))
                            { 
                                ?>
                                <tr class="bold">
                                    <td><?php __('lblExtras', false, false);?></td>
                                    <td><?php echo $tpl['extras'];?></td>
                                </tr>
                                <?php
                            } 
                            ?>
                            <tr>
                                <td><?php __('lblPassengers', false, false);?></td>
                                <td><?php echo $pickup_arr['passengers'];?></td>
                            </tr>
                            <tr>
                                <td><?php __('lblLuggage', false, false);?></td>
                                <td><?php echo $pickup_arr['luggage'];?></td>
                            </tr>
                            <tr class="bold">
                                <td><?php __('lblPayment', false, false);?></td>
                                <td><?php echo pjCurrency::formatPrice($pickup_arr['total']);?> / <?php echo $payment_methods[$pickup_arr['payment_method']];?></td>
                            </tr>
                             <tr class="bold">
                                <td><?php __('lblDeposit', false, false);?></td>
                                <td><?php echo pjCurrency::formatPrice($deposit);?> </td>
                            </tr>

                            <tr class="bold">
                                <td><?php __('lblRemainingBalance', false, false);?></td>
                                <td><?php echo pjCurrency::formatPrice($remainingBalance);?></td>
                            </tr>

                            <tr class="bold">
                                <td><?php __('lblStatus', false, false);?></td>
                                <td><?php echo $statuses[$pickup_arr['status']];?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>