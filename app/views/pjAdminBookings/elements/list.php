<table class="table" cellspacing="0" cellpadding="0" style="width: 100%; margin-top: 10px;">
	<thead>
		<tr>
			<th style="width: 70px;"><?php __('lblBookingID');?></th>
			<th style="width: 120px;"><?php __('lblClient');?></th>
			<th style="width: 100px;"><?php __('lblDateAndTime');?></th>
			<th style="width: 150px;"><?php __('lblFromTo');?></th>
			<th style="width: 100px;"><?php __('lblVehicle');?></th>
			<th style="width: 100px;"><?php __('lblDistance');?></th>
			<th style="width: 100px;"><?php __('lblPassengers');?></th>
			<th style="width: 120px;"><?php __('lblPayment');?></th>
			<th colspan="2"><?php __('lblAdditionalInfo');?></th>
		</tr>
	</thead>
	<tbody>
		<?php
		if(count($tpl['transfer_arr']) > 0)
		{
			$field_arr = array();
			$field_arr['phone'] = __('lblBookingPhone', true, false);
			$field_arr['email'] = __('lblBookingEmail', true, false);
			$field_arr['company'] = __('lblBookingCompany', true, false);
			$field_arr['c_notes'] = __('lblBookingNotes', true, false);
			$field_arr['address'] = __('lblBookingAddress', true, false);
			$field_arr['city'] = __('lblBookingCity', true, false);
			$field_arr['state'] = __('lblBookingState', true, false);
			$field_arr['zip'] = __('lblBookingZip', true, false);
			$field_arr['country'] = __('lblBookingCountry', true, false);
			$field_arr['c_airline_company'] = __('lblBookingAirlineCompany', true, false);
			$field_arr['c_departure_airline_company'] = __('lblDepartureAirlineCompany', true, false);
			$field_arr['c_flight_number'] = __('lblArrivalFlightNumber', true, false);
			$field_arr['c_flight_time'] = __('lblFlightArrivalTime', true, false);
			$field_arr['c_departure_flight_number'] = __('lblDepartureFlightNumber', true, false);
			$field_arr['c_departure_flight_time'] = __('lblFlightDepartureTime', true, false);
			$field_arr['c_destination_address'] = __('lblBookingDestAddress', true, false);
			$field_arr['c_cruise_ship'] = __('lblBookingCruiseShip', true, false);
			$field_arr['c_terminal'] = __('lblBookingTerminal', true, false);
			
			$name_titles = __('personal_titles', true, false);
			$statuses = __('booking_statuses', true, false);
			
			$row = 1;
			foreach($tpl['transfer_arr'] as $v)
			{
				$client_name_arr = array();
				$additional_arr = array();
				if(!empty($v['title']) || !empty($v['title']))
				{
					$client_name_arr[] = !empty($v['client_id']) ? $name_titles[$v['title']] : $name_titles[$v['title']];
				}
				if(!empty($v['name']) || !empty($v['name']))
				{
					$client_name_arr[] = !empty($v['client_id']) ? pjSanitize::clean($v['name']) : pjSanitize::clean($v['name']);
				}
				$total = pjCurrency::formatPrice($v['total']);
				$payment_methods = $tpl['payment_titles'];

				foreach($field_arr as $field => $title)
				{
					if(empty($v['return_id']))
					{
						if(in_array($field, array('c_departure_airline_company', 'c_departure_flight_number', 'c_departure_flight_time')))
						{
							$v[$field] = NULL;
						}
					}else{
						if(in_array($field, array('c_airline_company', 'c_flight_number', 'c_flight_time')))
						{
							$v[$field] = NULL;
						}
					}
					if(!empty($v[$field]))
					{
						if($field != 'c_notes')
						{
							$additional_arr[] = '<td>'.$title.'</td><td>'.pjSanitize::clean($v[$field]).'</td>';
						}
					}
				}
				$row_span = count($additional_arr) > 0 ? count($additional_arr) : 1; 
				
				$uuid_column = $row_span;
				if(!empty($v['c_notes']))
				{
					$uuid_column++;
				}
				if(count($additional_arr) >= 2)
				{
					foreach($additional_arr as $k => $addition)
					{
						if($k == 0)
						{
							?>
							<tr class="<?php echo $row%2==0? 'even' : 'odd';?>">
								<td rowspan="<?php echo $uuid_column;?>"><b><?php echo !empty($v['uuid']) ? pjSanitize::clean($v['uuid']) : pjSanitize::clean($v['uuid2']);?></b></td>
								<td rowspan="<?php echo $row_span;?>"><b><?php echo join(" ", $client_name_arr);?></b></td>
								<td rowspan="<?php echo $row_span;?>"><b><?php echo date($tpl['option_arr']['o_date_format'], strtotime($v['booking_date'])) . ', ' . date($tpl['option_arr']['o_time_format'], strtotime($v['booking_date']));?></b></td>
								<td rowspan="<?php echo $row_span;?>"><?php __('lblFrom');?>: <b><?php echo pjSanitize::html($v['pickup_address']) ;?></b><br/><?php __('lblTo');?>: <b><?php echo pjSanitize::html($v['return_address']) ;?></b></td>
								<td rowspan="<?php echo $row_span;?>"><?php echo pjSanitize::clean($v['fleet']);?></td>
								<td rowspan="<?php echo $row_span;?>"><?php echo pjSanitize::clean($v['distance']);?> km</td>
								<td rowspan="<?php echo $row_span;?>"><?php echo $v['passengers'] . ' ' . ($v['passengers'] != 1 ? __('lblPassengers', true, false) : __('lblPassenger', true, false)) ;?></td>
								<td rowspan="<?php echo $row_span;?>"><b><?php echo __('lblTotal', true, false) . ': ' . $total;?></b><br/><?php __('lblVia'); ?> <?php echo $payment_methods[$v['payment_method']];?><br/><?php echo $statuses[$v['status']];?></td>
								<?php echo $addition;?>
							</tr>
							<?php
						}else{
							?>
							<tr class="<?php echo $row%2==0? 'even' : 'odd';?>">
								<?php echo $addition;?>
							</tr>
							<?php
						}
					}
				}else{
					?>
					<tr class="<?php echo $row%2==0? 'even' : 'odd';?>">
						<td><b><?php echo !empty($v['uuid']) ? pjSanitize::clean($v['uuid']) : pjSanitize::clean($v['uuid2']);?></b></td>
						<td><b><?php echo join(" ", $client_name_arr);?></b></td>
						<td><b><?php echo date($tpl['option_arr']['o_date_format'], strtotime($v['booking_date'])) . ', ' . date($tpl['option_arr']['o_time_format'], strtotime($v['booking_date']));?></b></td>
						<td rowspan="<?php echo $row_span;?>"><?php __('lblFrom');?>: <b><?php echo pjSanitize::html($v['pickup_address']) ;?></b><br/><?php __('lblTo');?>: <b><?php echo pjSanitize::html($v['return_address']) ;?></b></td>
						<td><?php echo pjSanitize::clean($v['vehicle']);?></td>
						<td rowspan="<?php echo $row_span;?>"><?php echo pjSanitize::clean($v['distance']);?> km</td>
						<td><?php echo $v['passengers'] . ' ' . ($v['passengers'] != 1 ? __('lblPassengers', true, false) : __('lblPassenger', true, false)) ;?><br/><?php echo $v['luggage'] . ' ' . __('lblLuggage', true, false) ;?></td>
						<?php
						if(!empty($v['payment_method']))
						{ 
							?>
							<td><b><?php echo __('lblTotal', true, false) . ': ' . $total;?></b><br/><?php __('lblVia'); ?> <?php echo $payment_methods[$v['payment_method']];?><br/><?php echo $statuses[$v['status']];?></td>
							<?php
						}else{
							?>
							<td><b><?php echo __('lblTotal', true, false) . ': ' . $total;?></b><br/><?php echo $statuses[$v['status']];?></td>
							<?php
						} 
						?>
						
						<?php
						if(count($additional_arr) == 0)
						{
							?><td colspan="2">&nbsp;</td><?php
						}
						if(count($additional_arr) == 1)
						{
							echo join('', $additional_arr);
						} 
						?>
					</tr>
					<?php
				}
				if(!empty($v['c_notes']))
				{
					?>
					<tr>
						<td><?php echo __('lblBookingNotes', true, false);?></td>
						<td colspan="8"><?php echo nl2br($v['c_notes']);?></td>
					</tr>
					<?php
				}
				$row++;
			}
		} else {
			?>
			<tr>
				<td colspan="9"><?php __('gridEmptyResult');?></td>
			</tr>
			<?php
		}
		?>
	</tbody>
</table>