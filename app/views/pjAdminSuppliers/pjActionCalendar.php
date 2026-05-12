<?php 
$today = pjDateTime::formatDate(date('Y-m-d'), 'Y-m-d', $tpl['option_arr']['o_date_format']);
$months = __('months', true);
ksort($months);
$bs = __('booking_statuses', true); 

$auth = pjAuth::factory();
$roleId = $auth->getRoleId();

// echo "<pre>"; print_r($tpl['has_update']); echo "</pre>";
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
			    },
			    timeGridDay: {
			        slotDuration: "00:05:00",        
			        slotLabelInterval: "00:05:00",   
			        slotMinTime: "00:00:00",
			        slotMaxTime: "24:00:00"
			    },
			    timeGridWeek: {
			        slotDuration: "00:05:00",
			        slotLabelInterval: "00:05:00",
			        slotMinTime: "00:00:00",
			        slotMaxTime: "24:00:00"
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
				<div><b>Driver</b>: ${driverName}</div>

			</div>
		`;
		return { html: html };
		},

		eventClick: function(info) {



		info.jsEvent.preventDefault();

		let event = info.event.extendedProps;

		let isCompleted = event.status.toLowerCase() === "completed";

			let bookingCompleted = `<button
				${isCompleted ? "disabled" : `onclick="bookingCompleted(${info.event.id})"`}
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

		console.log('event.extendedProps.extras', info.event.extendedProps.extras);

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

				<div class="driver-assign-wrapper">

				<h4>Assign Driver</h4>

				<select id="popup_driver_id" class="form-control driver-select">

					<option value="">Choose Driver</option>

					<?php foreach ($tpl['deriver_ids'] as $v) { ?>
						<option value="<?php echo $v['id']; ?>"
							${event.driver_id == <?php echo $v['id']; ?> ? 'selected' : ''}>
							<?php echo stripslashes($v['first_name'].' '.$v['last_name']); ?>
						</option>
					<?php } ?>

				</select>

			</div>

			<div class="booking-action-buttons">

				<div class="left-action-buttons">

					${bookingCompleted}

					<?php if($tpl['has_update']) { ?>
					<button onclick="editBooking(${info.event.id})"
						style="background:#007bff;color:#fff;">
						Edit Booking
					</button>
					<?php } ?>

					<button onclick="printBooking(${info.event.id})"
						style="background:#007bff;color:#fff;">
						Print
					</button>

				</div>

				<button 
					class="assign-driver-btn"
					onclick="saveDriverAssignment(${info.event.id})">
					Assign Driver
				</button>

			</div>`;

		document.getElementById("bookingModalContent").innerHTML = html;
		document.getElementById("bookingModal").classList.add("active");
	}

	});
	calendar.render();
	});
</script>
<style>

  body {
    margin: 10px 10px;
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

</style>
<div class="row">
	<div class="col-md-10 col-xs-10" style="padding: 5px;"> </div>
</div>
<div id='calendar'></div>
	<!-- Booking Detail Modal -->
<div id="bookingModal" class="booking-modal">
    <div class="booking-modal-content">
        <span class="booking-close" onclick="closeBookingModal()">&times;</span>
        <div id="bookingModalContent"></div>
    </div>
</div>

<div id="toast"
     style=" position:fixed; top:20px; right:20px; background:#28a745; color:#fff; padding:12px 18px;  border-radius:6px; box-shadow:0 4px 10px rgba(0,0,0,0.15); opacity:0;  transform:translateY(-10px); transition:all .3s ease; z-index:9999; ">
</div>

<style>

	/* =========================
	BOOKING MODAL
	========================= */

	/* Background overlay */
	.booking-modal {
		display: none;
		position: fixed;
		z-index: 99999;
		left: 0;
		top: 0;
		width: 100%;
		height: 100%;

		background: rgba(0,0,0,0.6);

		overflow-y: auto;
		padding: 20px;
		box-sizing: border-box;
	}

	/* ACTIVE STATE */
	.booking-modal.active {
		display: flex;
		align-items: center;
		justify-content: center;
	}

	/* Modal content */
	.booking-modal-content {
		background: #fff;
		width: 100%;
		max-width: 750px;

		border-radius: 10px;
		position: relative;

		padding: 25px;

		box-sizing: border-box;

		max-height: 95vh;
		overflow-y: auto;

		animation: modalFade .25s ease;
	}

	/* Animation */
	@keyframes modalFade {
		from {
			opacity: 0;
			transform: translateY(-15px);
		}
		to {
			opacity: 1;
			transform: translateY(0);
		}
	}

	/* Close button */
	.booking-close {
		position: absolute;
		right: 15px;
		top: 10px;

		font-size: 28px;
		font-weight: bold;

		cursor: pointer;
		color: #333;
	}

	/* Text */
	.booking-modal-content h3,
	.booking-modal-content h4 {
		margin-top: 0;
	}

	.booking-modal-content p {
		margin-bottom: 8px;
		line-height: 1.5;
	}

	/* Inputs */
	.booking-modal-content select {
		width: 100%;
		padding: 10px;
		border: 1px solid #ddd;
		border-radius: 6px;
	}

	/* Buttons */
	.booking-modal-content button {
		padding: 10px 15px;
		border: none;
		border-radius: 6px;
		cursor: pointer;
		margin-top: 8px;
	}

	/* Mobile Responsive */
	@media (max-width: 768px) {

		.booking-modal {
			padding: 10px;
		}

		.booking-modal-content {
			max-width: 100%;
			padding: 18px;
		}

		.booking-modal-content .row {
			display: block;
		}

		.booking-modal-content .col-sm-6,
		.booking-modal-content .col-sm-12 {
			width: 100%;
			margin-bottom: 15px;
		}

		.booking-modal-content button {
			width: 100% !important;
			float: none !important;
			margin-top: 10px;
		}

		.booking-close {
			font-size: 24px;
		}
	}

	/* Extra Small Devices */
	@media (max-width: 480px) {

		.booking-modal-content {
			padding: 15px;
			border-radius: 8px;
		}

		.booking-modal-content h3 {
			font-size: 18px;
		}

		.booking-modal-content h4 {
			font-size: 16px;
		}

		.booking-modal-content p,
		.booking-modal-content li {
			font-size: 13px;
		}	
	}

	/* =========================
	DRIVER SECTION
	========================= */

	.driver-assign-wrapper {
		margin-top: 20px;
	}

	/* dropdown full width */
	.driver-select {
		width: 100% !important;
		height: 45px !important;
		padding: 0 12px !important;
		font-size: 14px;
		border-radius: 6px;
		border: 1px solid #ccc;
	}

	/* bottom action buttons row */
		.booking-action-buttons {
		display: flex;
		justify-content: space-between;
		align-items: center;
		gap: 10px;
		margin-top: 20px;
	}

	.left-action-buttons {
		display: flex;
		gap: 10px;
		flex-wrap: wrap;
	}

	/* Mobile */
	@media (max-width: 768px) {

		.booking-action-buttons {
			flex-direction: column;
			align-items: stretch;
		}

		.left-action-buttons {
			width: 100%;
			flex-direction: column;
		}

		.left-action-buttons button,
		.assign-driver-btn {
			width: 100%;
		}
	}
	.assign-driver-btn:hover {
		background: #218838 !important;
		color:#fff !important;
	}

</style>

<script>
	function editBooking(id) {
		window.location.href = `index.php?controller=pjAdminBookings&action=pjActionUpdate&id=${id}`;
		}

	function printBooking(id) {
		window.location.href = `index.php?controller=pjAdminBookings&action=pjActionPrint&id=${id}`;
		}

	function closeBookingModal() {
		document.getElementById("bookingModal").classList.remove("active");
	}

	/* Close modal on outside click */
	window.onclick = function(event) {

		let modal = document.getElementById("bookingModal");

		if (event.target === modal) {
			modal.classList.remove("active");
		}
	}

	function bookingCompleted(id) {
		window.location.href = `index.php?controller=pjAdmin&action=pjActionDriverUpdateEvents&id=${id}`;
		}

	function showToast(message, type = 'success') {

		const toast = document.getElementById('toast');

		toast.textContent = message;
		toast.style.background = type === 'success' ? '#28a745' : '#dc3545';

		toast.style.opacity = '1';
		toast.style.transform = 'translateY(0)';

		setTimeout(() => {
			toast.style.opacity = '0';
			toast.style.transform = 'translateY(-10px)';
		}, 2500);
	}


	function saveDriverAssignment(bookingId)
	{
		let driverId = document.getElementById('popup_driver_id').value;

		if (!driverId) {
			alert('Please select a driver');
			return;
		}

		fetch('index.php?controller=pjAdminBookings&action=pjActionAssignDriver', {
			method: 'POST',
			headers: {'Content-Type': 'application/x-www-form-urlencoded'},
			body: 'booking_id=' + bookingId + '&driver_id=' + driverId
		})
		.then(res => res.json())
		.then(resp => {
			if (resp.status === 'OK') {
				showToast('Driver assigned successfully');
				document.getElementById("bookingModal").classList.remove("active");

				setTimeout(() => {
					location.reload();
				}, 1200);
				
				calendar.refetchEvents();
			} else {
				alert('Failed to assign driver');
			}
		});
	}

</script>