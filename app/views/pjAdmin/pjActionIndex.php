<?php
$titles = __("error_titles", true);
$bodies = __("error_bodies", true);
$today = pjDateTime::formatDate(
    date("Y-m-d"),
    "Y-m-d",
    $tpl["option_arr"]["o_date_format"]
);
$months = __("months", true);
ksort($months);
$short_days = __("short_days", true);
$bs = __("booking_statuses", true);
$ps = __("payment_statuses", true);
$tt = __("time_type", true);
$get = $controller->_get->raw();
$set = isset($get["group"]) && !empty($get["group"]) ? $get["group"] : "daily";
$auth = pjAuth::factory();
$roleId = $auth->getRoleId();
?>


<?php if($roleId == 4 || $roleId == '4') { ?>
	<div id='calendar'></div>

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
	<!-- ================= Role Id 4 start here ================= -->

	<div id="bookingModal" class="booking-modal">
		<div class="booking-modal-content">
			<span class="booking-close" onclick="closeBookingModal()">&times;</span>
			<div id="bookingModalContent"></div>
		</div>
	</div>

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
    <!-- ================= Role Id 4 end here ================= -->
<?php } elseif ($roleId == 5 || $roleId == '5'){?>

<?php } else {?>

	<!-- DASHBOARD CONTENT -->

	<div id="datePickerOptions" style="display:none;" 
		data-wstart="<?php echo (int) $tpl["option_arr"]["o_week_start"]; ?>" 
		data-format="<?php echo $tpl["date_format"]; ?>" 
		data-months="<?php echo implode("_", $months); ?>" 
		data-days="<?php echo implode("_", $short_days); ?>">
	</div>

	<form method="get" action="" class="form-horizontal frm-filter" style="display: flex; flex-wrap: nowrap; gap: 0;margin-bottom: 20px;margin-top: 20px;">
		<input type="hidden" name="group" value="<?= htmlspecialchars($set) ?>">
		<input type="hidden" name="analysis" value="<?= isset($get["analysis"])
		? htmlspecialchars($get["analysis"])
		: "date" ?>">
			<div style="flex: 1; margin: 0;">
				<input type="text" name="from_date" id="from_date" class="form-control datetimepick_from" placeholder="From" value="<?php echo isset(
					$get["from_date"]
				)
					? htmlspecialchars($get["from_date"])
					: $tpl["filter_from"]; ?>"  readonly>
			</div>
			<div style="flex: 1; margin: 0;">
				<input type="text" name="to_date" id="to_date" class="form-control datetimepick_to" placeholder="To" value="<?php echo isset(
					$get["to_date"]
				)
					? htmlspecialchars($get["to_date"])
					: $tpl["filter_to"]; ?>" readonly>
			</div>
			<div style="flex: 1; margin: 0;">
				<select name="booking_status" class="form-control">
					<option value="">-- <?php __("lblAllStatus"); ?> --</option>
					<?php foreach ($bs as $k => $v) { ?>
					<option value="<?php echo $k; ?>" <?php echo isset($get["booking_status"]) && $get["booking_status"] === $k ? "selected"    : ""; ?>>
					<?php echo pjSanitize::html($v); ?>
					<?php } ?>
				</select>
			</div>
			<div style="flex: 1; margin: 0;">
				<select name="payment_status" class="form-control">
					<option value="">-- <?php __("lblALlPayments"); ?> --</option>
					<?php foreach ($ps as $k => $v) { ?>
					<option value="<?php echo $k; ?>" <?php echo isset($get["payment_status"]) && $get["payment_status"] === $k ? "selected"    : ""; ?>>
					<?php echo pjSanitize::html($v); ?>
					<?php } ?>
				</select>
			</div>
			<div style="flex: 1; margin: 0;">
				<select name="time_type" class="form-control">
					<option value="">-- <?php __("lblTimeType"); ?> --</option>
					<?php foreach ($tt as $k => $v) { ?>
					<option value="<?php echo $k; ?>" <?php echo isset($get["time_type"]) && $get["time_type"] === $k ? "selected"    : ""; ?>>
					<?php echo pjSanitize::html($v); ?>
					<?php } ?>
				</select>
			</div>

			<div style="flex: 1; margin: 0;">
				<select name="city" class="form-control">
					<option value="">-- City --</option>
					<?php foreach($tpl['cities'] as $city){ ?>
							<option value="<?= pjSanitize::html($city['name']) ?>"
							<?= isset($get['city']) && $get['city']==$city['name'] ? 'selected':'' ?>>
							<?= pjSanitize::html($city['name']) ?>
						</option>
					<?php } ?>
				</select>
			</div>

			<div style="flex: 1; margin: 0;">
				<select name="fleet_id" class="form-control">
					<option value="">Vehicle Type</option>

					<?php foreach ($tpl['fleets'] as $fleet): ?>
						<option value="<?php echo $fleet['id']; ?>"
							<?php echo (isset($get['fleet_id']) && $get['fleet_id'] == $fleet['id']) ? 'selected' : ''; ?>>
							<?php echo pjSanitize::html($fleet['fleet']); ?>
						</option>
					<?php endforeach; ?>

				</select>
			</div>
			<div class="col-md-1">
				<button type="submit" class="btn btn-primary"><i class="fa fa-search"></i></button>
			</div>
	</form>

	<div id="dashboardContent">
		<!-- ================= Summary ================= -->
		<div class="kpi-row">
			<div class="kpi-col">
				<div class="kpi-card  kpi-total-bookings">
					<div class="kpi-top"><i class="fa fa-calendar-check-o"></i><h4><?php __(
						"dash_total_bookings"
					); ?></h4></div>
					<h2><?php echo $tpl["total_reservations"]; ?></h2>
				</div>
			</div>
			<div class="kpi-col">
				<div class="kpi-card kpi-total-revenue">
					<div class="kpi-top"><i class="fa fa-money"></i><h4><?php __(
						"dash_total_revenue"
					); ?></h4></div>
					<h2><?php echo $tpl["total_revenue"]; ?></h2>
				</div>
			</div>
			<div class="kpi-col">
				<div class="kpi-card kpi-completed-bookings">
					<div class="kpi-top"><i class="fa fa-check-circle"></i><h4><?php __(
						"dash_completed_bookings"
					); ?></h4></div>
					<h2><?php echo $tpl["completed_bookings"]; ?></h2>
				</div>
			</div>
			<div class="kpi-col">
				<div class="kpi-card  kpi-cancelled-bookings">
					<div class="kpi-top"><i class="fa fa-times-circle"></i><h4><?php __(
						"dash_cancelled_bookings"
					); ?></h4></div>
					<h2><?php echo $tpl["cancelled_bookings"]; ?></h2>
				</div>
			</div>
			<div class="kpi-col">
				<div class="kpi-card kpi-new-customers">
					<div class="kpi-top"><i class="fa fa-user-plus"></i><h4><?php __(
						"dash_new_customers"
					); ?></h4></div>
					<h2><?php echo $tpl["new_customers"]; ?></h2>
				</div>
			</div>
			<div class="kpi-col">
				<div class="kpi-card kpi-total-customers">
					<div class="kpi-top"><i class="fa fa-users"></i><h4><?php __(
						"dash_total_customers"
					); ?></h4></div>
					<h2><?php echo $tpl["total_customers"]; ?></h2>
				</div>
			</div>
		</div>

		<!-- ================= CHARTS ================= -->
		<div class="row m-t-lg">
			<div class="col-lg-6">
				<div class="ibox">
					<div class="ibox-title" style="display:flex;justify-content:space-between;align-items:center;">
						<h4><?php __("dash_revenue_trend"); ?></h4>
						<div class="btn-group btn-group-sm" id="revenueTabs">
							<button class="btn btn-white <?= $set == "daily"
								? "active"
								: "" ?>" data-type="daily"><?php __("dash_daily"); ?></button>
							<button class="btn btn-white <?= $set == "weekly"
								? "active"
								: "" ?>" data-type="weekly"><?php __("dash_weekly"); ?></button>
							<button class="btn btn-white <?= $set == "monthly"
								? "active"
								: "" ?>" data-type="monthly"><?php __("dash_monthly"); ?></button>
						</div>
					</div>
					<div class="ibox-content">
						<canvas id="revenueChart"></canvas>
					</div>
				</div>
			</div>

			<div class="col-lg-6">
				<div class="ibox">
					<div class="ibox-title"><h4><?php __(
						"dash_booking_per_day"
					); ?></h4></div>
					<div class="ibox-content">
						<canvas id="bookingChart"></canvas>
					</div>
				</div>
			</div>
		</div>
		<div class="row m-t-lg">
			<div class="col-lg-6">
				<div class="ibox">
					<div class="ibox-title" style="display:flex;justify-content:space-between;align-items:center;">
						<h4><?php __("dash_peek_booking"); ?></h4>

						<?php $analysis = isset($get["analysis"]) ? $get["analysis"] : "date"; ?>

						<div class="btn-group btn-group-sm" id="bookingTabs">
							<button class="btn btn-white <?= $analysis == "date" ? "active" : "" ?>" data-type="date">
								<?php __("lblDate"); ?>
							</button>
							<button class="btn btn-white <?= $analysis == "hour" ? "active": "" ?>" data-type="hour">
								<?php __("lblOptionHours"); ?>
							</button>
						</div>
					</div>
					<div class="ibox-content">
						<canvas id="peakBookingChart"></canvas>
					</div>
				</div>
			</div>
			<div class="col-lg-3">
				<div class="ibox">
					<div class="ibox-title"><h4><?php __(
						"dash_booking_status"
					); ?></h4></div>
					<div class="ibox-content">
						<canvas id="statusChart"></canvas>
					</div>
				</div>
			</div>

			<div class="col-lg-3">
				<div class="ibox">
					<div class="ibox-title"><h4><?php __(
						"dash_payment_methods"
					); ?></h4></div>
					<div class="ibox-content">
						<canvas id="paymentChart"></canvas>
					</div>
				</div>
			</div>
		</div>
		<div class="row m-t-lg">
			<div class="col-lg-3">
				<div class="ibox">
					<div class="ibox-title"><h4><?php __("dash_revenue_vehicle_type"); ?></h4></div>
					<div class="ibox-content">
						<canvas id="revenue_by_vehicle"></canvas>
					</div>
				</div>
			</div>
		</div>
	</div> 
	<!-- end dashboardContent -->
	<!-- ================= STYLES ================= -->
	<style>
		/* ROW */
		/* Make all charts same size */
		.ibox-content{
			height: 260px;
			position: relative;
		}

		.ibox-content canvas{
			width: 100% !important;
			height: 220px !important;
		}
		.kpi-row {
			display: flex;
			flex-wrap: wrap;
			gap: 15px;
		}

		/* COLUMN */
		.kpi-col {
			flex: 1 1 200px; /* grow, shrink, base width */
		}

		/* CARD */
		.kpi-card {
			background: #ffffff;
			border-radius: 12px;
			box-shadow: 0 4px 15px rgba(0,0,0,0.1);
			padding: 20px;
			transition: transform 0.2s, box-shadow 0.2s;
		}

		.kpi-card:hover {
			transform: translateY(-3px);
			box-shadow: 0 8px 20px rgba(0,0,0,0.15);
		}

		/* ICON + TITLE */
		.kpi-top {
			display: flex;
			align-items: center;
			gap: 10px;
			margin-bottom: 15px;
		}

		.kpi-top i {
			font-size: 28px; /* bigger icon */
			color: #9aa0a6;
		}

		/* KPI Colors */
		.kpi-total-bookings i { color: #3498db; }
		.kpi-total-revenue i { color: #2ecc71; }
		.kpi-completed-bookings i { color: #1abc9c; }
		.kpi-cancelled-bookings i { color: #e74c3c; }
		.kpi-new-customers i { color: #e67e22; }
		.kpi-total-customers i { color: #9b59b6; }
		/* NUMBER */
		.kpi-card h2 {
			/* font-size: 32px; */
			font-weight: 500;
			margin: 0;
			text-align:center
		}
		.kpi-card h4 {
			/* font-size: 13px; */
			font-weight: 600;
			margin: 0;
			color: #555;
		}

		/* CHANGE % (if used) */
		.kpi-change {
			font-size: 13px;
			font-weight: 600;
			text-align: center;
		}

		.kpi-change.up {
			color: #1ab394;
		}

		.kpi-change.down {
			color: #e74c3c;
		}

		/* RESPONSIVE */
		@media (max-width: 1200px) {
			.kpi-col {
				flex: 1 1 45%; /* two per row */
			}
		}

		@media (max-width: 768px) {
			.kpi-col {
				flex: 1 1 100%; /* one per row */
			}
		}
	</style>

	<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

	<script>
	window.dashboardData = {
		revenueTrend: <?= json_encode($tpl["revenue_trend"]) ?>,
		statusChart: <?= json_encode($tpl["status_chart"]) ?>,
		paymentChart: <?= json_encode($tpl["payment_chart"]) ?>,
		bookingsPerDay: <?= json_encode($tpl["bookings_per_day"]) ?>,
		peakBookingChart: <?= json_encode($tpl["booking_analysis"]) ?>,
		revenueByVehicleChart: <?= json_encode($tpl["revenue_by_vehicle"]) ?>
	};
	</script>
<?php } ?>
