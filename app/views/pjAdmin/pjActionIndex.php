<?php 
$today = pjDateTime::formatDate(date('Y-m-d'), 'Y-m-d', $tpl['option_arr']['o_date_format']);
$months = __('months', true);
ksort($months);
$bs = __('booking_statuses', true); 

$auth = pjAuth::factory();
$roleId = $auth->getRoleId();

// echo "<pre>"; print_r($roleId); echo "</pre>";
?>

<script>

	document.addEventListener('DOMContentLoaded', function () {

	function hasValue(val) {
        return val !== null && val !== undefined && val !== '' && val !== 'NA';
    }

    function isAirportAddress(address) {
    if (!address) return false;
	    const airportKeywords = [
	        'airport', 'flughafen',
	        'innsbruck airport', 'innsbruck flughafen',
	        'salzburg airport', 'salzburg flughafen', 'flughafen salzburg',
	        'munich airport', 'munich international airport', 'muc',
	        'münchen flughafen', 'muenchen flughafen',
	        'memmingen airport', 'memmingen flughafen',
	        'zurich airport', 'zürich flughafen', 'zuerich flughafen',
	        'verona airport', 'verona flughafen',
	        'bolzano airport', 'bozen flughafen',
	        'engadin airport', 'samedan st. moritz flughafen'
	    ];

	    const addr = address.toLowerCase();
	    return airportKeywords.some(keyword => addr.includes(keyword));
	}


	var calendarEl = document.getElementById('calendar');
	var calendar = new FullCalendar.Calendar(calendarEl, {
			height: "auto",
			timeZone: 'Europe/Vienna',
			initialView: 'dayGridMonth',
			editable: false,
			views: {
				listMonth: {
					buttonText: 'Agenda view',
				}
			},
			displayEventTime: true, // show time in month view
			eventTimeFormat: { // how to format time
			hour: '2-digit',
			minute: '2-digit',
			hour12: false // set false for 24-hour
			},
		headerToolbar: {
		left: 'prev, today, next',
		center: 'title',
		right: 'timeGridDay,dayGridMonth,timeGridWeek,listMonth'
		},
		eventDisplay: 'block',
		// weekNumbers: true,
		dayMaxEvents: true, 
       events: {
			url: 'index.php?controller=pjAdminFullDrivers&action=pjActionDriverCalendarEvents',
			method: 'GET'
		},

		eventContent: function(arg) {

			let lines = arg.event.title.split("\n");
			// THIS IS THE TIME YOU ARE MISSING
			let time = arg.timeText ? `<div class="fc-time">${arg.timeText}</div>` : "";
			let driverName = arg.event.extendedProps.driver_name || 'NA';
			let pickup = arg.event.extendedProps.pickup || 'NA';
		    let dropup = arg.event.extendedProps.return || 'NA';

			let html = `
				<div class="fc-custom-event">
					${time}
					<div>${lines[0]}</div>
					<div>${lines[1]}</div>
					<div>Pickup: ${pickup}</div>
		            <div>Drop:  ${dropup}</div>
				
				</div>
			`;
			return { html: html };
			},

			eventClick: function(info) {

			info.jsEvent.preventDefault();

			let event = info.event.extendedProps;

			let isCompleted = event.status.toLowerCase() === "completed";

			let editButton = `<button
				${isCompleted ? "disabled" : `onclick="editBooking(${info.event.id})"`}
				style="
					padding:8px 15px; 
					background:${isCompleted ? '#6c757d' : '#007bff'};
					color:#fff; 
					border:none; 
					border-radius:4px; 
					cursor:${isCompleted ? 'not-allowed' : 'pointer'};
					opacity:${isCompleted ? '0.7' : '1'};
				">
				${isCompleted ? "Completed" : "Mark as Completed"}
			</button>`;

			// ------------------------
			// Build Extras HTML
			// ------------------------
			let extrasHtml = "";

			if (event.extras && event.extras.length > 0) {
				extrasHtml += `<p><strong>Extras:</strong></p><ul>`;
				event.extras.forEach(ex => {
					extrasHtml += `<li>${ex.extra_name} (${ex.extra_value})</li>`;
				});
				extrasHtml += `</ul>`;
			} else {
				extrasHtml = `<p><strong>Extras:</strong> None</p>`;
			}

			const paymentLabels = {
					bank: "Card on Board",
					cash: "Cash on Board",
					stripe: "Pay via Stripe"
				};

			const paymentText = paymentLabels[event.payment_method] || event.payment_method;

			    /* Flight Details */
		    let pickupIsAirport  = isAirportAddress(event.pickup);
			let dropoffIsAirport = isAirportAddress(event.return);
	            let flightHtml = '';
					if (pickupIsAirport || dropoffIsAirport) {
					    flightHtml = `<hr><div class="row"><div class="col-sm-12">`;
					    /* ARRIVAL FLIGHT (Pickup = Airport) */
					    if (pickupIsAirport) {
					        flightHtml += `
					            <h4>Arrival Flight</h4>
					            ${hasValue(event.c_flight_number) ? `<p><strong>Flight No:</strong> ${event.c_flight_number}</p>` : ''}
					            ${hasValue(event.c_airline_company) ? `<p><strong>Airline:</strong> ${event.c_airline_company}</p>` : ''}
					            ${hasValue(event.c_flight_time) ? `<p><strong>Time:</strong> ${event.c_flight_time}</p>` : ''}
					        `;
					    }

					    /* DEPARTURE FLIGHT (Drop-off = Airport) */
					    if (dropoffIsAirport) {
					        flightHtml += `
					            <h4>Departure Flight</h4>
					            ${hasValue(event.c_departure_flight_number) ? `<p><strong>Flight No:</strong> ${event.c_departure_flight_number}</p>` : ''}
					            ${hasValue(event.c_departure_airline_company) ? `<p><strong>Airline:</strong> ${event.c_departure_airline_company}</p>` : ''}
					            ${hasValue(event.c_departure_flight_time) ? `<p><strong>Time:</strong> ${event.c_departure_flight_time}</p>` : ''}
					        `;
					    }
					    flightHtml += `</div></div>`;
					}

			let html = `
				<h3>Booking Details</h3>
				<div class="driverpopup">
				<p><strong>Name:</strong> ${event.names}</p>
				<p><strong>Car:</strong> ${event.cars}</p>
				<p><strong>Pickup:</strong> ${event.pickup}</p>
				<p><strong>Drop off:</strong> ${event.return}</p>

				<div class="row">
				<div class="col-sm-6">
					<p><strong>Status:</strong> ${event.status}</p>
					<p><strong>Passengers:</strong> ${event.passengers}</p>
					<p><strong>Payment type:</strong> ${paymentText}</p>
					<p><strong>Price:</strong> ${event.price} €</p>
					<p><strong>Date:</strong> ${event.display_date}</p>
				</div>
				<div class="col-sm-6">
					<p><strong>Customer Name:</strong> ${event.customername}</p>
					<p><strong>Customer Phone:</strong> ${event.customerphone}</p>
					<p><strong>Assign driver:</strong> ${event.driver_name}</p>
					${extrasHtml}
				</div>
			</div>
			<div class="row">
				<div class="col-sm-12">
					${flightHtml}
				</div>
			</div>
				<br>
				${editButton}
			</button>
				&nbsp;
			<button onclick="viewBooking(${info.event.id})" 
					style="padding:8px 15px; background:#007bff; color:#fff; border:none; border-radius:4px; cursor:pointer;">
				Views
			</button>
			&nbsp;
			<button onclick="printBooking(${info.event.id})" 
					style="padding:8px 15px; background:#007bff; color:#fff; border:none; border-radius:4px; cursor:pointer;">
				Print
			</button>
			</div>`;
			// 	&nbsp;
			// <button onclick="printBooking(${info.event.id})" 
			// 		style="padding:8px 15px; background:#007bff; color:#fff; border:none; border-radius:4px; cursor:pointer;">
			// 	Print
			// </button> 

		document.getElementById("bookingModalContent").innerHTML = html;
		document.getElementById("bookingModal").style.display = "block";
	}

	});
	
	calendar.render();
	});
</script>
<style>

  body {
    margin: 40px 10px;
    padding: 0;
    font-family: Arial, Helvetica Neue, Helvetica, sans-serif;
    font-size: 14px;
  }

  #calendar {
    /* max-width: 1100px; */
    margin: 0 auto;
  }
  .fc-h-event .fc-event-title {
    /* overflow: hidden; */
    /* right: 0px; */
    text-wrap: auto;
}

.fc-event-main {
    white-space: normal !important;
    height: auto !important;
    overflow: visible !important;
}



div#calendar .fc-header-toolbar button span.fc-icon {
    line-height: 0;
}
div#calendar .fc-header-toolbar  button {
    padding: 16px 12px;
    line-height: 0px;
    background: #0a5114;
    text-transform: capitalize;
}

div#calendar .fc-header-toolbar button.fc--button.fc-button.fc-button-primary {
    display: none;
}


@media only screen and (min-width :100px) and (max-width : 767px) {
   .driverpopup {
    height: 450px;
    overflow-y: auto !important;
 	overflow: hidden;
}
}

</style>
	<?php if($roleId == 4 || $roleId ==  '4') { 
			?>
		<div id='calendar'></div>
		<?php } else { ?>
	<div class="wrapper wrapper-content animated fadeInRight">
		<div class="ibox float-e-margins">
			<div class="ibox-title">
				<h5><?php __('dash_today');?></h5>
			</div>
			<div class="ibox-content">
				<div class="row">
					<div class="col-xs-4">
						<p class="h1 no-margins"><?php echo $tpl['enquiries_received_today'];?></p>
						<small class="text-info"><?php $tpl['enquiries_received_today'] != 1 ? __('dash_enquiries_received_today') : __('dash_enquiry_received_today');?></small>        
					</div><!-- /.col-xs-6 -->
		
					<div class="col-xs-4">
						<p class="h1 no-margins"><?php echo $tpl['reservations_today'];?></p>
						<small class="text-info"><?php $tpl['reservations_today'] != 1 ? __('dash_reservations_today') : __('dash_reservation_today');?></small>        
					</div><!-- /.col-xs-6 -->

					<div class="col-xs-4">
						<p class="h1 no-margins"><?php echo $tpl['total_reservations'];?></p>
						<small class="text-info"><?php $tpl['total_reservations'] != 1 ? __('dash_total_reservations') : __('dash_reservation');?></small>        
					</div><!-- /.col-xs-6 -->
				</div><!-- /.row -->
			</div>
		</div><!-- /.row -->

		<div class="row">
			<div class="col-lg-4">
				<div class="ibox float-e-margins">
					<div class="ibox-content ibox-heading clearfix">
						<div class="pull-left">
							<h3><?php __('dash_latest_enquiries');?></h3>
						</div><!-- /.pull-left -->

						<div class="pull-right m-t-md">
							<a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionIndex" class="btn btn-primary btn-sm btn-outline m-n"><?php __('lblDashViewAll');?></a>
						</div><!-- /.pull-right -->
					</div>

					<div class="ibox-content inspinia-timeline">
						<?php if (count($tpl['latest_enquiries']) > 0) { ?>
							<?php foreach ($tpl['latest_enquiries'] as $k => $v) { 
								$bookingname = pjSanitize::html($v['c_fname'] . ' ' . $v['c_lname']);?>
							<div class="timeline-item">
								<div class="row">
									<div class="col-xs-3 date">
										<i class="fa fa-calendar"></i>
										<?php echo date($tpl['option_arr']['o_date_format'], strtotime($v['booking_date'])) . ', ' . date($tpl['option_arr']['o_time_format'], strtotime($v['booking_date']));?>
									</div>
									<div class="col-xs-7 content">
										<a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionUpdate&amp;id=<?php echo $v['id']; ?>">
											<p class="m-b-xs"><strong><?php echo $bookingname;?></strong></p>
		
											<p class="m-n"><em><?php echo pjSanitize::html($v['fleet']);?></em></p>
										</a>
									</div>
		
									<div class="badge bg-<?php echo $v['status'];?> b-r-sm pull-right m-t-md m-r-sm"><?php echo @$bs[$v['status']];?></div>
								</div>
							</div>
							<?php } ?>
						<?php } else { ?>
							<p><?php __('dash_no_enquiries');?></p>
						<?php } ?>
					</div>
				</div>
			</div><!-- /.col-lg-4 -->

			<div class="col-lg-4">
				<div class="ibox float-e-margins">
					<div class="ibox-content ibox-heading clearfix">
						<div class="pull-left">
							<h3><?php __('dash_title_reservations_today');?></h3>
						</div><!-- /.pull-left -->

					</div>

					<div class="ibox-content inspinia-timeline">
						<?php if (count($tpl['reservations_today_arr']) > 0) { ?>
							<?php foreach ($tpl['reservations_today_arr'] as $k => $v) { 
								$bookingname = pjSanitize::html($v['c_fname'] . ' ' . $v['c_lname']);
								?>
								<div class="timeline-item">
									<div class="row">
										<div class="col-xs-3 date">
											<i class="fa fa-calendar"></i>
											<?php echo date($tpl['option_arr']['o_date_format'], strtotime($v['booking_date'])) . ', ' . date($tpl['option_arr']['o_time_format'], strtotime($v['booking_date']));?>
										</div>
										<div class="col-xs-7 content">
											<a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionUpdate&amp;id=<?php echo $v['id']; ?>">
												<p class="m-b-xs"><strong><?php echo $bookingname;?></strong></p>
			
												<p class="m-n"><em><?php echo pjSanitize::html($v['fleet']);?></em></p>
											</a>
										</div>
			
										<div class="badge bg-<?php echo $v['status'];?> b-r-sm pull-right m-t-md m-r-sm"><?php echo @$bs[$v['status']];?></div>
									</div>
								</div>
							<?php } ?>
						<?php } else { ?>
							<p><?php __('dash_no_enquiries');?></p>
						<?php } ?>
					</div>
				</div>
			</div><!-- /.col-lg-4 -->

			<div class="col-lg-4">
				<div class="ibox float-e-margins">
					<div class="ibox-content ibox-heading clearfix">
						<h3><?php __('dash_quick_links');?></h3>
					</div>

					<div class="ibox-content inspinia-timeline">
						<?php
						if(pjAuth::factory('pjAdminBookings', 'pjActionIndex')->hasAccess())
						{
							?>
							<p><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionIndex"><?php __('dash_view_enquiries'); ?></a></p>
							<?php
						}
						if(pjAuth::factory('pjAdminBookings', 'pjActionIndex')->hasAccess())
						{
							?>
							<p><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionIndex&amp;date=<?php echo date("Y-m-d");?>"><?php __('dash_link_reservations_today'); ?></a></p>
							<?php
						}
						if(pjAuth::factory('pjAdminBookings', 'pjActionCreate')->hasAccess())
						{
							?>
							<p><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionCreate"><?php __('dash_add_enquiry'); ?></a></p>
							<?php
						}
						?>
						<p><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOptions&amp;action=pjActionPreview" target="_blank"><?php __('dash_open_frontend'); ?></a></p>
					</div>
				</div>
			</div><!-- /.col-lg-4 -->
		</div><!-- /.row -->
	</div><!-- /.wrapper wrapper-content -->
<?php  } ?>

<!-- Booking Detail Modal -->
<div id="bookingModal" class="booking-modal">
    <div class="booking-modal-content">
        <span class="booking-close" onclick="closeBookingModal()">&times;</span>
        <div id="bookingModalContent"></div>
    </div>
</div>

<style>
/* Background overlay */
.booking-modal {
    display: none;
    position: fixed;
    z-index: 9999;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
}

/* Modal box */
.booking-modal-content {
    background: #fff;
    margin: 4% auto;
    padding: 20px;
    width: 650px;
    border-radius: 8px;
    position: relative;
}

/* Close button */
.booking-close {
    position: absolute;
    right: 15px;
    top: 10px;
    cursor: pointer;
    font-size: 22px;
    font-weight: bold;
}

/* Mobile / iPhone */
@media (max-width: 768px) {
    .booking-modal-content {
        width: 90%;
    }
}

@media (max-width: 480px) {
    .booking-modal-content {
        width: 95%;
        padding: 15px;
    }
}

@media (max-width: 768px) {

    .fc-toolbar-title {
        font-size: 16px !important;
    }

    .fc-header-toolbar {
        flex-wrap: wrap;
        gap: 4px;
    }

    .fc-header-toolbar button {
        padding: 6px 8px !important;
        font-size: 12px;
    }

    .fc-daygrid-day-number {
        font-size: 12px;
    }

    .fc-event {
        font-size: 12px;
    }

    /* LIST view looks best on mobile */
    .fc-list-event-title {
        font-size: 13px;
        line-height: 1.4;
    }

    div#calendar .fc-header-toolbar button span.fc-icon {
    line-height: 10px;
	}
}
</style>

<script>

function editBooking(id) {
    window.location.href = `index.php?controller=pjAdmin&action=pjActionDriverUpdateEvents&id=${id}`;
	}

function viewBooking(id) {
    window.location.href = `index.php?controller=pjAdmin&action=pjActionDriverViewEvents&id=${id}`;
	}

function closeBookingModal() {
    document.getElementById("bookingModal").style.display = "none";
}

function printBooking(id) {
    window.location.href = `index.php?controller=pjAdminBookings&action=pjActionPrint&id=${id}`;
	}
	
</script>

