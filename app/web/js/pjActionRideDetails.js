var jQuery_1_8_2 = jQuery_1_8_2 || $.noConflict();

(function ($, undefined) {

	$(function () {

		"use strict";

		var 

			$frmCreateBooking = $('#frmCreateBooking'),

			$frmUpdateBooking = $('#frmUpdateBooking'),

			$modalCancellation = $("#modalCancellation"),

			$modalConfirmation = $("#modalConfirmation"),

			$modalSmsConfirmation = $("#modalSmsConfirmation"),

			datagrid = ($.fn.datagrid !== undefined),

			datetimeOptions = null;;

	

		

		if($frmUpdateBooking.length > 0)

		{

			var passengers = parseInt($( "#passengers" ).attr('data-value'), 10);

			var luggage = parseInt($( "#luggage" ).attr('data-value'), 10);

			if(passengers > 0)

			{

				$( "#passengers" ).trigger("touchspin.updatesettings", {max: passengers});

				$( "#passengers" ).on('touchspin.on.startspin', function () {calcPrice();});

			}

			if(luggage > 0)

			{

				$( "#luggage" ).trigger("touchspin.updatesettings", {max: luggage});

			}

			if($('.i-checks').length > 0)

			{

				$('.i-checks').iCheck({

		            checkboxClass: 'icheckbox_square-green',

		            radioClass: 'iradio_square-green'

		        });

				$('input').on('ifChanged', function (event) { $(event.target).trigger('change'); });

			}

		}

		

		// function calcDistance() {
		// 	var start = document.getElementById('pickup_address').value;
		// 	var end = document.getElementById('return_address').value;
		// 	if(start != '' && end != '')
		// 	{
		// 		var request = {
		// 			origin: start,
		// 		    destination: end,
		// 		    travelMode: 'DRIVING'
		// 		};
		// 		directionsService.route(request, function(response, status) {
		// 			if (status == google.maps.DirectionsStatus.OK) {
		// 				var distanceinkm = parseInt(response.routes[0].legs[0].distance.value / 1000, 10);
		// 				var durationInMin = parseInt(response.routes[0].legs[0].duration.value / 60, 10);
		// 				$('#distance').val(distanceinkm);
		// 				$('#pjTbsDurationInMinFiled').val(durationInMin);
		// 		    }
		// 		});
		// 	}else{
		// 		$('#distance').val("");
		// 		$('#pjTbsDurationInMinFiled').val("");
		// 	}

		// 	calcPrice();
		// }

		function calcDistance() {
		    var start = document.getElementById('pickup_address').value;
		    var end = document.getElementById('return_address').value;
		    console.log(start, 'Admin pickup');
		    console.log(end, 'Admin retun');
		    if (start !== '' && end !== '') {
		        var request = {
		            origin: start,
		            destination: end,
		            travelMode: google.maps.TravelMode.DRIVING
		        };

		        directionsService.route(request, function (response, status) {
		            if (status === google.maps.DirectionsStatus.OK) {
		                var leg = response.routes[0].legs[0];

		                // var distanceInKm = (leg.distance.value / 1000, 10); // exact km
		                // var distanceInKm = (leg.distance.value / 1000).toFixed(2);
		                var distanceInKm = Math.round(leg.distance.value / 1000);
		                var durationText = leg.duration.text; // ✅ example: "1 hour 23 mins"
		                var durationInMin = Math.round(leg.duration.value / 60); // still keep numeric

		                $('#distance').val(distanceInKm);
		                $('#pjTbsDurationInMinFiled').val(durationInMin); // readable time
		                // $('#durationInMinHidden').val(durationInMin); // optional show hours
		            }
		        });
		    } else {
		        $('#distance').val('');
		        $('#pjTbsDurationInMinFiled').val('');
		    }

		    calcPrice();
		}

		if ($('#datePickerOptions').length)
			{
				var currentDate = new Date(),
					$optionsEle = $('#datePickerOptions');
				moment.updateLocale('en', {
					week: { dow: parseInt($optionsEle.data('wstart'), 10) },
					months: $optionsEle.data('months').split("_"),
					weekdaysMin: $optionsEle.data('days').split("_")
				});
				var datetimeOptions = {
					format: $optionsEle.data('format'),
					locale: moment.locale('en'),
					allowInputToggle: true,
					ignoreReadonly: true,
					useCurrent: false
				};

				var dateOnlyOptions = {
					format: 'YYYY-MM-DD',
					locale: moment.locale('en'),
					allowInputToggle: true,
					ignoreReadonly: true,
					useCurrent: false
				};
				$('.datetimepick_from').datetimepicker(dateOnlyOptions);
				$('.datetimepick_to').datetimepicker(dateOnlyOptions);

				$('#from_date').on('click', function () {
					$(this).data("DateTimePicker").show();
				});

				$('#to_date').on('click', function () {
					$(this).data("DateTimePicker").show();
				});

				$("#from_date").on("dp.change", function (e) {
					$('#to_date').data("DateTimePicker").minDate(e.date);
				});

				$("#to_date").on("dp.change", function (e) {
					$('#from_date').data("DateTimePicker").maxDate(e.date);
				});
			}

		if ($frmCreateBooking.length > 0 || $frmUpdateBooking.length > 0) 

		{

			var directionsService = new google.maps.DirectionsService();	

			const airportNames = [
				'innsbruck airport', 'innsbruck flughafen',
				'Salzburg airport', 'salzburg flughafen', 'Flughafen Salzburg (SZG)',
				'Innsbrucker Bundesstraße', 'Salzburg', 'salzburg',
				'Flughafen Salzburg', 'salzburg flughafen',
				'munich airport', 'münchen flughafen', 'muenchen flughafen',
				'memmingen airport', 'memmingen flughafen',
				'zurich airport', 'zürich flughafen', 'zuerich flughafen',
				'engadin airport', 'samedan st. moritz flughafen',
				'verona airport', 'verona flughafen',
				'bolzano airport', 'bozen flughafen',
				'munich international airport',
				'munich international airport (muc)',
				'munich airport muc',
				'munich international airport muc',
				'münchen flughafen muc',
				'münchen international flughafen muc',
				'muenchen flughafen muc',
				'muenchen international flughafen muc'
			];


			// Initially hide the block

			$('.airlineIncluded').hide();
			$('.returnDateTime').hide();
			$('.airlineIncludedArival').hide();

			if($('#pickup_address').length > 0)

			{

				var autocomplete_pickup = new google.maps.places.Autocomplete($('#pickup_address')[0], {
					types: ["geocode", "establishment"]
				});

				
				var pickup_field = document.getElementById('pickup_address');
			
				google.maps.event.addDomListener(pickup_field, 'keydown', function(e) { 
				    if (e.keyCode == 13) { 
				        e.preventDefault(); 
				    }
				});


				google.maps.event.addListener(autocomplete_pickup, 'place_changed', function() {
					    var place = autocomplete_pickup.getPlace();
						var pickupName = place.name ? place.name.toLowerCase() : '';
						var pickupAddress = place.formatted_address ? place.formatted_address.toLowerCase() : '';
						var $placess = autocomplete_pickup.getPlace();
						var matchFound = false;

						var $from_city = null;
						console.log($placess, "placess");
						// First, prioritize the best city value
						for (var $component of $placess.address_components) {
						    var $types = $component.types;

						     if ($types.includes("neighborhood")) {
						    	console.log("neighborhood");
						        $from_city = $component.long_name;
						        break; // Stop as soon as preferred city found
						    }
						    
						    if ($types.includes("establishment")) {
						    	console.log("establishment");
						        $from_city  = $component.long_name;
						        break; // Stop as soon as preferred city found
						    }

						    if (!$from_city  && $types.includes("administrative_area_level_3")) {
						    	console.log("administrative_area_level_3");
						        $from_city  = $component.long_name;
						    }

						    if ($types.includes("locality")) {
						    	console.log("locality");
						        $from_city = $component.long_name;
						        break; // Stop as soon as preferred city found
						    }

						    
						    if (!$from_city && $types.includes("administrative_area_level_2")) {
						    	console.log("administrative_area_level_2");
						        $from_city = $component.long_name;
						    }
						    if (!$from_city && $types.includes("administrative_area_level_1")) {
						    	console.log("administrative_area_level_1");
						        $from_city = $component.long_name;
						    }
						}
						// Set the city input once
						if ($from_city) {
						    $('#from_city').val($from_city);
						}

						 // Check if booking type is selected

						var booking_type = $('#return_status').val(); // adjust selector if needed

						if (!booking_type || booking_type === "") {

							alert(" Please select a booking type before choosing a Pick-up address.");

							$('#return_status').focus();
							$('#pickup_address').val("");
							return; // stop execution here
						}

						// Check if any airport name appears in either name or address

						for (var i = 0; i < airportNames.length; i++) {
							if (pickupName.includes(airportNames[i]) || pickupAddress.includes(airportNames[i])) {
								matchFound = true;
								break;
							}
						}

						if (matchFound) {

							console.log(matchFound, 'okkkk');
							if(booking_type == '0'){
								
								$('.airlineIncludedArival').show();
							}else{
								
							 $('.airlineIncluded').show(); // show block
							 $('.airlineIncludedArival').show();
							}
						
						} else {
							$('.airlineIncluded').hide(); // hide block
							$('.airlineIncludedArival').hide();
						}

					calcDistance();

				});

			}

			if($('#return_address').length > 0)

			{

				var autocomplete_return = new google.maps.places.Autocomplete($('#return_address')[0], {
					types: ["geocode", "establishment"]
				});
				var return_field = document.getElementById('return_address');
				google.maps.event.addDomListener(return_field, 'keydown', function(e) { 
				    if (e.keyCode == 13) { 
				        e.preventDefault(); 
				    }
				});

				google.maps.event.addListener(autocomplete_return, 'place_changed', function() {

					var place = autocomplete_return.getPlace();
					var pickupName = place.name ? place.name.toLowerCase() : '';
					var pickupAddress = place.formatted_address ? place.formatted_address.toLowerCase() : '';
					var matchFound = false;

					var $placess = autocomplete_return.getPlace();
					console.log($placess, '$placess');
					var $to_city = null;
					// First, prioritize the best city value
						for (var $component of $placess.address_components)
						{
						    var $types = $component.types;
							 if ($types.includes("neighborhood")) {
						        $to_city = $component.long_name;
						        break; // Stop as soon as preferred city found
						    }

						     if ($types.includes("establishment")){
						        $to_city = $component.long_name;
						        break; // Stop as soon as preferred city found
						    }
							
							if (!$to_city && $types.includes("administrative_area_level_3")) {
						        $to_city = $component.long_name;
						        break; // Stop as soon as preferred city found
						    }

						    if ($types.includes("locality")) {
						    	console.log("locality");
						        $to_city = $component.long_name;
						        break; // Stop as soon as preferred city found
						    }
						 
						 	
						    if (!$to_city && $types.includes("administrative_area_level_2")) {
						        $to_city = $component.long_name;
						        break; // Stop as soon as preferred city found
						    }

						    if (!$to_city && $types.includes("administrative_area_level_1")) {
						    	console.log("administrative_area_level_1");
						        $to_city = $component.long_name;
						        break; // Stop as soon as preferred city found
						    }
						}
						// Set the city input once
						if ($to_city) {
						    $('#to_city').val($to_city);
						}

						 //  Check if booking type is selected

						var booking_type = $('#return_status').val(); // adjust selector if needed

						if (!booking_type || booking_type === "") {

							alert("Please select a booking type before choosing a Drop-off address.");

							$('#return_status').focus();

							$('#return_address').val("");

							return; // stop execution here

						}



					// Check if any airport name appears in either name or address

					for (var i = 0; i < airportNames.length; i++) {

						if (pickupName.includes(airportNames[i]) || pickupAddress.includes(airportNames[i])) {

							matchFound = true;

							break;

						}

					}



					if (matchFound) { 
						console.log('okk');
						$('.airlineIncluded').show(); // show block
						$('.airlineIncludedArival').show();
					}

					calcDistance();

				});

			}

			if($('#client_id').length == 0)

			{

				$('.clientRequired').addClass('required');

			}

			$.validator.addMethod('positiveNumber', function (value) { 

				return Number(value) >= 0;

			}, myLabel.positive_number);

			

			$.validator.addMethod('maximumNumber', function (value, element) { 

				var data = parseInt($(element).attr('data-value'), 10);

				if(Number(value) > data)

				{

					return false;

				}else{

					return true;

				}

			}, myLabel.max_number);

			

			$frmCreateBooking.validate({

				rules: {

					passengers: {

						positiveNumber: true,

						maximumNumber: true

					},

					luggage: {

						positiveNumber: true,

						maximumNumber: true

					},

					c_email: {

						email: true,

						remote: 'index.php?controller=pjAdminBookings&action=pjActionCheckEmail'

					}

				},

				messages: {

					c_email: {

						remote: myLabel.email_already_exist

					}

				},

				onkeyup: false,

				ignore: "",

				invalidHandler: function (event, validator) {

				    if (validator.numberOfInvalids()) {

				    	var $_id = $(validator.errorList[0].element, this).closest("div.tab-pane").attr("id");

				    	$('[data-toggle="tab"][href="#' + $_id + '"]' ).trigger( 'click' );

				    };

				},

			});

			$frmUpdateBooking.validate({

				rules:{

					"return_date":{

						required: function(){

							if($('#has_return').is(':checked'))

							{

								return true;

							}else{

								return false;

							}

						}

					},

					uuid: {

						required: true,

						remote: "index.php?controller=pjAdminBookings&action=pjActionCheckID&id=" + $frmUpdateBooking.find("input[name='id']").val()

					},

					passengers: {

						positiveNumber: true,

						maximumNumber: true

					},

					luggage: {

						positiveNumber: true,

						maximumNumber: true

					},

					c_email: {

						email: true,

						remote: {

							type: 'post',

							url: "index.php?controller=pjAdminBookings&action=pjActionCheckEmail",

							data:{

								c_email: function()

						        {

									if($frmUpdateBooking.find("input[name='new_client']").is(":checked"))

									{

										return $frmUpdateBooking.find("input[name='c_email']").val();	

									}else{

										return false;

									}

						        }

							}

						}

					}



				},

				messages:{

					uuid: {

						remote: myLabel.duplicated_id

					},

					c_email: {

						remote: myLabel.email_already_exist

					}

				},

				onkeyup: false,

				ignore: "",

				invalidHandler: function (event, validator) {

				    if (validator.numberOfInvalids()) {

				    	var $_id = $(validator.errorList[0].element, this).closest("div.tab-pane").attr("id");

				    	$('[data-toggle="tab"][href="#' + $_id + '"]' ).trigger( 'click' );

				    };

				},

			});

			if ($('#dateTimePickerOptions').length) {

				

	        	var currentDate = new Date(),

	        		$optionsEle = $('#dateTimePickerOptions');

	        	

		        moment.updateLocale('en', {

					week: { dow: parseInt($optionsEle.data('wstart'), 10) },

					months : $optionsEle.data('months').split("_"),

			        weekdaysMin : $optionsEle.data('days').split("_")

				});

		        datetimeOptions = {

						format: $optionsEle.data('format'),

						locale: moment.locale('en'),

						allowInputToggle: true,

						ignoreReadonly: true,

						useCurrent: false

					};

		        $('.datetimepick').datetimepicker(datetimeOptions);

		        

		        if($('.pjCrTimePicker').length > 0)

		        {

		        	$('.pjCrTimePicker').datetimepicker({

						format: $optionsEle.data('timeformat'),

						ignoreReadonly: true,

						allowInputToggle: true

					});	

		        }

	        }

			$(".field-int").TouchSpin({

	            verticalbuttons: true,

	            buttondown_class: 'btn btn-white',

	            buttonup_class: 'btn btn-white',

	            min: 1,

	            max: 4294967295

			});

			if ($(".select-item").length) {

	            $(".select-item").select2({

	                placeholder: myLabel.choose ,

	                allowClear: true

	            });

	        };

		}

		if ($("#grid").length > 0 && datagrid) {

			function formatExtras(val, obj) {
			    // val is the array of extras
			    if (!val || !val.length) return '—'; // show dash if no extras

			    // Map each extra to "Extra Name (Qty)"
			    return val.map(function(extra) {
			        return extra.extra_name + ' (' + extra.extra_value + ')';
			    }).join(', '); // join with comma
			}

			function formatStatus(val, obj) {

				if(val == 'confirmed')

				{

					return '<div class="btn bg-confirmed btn-xs no-margin"><i class="fa fa-check"></i> ' + myLabel.confirmed + '</div>';

				}else if(val == 'cancelled'){

					return '<div class="btn bg-cancelled btn-xs no-margin"><i class="fa fa-times"></i> ' + myLabel.cancelled + '</div>';

				}else if(val == 'pending'){

					return '<div class="btn bg-pending btn-xs no-margin"><i class="fa fa-exclamation-triangle"></i> ' + myLabel.pending + '</div>';

				}
				else if(val == 'completed'){

					return '<div class="btn bg-completed btn-xs no-margin"><i class="fa fa-check"></i>' + myLabel.completed + '</div>';

				}

			}

			function formatPassengers(val, obj) {
			    if (parseInt(val) === 1) {
			        return '<i class="fa fa-user"></i> ' + val;
			    } else {
			        return '<i class="fa fa-users"></i> ' + val;
			    }
			}

			function formatPaymentTypes(val, obj) {
			    const paymentLabels = {
			        bank: "Card on Board",
			        cash: "Cash on Board",
			        stripe: "Pay via Stripe",
			        mollie: "Pay via Mollie",
			        ideal: "iDEAL Payment"
			    };

			    return paymentLabels[val] || val;
			}

			var $grid = $("#grid").datagrid({

				buttons: [{type: "print", target: "_blank", url: "index.php?controller=pjAdminBookings&action=pjActionPrint&id={:id}"},

				          {type: "edit", url: "index.php?controller=pjAdminBookings&action=pjActionUpdate&id={:id}"},

				          {type: "delete", url: "index.php?controller=pjAdminBookings&action=pjActionDeleteBooking&id={:id}"},

				          {type: "auction", url: "index.php?controller=pjAdminBookings&action=pjActionPutBookingInAuction&id={:id}"}

						  ],

				columns: [

				          {text: myLabel.client, type: "text", sortable: false},
				          {text: myLabel.fleet, type: "text", sortable: false},
				          {text: myLabel.pickup_address, type: "text", sortable: false},
				          {text: myLabel.return_address, type: "text", sortable: false},
				          {text: myLabel.passengers, type: "text", sortable: false, renderer: formatPassengers},
				          {text: myLabel.extras, type: "text", sortable: false, renderer: formatExtras},
				          {text: myLabel.payment_method, type: "text", sortable: false, renderer: formatPaymentTypes},
				          {text: myLabel.total, type: "text", sortable: false},
				          {text: myLabel.distance, type: "text", sortable: false},
				          {text: myLabel.date_time, type: "text", sortable: false},
				          {text: myLabel.driver_name, type: "text", sortable: false},
				          {text: myLabel.supplier_name, type: "text", sortable: false},
				          {text: myLabel.is_auction, type: "text", sortable: false},
				          {text: myLabel.status, type: "text", sortable: true, editable: false, renderer: formatStatus}],

				dataUrl: "index.php?controller=pjAdminBookings&action=pjActionGetBooking" + pjGrid.queryString,

				dataType: "json",

				fields: ['client', 'fleet','pickup_address', 'return_address', 'passengers', 'extras', 'payment_method', 'total', 'distance', 'date_time', 'driver_name', 'supplier_name','is_auction','status'],

				paginator: {

					actions: [

					   {text: myLabel.delete_selected, url: "index.php?controller=pjAdminBookings&action=pjActionDeleteBookingBulk", render: true, confirmation: myLabel.delete_confirmation},

					   {text: myLabel.exported, url: "index.php?controller=pjAdminBookings&action=pjActionExportBooking", render: false, ajax: false},

					   {text: myLabel.print, url: "javascript:void(0);", render: false}

					],

					gotoPage: true,

					paginate: true,

					total: true,

					rowCount: true

				},

				saveUrl: "index.php?controller=pjAdminBookings&action=pjActionSaveBooking&id={:id}",

				select: {

					field: "id",

					name: "record[]",

					cellClass: 'cell-width-2'

				},

			});

		}

		if ($("#griddeleted").length > 0) {
			function formatExtras(val, obj) {
			    // val is the array of extras
			    if (!val || !val.length) return '—'; // show dash if no extras
			    // Map each extra to "Extra Name (Qty)"
			    return val.map(function(extra) {
			        return extra.extra_name + ' (' + extra.extra_value + ')';
			    }).join(', '); // join with comma
			}

			function formatStatus(val, obj) {
				if(val == 'confirmed')
				{
					return '<div class="btn bg-confirmed btn-xs no-margin"><i class="fa fa-check"></i> ' + myLabel.confirmed + '</div>';
				}else if(val == 'cancelled'){
					return '<div class="btn bg-cancelled btn-xs no-margin"><i class="fa fa-times"></i> ' + myLabel.cancelled + '</div>';
				}else if(val == 'pending'){
					return '<div class="btn bg-pending btn-xs no-margin"><i class="fa fa-exclamation-triangle"></i> ' + myLabel.pending + '</div>';
				}
				else if(val == 'completed'){
					return '<div class="btn bg-completed btn-xs no-margin"><i class="fa fa-check"></i>' + myLabel.completed + '</div>';
				}
			}

			function formatPassengers(val, obj) {
			    if (parseInt(val) === 1) {
			        return '<i class="fa fa-user"></i> ' + val;
			    } else {
			        return '<i class="fa fa-users"></i> ' + val;
			    }
			}

			function formatPaymentTypes(val, obj) {
			    const paymentLabels = {
			        bank: "Card on Board",
			        cash: "Cash on Board",
			        stripe: "Pay via Stripe",
			        mollie: "Pay via Mollie",
			        ideal: "iDEAL Payment"
			    };

			    return paymentLabels[val] || val;
			}

			var $grid = $("#griddeleted").datagrid({
				buttons: [ //{type: "print", target: "_blank", url: "index.php?controller=pjAdminBookings&action=pjActionPrint&id={:id}"},
				          {type: "refresh", url: "index.php?controller=pjAdminBookings&action=pjActionRestore&id={:id}"},
				          {type: "delete", url: "index.php?controller=pjAdminBookings&action=pjActionDeletePBooking&id={:id}"}
						  ],
				columns: [
				          {text: myLabel.client, type: "text", sortable: false},
				          {text: myLabel.fleet, type: "text", sortable: false},
				          {text: myLabel.pickup_address, type: "text", sortable: false},
				          {text: myLabel.return_address, type: "text", sortable: false},
				          {text: myLabel.passengers, type: "text", sortable: false, renderer: formatPassengers},
				          {text: myLabel.extras, type: "text", sortable: false, renderer: formatExtras},
				          {text: myLabel.payment_method, type: "text", sortable: false, renderer: formatPaymentTypes},
				          {text: myLabel.total, type: "text", sortable: false},
				          {text: myLabel.distance, type: "text", sortable: false},
				          {text: myLabel.date_time, type: "text", sortable: false},
				          {text: myLabel.driver_name, type: "text", sortable: false},
				          {text: myLabel.status, type: "text", sortable: true, editable: false, renderer: formatStatus}],
				dataUrl: "index.php?controller=pjAdminBookings&action=pjActionGetDeletedBooking" + pjGrid.queryString,
				dataType: "json",
				fields: ['client', 'fleet','pickup_address', 'return_address', 'passengers', 'extras', 'payment_method', 'total', 'distance', 'date_time', 'driver_name', 'status'],

				paginator: {
					actions: [
					   {text: myLabel.delete_selected, url: "index.php?controller=pjAdminBookings&action=pjActionDeletePBookingBulk", render: true, confirmation: myLabel.delete_confirmation},
					   {text: myLabel.exported, url: "index.php?controller=pjAdminBookings&action=pjActionExportBooking", render: false, ajax: false},
					   {text: myLabel.print, url: "javascript:void(0);", render: false}
					],

					gotoPage: true,
					paginate: true,
					total: true,
					rowCount: true
				},

				saveUrl: "index.php?controller=pjAdminBookings&action=pjActionSaveBooking&id={:id}",
				select: {
					field: "id",
					name: "record[]",
					cellClass: 'cell-width-2'
				},
			});
		}

		$(document).on("focusin", ".timepick", function (e) {

			var minDateTime, maxDateTime,

				$this = $(this),

				custom = {},

				o = {

					timeFormat: $this.attr("lang"),

					stepMinute: 5,

					timeOnly: true

				};

			$(this).datetimepicker(o);

		}).on("submit", ".frm-filter", function (e) {

			if (e && e.preventDefault) {

				e.preventDefault();

			}

			var $this = $(this),

				content = $grid.datagrid("option", "content"),

				cache = $grid.datagrid("option", "cache");
				var startDate = $this.find("input[name='from_date']").val();
				var endDate   = $this.find("input[name='to_date']").val();

			$.extend(cache, {

				q: $this.find("input[name='q']").val(),
				status: $this.find("select[name='status']").val(),
				from_date: $this.find("select[name='from_date']").val(),
				to_date: $this.find("select[name='to_date']").val(),
				start_date: startDate,
       			end_date: endDate

			});

			$grid.datagrid("option", "cache", cache);

			$grid.datagrid("load", "index.php?controller=pjAdminBookings&action=pjActionGetBooking" + pjGrid.queryString, "created", "DESC", content.page, content.rowCount);

			return false;

		}).on("change", ".pj-filter-status", function (e) {

			if (e && e.preventDefault) {

				e.preventDefault();

			}

			$(".frm-filter").trigger("submit");

			return false;

		}).on("change", "#return_status", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var booking_type = $('#return_status').val();
		    if (booking_type == 1) {
		        $('.returnDateTime').show();
		    } else {
		        $('.returnDateTime').hide();
		    }
			calcPrice();
			return false;

		}).on("change", "#fleet_id", function (e) {

			// const airportNames = [
			// 	'innsbruck airport', 'innsbruck flughafen',
			// 	'salzburg airport', 'salzburg flughafen',
			// 	'munich airport', 'münchen flughafen', 'muenchen flughafen',
			// 	'memmingen airport', 'memmingen flughafen',
			// 	'zurich airport', 'zürich flughafen', 'zuerich flughafen',
			// 	'engadin airport', 'samedan st. moritz flughafen',
			// 	'verona airport', 'verona flughafen',
			// 	'bolzano airport', 'bozen flughafen',
			// 	'munich international airport',
			// 	'munich international airport (muc)',
			// 	'munich airport muc',
			// 	'munich international airport muc',
			// 	'münchen flughafen muc',
			// 	'münchen international flughafen muc',
			// 	'muenchen flughafen muc',
			// 	'muenchen international flughafen muc'
			// ];

			const airportNames = [
				'innsbruck airport', 'innsbruck flughafen',
				'Salzburg airport', 'salzburg flughafen', 'Flughafen Salzburg (SZG)',
				'Innsbrucker Bundesstraße', 'Salzburg', 'salzburg',
				'Flughafen Salzburg', 'salzburg flughafen',
				'munich airport', 'münchen flughafen', 'muenchen flughafen',
				'memmingen airport', 'memmingen flughafen',
				'zurich airport', 'zürich flughafen', 'zuerich flughafen',
				'engadin airport', 'samedan st. moritz flughafen',
				'verona airport', 'verona flughafen',
				'bolzano airport', 'bozen flughafen',
				'munich international airport',
				'munich international airport (muc)',
				'munich airport muc',
				'munich international airport muc',
				'münchen flughafen muc',
				'münchen international flughafen muc',
				'muenchen flughafen muc',
				'muenchen international flughafen muc'
			];

			// Initially hide the block

			$('.airlineIncluded').hide();
			$('.returnDateTime').hide();
			$('.airlineIncludedArival').hide();

			var pickupName = document.getElementById('pickup_address').value;
			var returnAddress = document.getElementById('return_address').value;
			var passengers = parseInt($('#fleet_id').find(':selected').attr('data-passengers'), 10),
			    luggage = parseInt($('#fleet_id').find(':selected').attr('data-luggage'), 10),
			    curr_passengers = parseInt($('#passengers').val(), 10),
			    curr_luggage = parseInt($("#luggage").val(), 10);

			var matchFound = false;
			var booking_type = $('#return_status').val(); // adjust selector if needed

			if(passengers > 0)
			{
				$('#tr_max_passengers').html("("+myLabel.maximum+" "+passengers+")");
				$( "#passengers" ).trigger("touchspin.updatesettings", {max: passengers});
				$( "#passengers" ).on('touchspin.on.startspin', function () {calcPrice();});
				if(curr_passengers > passengers)
				{
					$( "#passengers" ).val("");
				}
				$( "#passengers" ).attr('data-value', passengers);

			}

			if(luggage > 0)

			{

				$('#tr_max_luggage').html("("+myLabel.maximum+" "+luggage+")");
				$( "#luggage" ).trigger("touchspin.updatesettings", {max: luggage});
				if(curr_luggage > luggage)
				{
					$( "#luggage").val("");
				}
				$( "#luggage" ).attr('data-value', luggage);
			}

			// Check if any airport name appears in pickup or return address
				for (var i = 0; i < airportNames.length; i++) {
				    if (
				        pickupName.toLowerCase().includes(airportNames[i].toLowerCase()) ||
				        returnAddress.toLowerCase().includes(airportNames[i].toLowerCase())
				    ) {
				        matchFound = true;
				        break;
				    }
				}

				if (matchFound) {
				    if (booking_type == '0') {
				        $('.airlineIncluded').show();
				        $('.airlineIncludedArival').show();
				    } else {
				        $('.airlineIncluded').show();
				        $('.airlineIncludedArival').show();
				    }
				} else {
				    $('.airlineIncluded').hide();
				    $('.airlineIncludedArival').hide();
				}
				
			    if (booking_type == 1) {
			        $('.returnDateTime').show();
			    } else {
			        $('.returnDateTime').hide();
			    }
			getExtras();
		})
		// .on('input', '#deposit', function () {
		//     // Get total and deposit values
		//     var total = parseFloat($('#total').val()) || 0;
		//     var deposit = parseFloat($(this).val()) || 0;

		//     // Calculate remaining balance
		//     var remaining = total - deposit;

		//     // Update remaining balance field
		//     $('#remainingBalance').val(remaining.toFixed(2));
		// })
		.on('input', '#sub_total, #tax, #total, #deposit', function () {
		    // Get numeric values or default to 0
		    var subTotal = parseFloat($('#sub_total').val()) || 0;
		    var tax = parseFloat($('#tax').val()) || 0;
		    var deposit = parseFloat($('#deposit').val()) || 0;
		    var total = parseFloat($('#total').val()) || 0;

		     // Prevent negative values
		    if (subTotal < 0) { $('#sub_total').val(0); subTotal = 0; }
		    if (tax < 0) { $('#tax').val(0); tax = 0; }
		    if (deposit < 0) { $('#deposit').val(0); deposit = 0; }
		    if (total < 0) { $('#total').val(0); total = 0; }

		    // If total is not manually entered, calculate automatically
		    if (!$('#total').is(':focus')) {
		        total = subTotal + tax;
		        $('#total').val(total.toFixed(2));
		    }

		      // Validation: Deposit > Total
		    if (deposit > total) {
		       
		        alert("Deposit cannot be greater than the Total amount!");
		        $('#deposit').val("");
		        return; // stop further calculation
		    } else {
		        
		    }


		    // Calculate remaining balance
		    var remaining = total - deposit;
		    $('#remainingBalance').val(remaining.toFixed(2));
		})

		.on("change", ".pjAvailExtra", function (e) {

					calcPrice();

		}).on("change", ".onoffswitch-client .onoffswitch-checkbox", function (e) {

			if ($(this).prop('checked')) {

                $('.current-client-area').hide();

                $('.current-client-area').find('.fdRequired').removeClass('required');

                $('.new-client-area').show();

                $('.new-client-area').find('.fdRequired').addClass('required');

            }else {

                $('.current-client-area').show();

                $('.current-client-area').find('.fdRequired').addClass('required');

                $('.new-client-area').hide();

                $('.new-client-area').find('.fdRequired').removeClass('required');

                $('#c_email').val("").valid();

            }

		});

		

		$("#grid").on("click", 'a.pj-paginator-action:last', function (e) {

			e.preventDefault();

			var booking_id = $('.pj-table-select-row:checked').map(function(e){

				 return $(this).val();

			}).get();

			if(booking_id != '' && booking_id != null)

			{

				window.open('index.php?controller=pjAdminBookings&action=pjActionPrint&record=' + booking_id,'_blank');

			}	

			return false;

		});

		function getExtras()

		{

			var $frm = null;

			if($frmCreateBooking.length > 0)

			{

				$frm = $frmCreateBooking;

			}

			if($frmUpdateBooking.length > 0)

			{

				$frm = $frmUpdateBooking;

			}

			$.post("index.php?controller=pjAdminBookings&action=pjActionGetExtras", $frm.serialize()).done(function (data) {

				$('#extraBox').html(data);

				if($('.i-checks').length > 0)

				{

					$('.i-checks').iCheck({

			            checkboxClass: 'icheckbox_square-green',

			            radioClass: 'iradio_square-green'

			        });

					$('input').on('ifChanged', function (event) { $(event.target).trigger('change'); });

				}

				calcPrice();

			});	

		}

		function calcPrice()
		{

			var passengers = $('#passengers').val() != "" ? parseInt($('#passengers').val(), 10) : 0;
			var fleet_id = $('#fleet_id').val() != "" ? parseInt($('#fleet_id').val(), 10) : 0;
			var distance = $('#distance').val() != "" ? parseFloat($('#distance').val()) : 0;
			var durationInMin = $('#pjTbsDurationInMinFiled').val() != "" ? parseFloat($('#pjTbsDurationInMinFiled').val()) : 0;
			var booking_type = $('#return_status').val() != "" ? $('#return_status').val() : '0';
			var from_city = $('#from_city').val();
			var to_city = $('#to_city').val();
			var booking_date = $('#booking_date').val();
			var return_date = $('#return_date').val();

				// console.log(booking_type, 'booking_type');

			if(passengers > 0 && fleet_id > 0)
			{
				if($('.pjAvailExtra').length > 0)
				{
					var params = $('.pjAvailExtra').serializeArray();
					params.push({name: "fleet_id", value: fleet_id});
					params.push({name: "passengers", value: passengers});
					params.push({name: "distance", value: distance});
					params.push({name: "durationInMin", value: durationInMin});
					params.push({name: "from_city", value: from_city});
					params.push({name: "to_city", value: to_city});
					params.push({name: "booking_type", value: booking_type});
					params.push({name: "booking_date", value: booking_date});
					params.push({name: "return_date", value: return_date});

				}else{
					var params = {};
					params.fleet_id = fleet_id;
					params.passengers = passengers;
					params.distance = distance;
					params.durationInMin = durationInMin;
					params.from_city = from_city;
					params.to_city = to_city;
					params.booking_type = booking_type;
					params.booking_date = booking_date;
					params.return_date = return_date;
				}


				$.post(["index.php?controller=pjAdminBookings&action=pjActionCalPrice"].join(""), params).done(function (data) {
					
					if(parseFloat(data.subtotal) > 0)
					{   
						
						const multiplier = (booking_type == '1') ? 2 : 1;
						const dateRangeExtra = (parseFloat(data.daterange_price) || 0) + (parseFloat(data.returndate_rangePrice) || 0);
						const subTotal = (parseFloat(data.subtotal) * multiplier) + dateRangeExtra;
					    const tax = (parseFloat(data.tax) * multiplier);
					    const total = (parseFloat(data.total) * multiplier) + dateRangeExtra;
					    const deposit = (parseFloat(data.deposit) * multiplier) + dateRangeExtra;
					    const remainingBalance = (parseFloat(data.remainingBalance) * multiplier);

					    $('#sub_total').val(subTotal.toFixed(2));
					    $('#tax').val(tax.toFixed(2));
					    $('#total').val(total.toFixed(2));
					    $('#deposit').val(deposit.toFixed(2));
					    $('#remainingBalance').val(remainingBalance.toFixed(2));

						// // console.log(data.remainingBalance, 'remainingBalance');						
						// $('#sub_total').val((data.subtotal * multiplier).toFixed(2));
						// $('#tax').val((data.tax * multiplier).toFixed(2));
			    		// $('#total').val((data.total * multiplier).toFixed(2));
			    		// $('#deposit').val((data.deposit * multiplier).toFixed(2));
						// $('#remainingBalance').val((data.remainingBalance * multiplier).toFixed(2));

					}else{
						$('#sub_total').val("");
						$('#tax').val("");
						$('#total').val("");
						$('#deposit').val("");
						$('#remainingBalance').val("");
					}

				}).fail(function () {

					$('#sub_total').val("");
					$('#tax').val("");
					$('#total').val("");
					$('#deposit').val("");
					$('#remainingBalance').val("");
				});

			}else{

				$('#sub_total').val("");

				$('#tax').val("");

				$('#total').val("");

				$('#deposit').val("");

				$('#remainingBalance').val("");

			}

		}

		

		function attachTinyMce(options) {

			if (window.tinymce !== undefined) {

				tinymce.EditorManager.editors = [];

				var defaults = {

					selector: "textarea.mceEditor",

					theme: "modern",

					width: 550,

					height: 330,

					plugins: [

				         "advlist autolink link image lists charmap print preview hr anchor pagebreak",

				         "searchreplace visualblocks visualchars code fullscreen insertdatetime media nonbreaking",

				         "save table contextmenu directionality emoticons template paste textcolor"

				    ],

				    toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | print preview media fullpage | forecolor backcolor emoticons"

				};

				

				var settings = $.extend({}, defaults, options);

				

				tinymce.init(settings);

			}

		}

		if ($modalCancellation.length > 0) {

            $modalCancellation.on("show.bs.modal", function(e) {

                var link = $(e.relatedTarget);



                $(this).find(".modal-body").load(link.attr("href"), function (e) {

                    var $frmCancellation = $('form', $modalCancellation);



                    if ($modalCancellation.find('.multilang').length) {

                    	var locale = $frmCancellation.data("locale"),

                    		$el = $modalCancellation.find('.pj-form-langbar-item[data-index="' + locale + '"]');

                    	if ($el.length) {

                    		$el.trigger('click');

                    	} else {

                    		$modalCancellation.find('.pj-form-langbar-item[data-index]:first').trigger('click');                    		

                    	}

                    }

                    

                    $frmCancellation.validate({

                        ignore: "",

                        submitHandler: function(e) {

                            $.post("index.php?controller=pjAdminBookings&action=pjActionCancellation", $frmCancellation.serialize()).done(function (resp) {

                                if (resp.code !== undefined && parseInt(resp.code, 10) === 200) {

                                    $modalCancellation.modal('hide');

                                    swal("Success!", resp.text, "success");

                                } else {

                                    swal("Error!", resp.text, "error");

                                }

                            });

                        }

                    });

                    attachTinyMce.call(null);

                });

            }).on('click', '.btn-primary', function (e) {

                $modalCancellation.find('form').trigger('submit');

            });

		}

		if ($modalConfirmation.length > 0) {

			$modalConfirmation.on("show.bs.modal", function(e) {

                var link = $(e.relatedTarget);

                $(this).find(".modal-body").load(link.attr("href"), function (e) {

                    var $frmConfirmation = $('form', $modalConfirmation);

                    if ($modalConfirmation.find('.multilang').length) {

                    	var locale = $frmConfirmation.data("locale"),

                    		$el = $modalConfirmation.find('.pj-form-langbar-item[data-index="' + locale + '"]');

                    	if ($el.length) {

                    		$el.trigger('click');

                    	} else {

                    		$modalConfirmation.find('.pj-form-langbar-item[data-index]:first').trigger('click');                    		

                    	}

                    }

                    

                    $frmConfirmation.validate({

                        ignore: "",

                        submitHandler: function(e) {

                            $.post("index.php?controller=pjAdminSuppliers&action=pjActionConfirmation", $frmConfirmation.serialize()).done(function (resp) {

                                if (resp.code !== undefined && parseInt(resp.code, 10) === 200) {

                                	$modalConfirmation.modal('hide');

                                    swal("Success!", resp.text, "success");

                                } else {

                                    swal("Error!", resp.text, "error");

                                }

                            });

                        }

                    });

                    attachTinyMce.call(null);

                });

            }).on('click', '.btn-primary', function (e) {

            	$modalConfirmation.find('form').trigger('submit');

            });

		}

		

		if ($modalSmsConfirmation.length > 0) {

			$modalSmsConfirmation.on("show.bs.modal", function(e) {

                var link = $(e.relatedTarget);

                	

                $(this).find(".modal-body").load(link.attr("href"), function (e) {

                    var $frmSmsConfirmation = $('form', $modalSmsConfirmation);



                    if ($modalSmsConfirmation.find('.multilang').length) {

                    	var locale = $frmSmsConfirmation.data("locale"),

                    		$el = $modalSmsConfirmation.find('.pj-form-langbar-item[data-index="' + locale + '"]');

                    	if ($el.length) {

                    		$el.trigger('click');

                    	} else {

                    		$modalSmsConfirmation.find('.pj-form-langbar-item[data-index]:first').trigger('click');                    		

                    	}

                    }

                    

                    $frmSmsConfirmation.validate({

                        ignore: "",

                        submitHandler: function(e) {

                            $.post("index.php?controller=pjAdminBookings&action=pjActionSmsConfirmation", $frmSmsConfirmation.serialize()).done(function (resp) {

                                if (resp.code !== undefined && parseInt(resp.code, 10) === 200) {

                                	$modalSmsConfirmation.modal('hide');

                                    swal("Success!", resp.text, "success");

                                } else {

                                    swal("Error!", resp.text, "error");

                                }

                            });

                        }

                    });

                });

            }).on('click', '.btn-primary', function (e) {

            	$modalSmsConfirmation.find('form').trigger('submit');

            });

		}

	});

})(jQuery_1_8_2);