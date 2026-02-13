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
				<div>
					<h4>Assign Driver</h4>
					<select id="popup_driver_id" class="form-control" style="margin-bottom:10px;">
						<option value=""> Choose Driver </option>
						<?php foreach ($tpl['deriver_ids'] as $v) { ?>
							<option value="<?php echo $v['id']; ?>"
								${event.driver_id == <?php echo $v['id']; ?> ? 'selected' : ''}>
								<?php echo stripslashes($v['first_name'].' '.$v['last_name']); ?>
							</option>
						<?php } ?>
					</select>
					<button onclick="saveDriverAssignment(${info.event.id})"
							style="width:20%;padding:8px;background:#28a745;color:#fff;border:none;border-radius:4px; float: right;">
						Assign Driver
					</button>
				</div>
			<br>
			${bookingCompleted}

			<?php if($tpl['has_update']) { ?>
			<button onclick="editBooking(${info.event.id})" 
					style="padding:8px 15px; background:#007bff; color:#fff; border:none; border-radius:4px; cursor:pointer;">
				Edit Booking
			</button>
				<?php } ?>
			&nbsp;
			<button onclick="printBooking(${info.event.id})" 
					style="padding:8px 15px; background:#007bff; color:#fff; border:none; border-radius:4px; cursor:pointer;">
				Print
			</button>`;

		document.getElementById("bookingModalContent").innerHTML = html;
		document.getElementById("bookingModal").style.display = "block";
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
	<div class="col-sm-2 col-xs-12 align-items-end" style="padding: 10px; text-align: right;">
		<a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&action=pjActionIndex" class="btn btn-primary"><?php __('infoReservationListTitle');?></a>
	</div>
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
    window.location.href = `index.php?controller=pjAdminBookings&action=pjActionUpdate&id=${id}`;
	}

function printBooking(id) {
    window.location.href = `index.php?controller=pjAdminBookings&action=pjActionPrint&id=${id}`;
	}

function closeBookingModal() {
    document.getElementById("bookingModal").style.display = "none";
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
            document.getElementById("bookingModal").style.display = "none";

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



