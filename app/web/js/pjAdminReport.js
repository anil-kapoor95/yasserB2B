var jQuery_1_8_2 = jQuery_1_8_2 || $.noConflict();

(function ($, undefined) {

	$(function () {

		"use strict";

		var
			datagrid = ($.fn.datagrid !== undefined),
			datetimeOptions = null;

		if ($('#datePickerOptions').length) {
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

			// ✅ ADD HERE ↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓

				var startOfMonth = moment().startOf('month');
				var endOfMonth = moment().endOf('month');

				$('#from_date').val(startOfMonth.format('YYYY-MM-DD'));
				$('#to_date').val(endOfMonth.format('YYYY-MM-DD'));

				$('#from_date').data("DateTimePicker").date(startOfMonth);
				$('#to_date').data("DateTimePicker").date(endOfMonth);

				setTimeout(function () {
					$('.frm-filter').trigger('submit');
				}, 200);

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


		if ($("#grid").length > 0 && datagrid) {

			function formatExtras(val, obj) {
				// val is the array of extras
				if (!val || !val.length) return '—'; // show dash if no extras

				// Map each extra to "Extra Name (Qty)"
				return val.map(function (extra) {
					return extra.extra_name + ' (' + extra.extra_value + ')';
				}).join(', '); // join with comma
			}

			function formatStatus(val, obj) {

				if (val == 'confirmed') {

					return '<div class="btn bg-confirmed btn-xs no-margin"><i class="fa fa-check"></i> ' + myLabel.confirmed + '</div>';

				} else if (val == 'cancelled') {

					return '<div class="btn bg-cancelled btn-xs no-margin"><i class="fa fa-times"></i> ' + myLabel.cancelled + '</div>';

				} else if (val == 'pending') {

					return '<div class="btn bg-pending btn-xs no-margin"><i class="fa fa-exclamation-triangle"></i> ' + myLabel.pending + '</div>';

				}
				else if (val == 'completed') {

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

				// buttons: [{type: "print", target: "_blank", url: "index.php?controller=pjAdminReport&action=pjActionPrint&id={:id}"},

				//           {type: "edit", url: "index.php?controller=pjAdminReport&action=pjActionUpdate&id={:id}"},

				//           {type: "delete", url: "index.php?controller=pjAdminReport&action=pjActionDeleteBooking&id={:id}"},

				//           {type: "auction", url: "index.php?controller=pjAdminReport&action=pjActionPutBookingInAuction&id={:id}"}

				// 		  ],

				columns: [

					{ text: myLabel.client, type: "text", sortable: false },
					{ text: myLabel.fleet, type: "text", sortable: false },
					{ text: myLabel.pickup_address, type: "text", sortable: false },
					{ text: myLabel.return_address, type: "text", sortable: false },
					{ text: myLabel.distance, type: "text", sortable: false },
					{ text: myLabel.date_time, type: "text", sortable: false },
					{text: myLabel.total + "(" + (window.appCurrency || '')+ ")", type: "text", sortable: false},
					{text: myLabel.commission + "(" + (window.appCurrency || '')+ ")", type: "text", sortable: false},
					{text: myLabel.supplier_amount + "(" + (window.appCurrency || '')+ ")", type: "text", sortable: false},
				],

				dataUrl: "index.php?controller=pjAdminReport&action=pjActionGetBooking" + pjGrid.queryString,

				dataType: "json",

				fields: ['client', 'fleet', 'pickup_address', 'return_address', 'distance', 'date_time', 'total','commission_amount','supplier_amount'],

				paginator: {
					actions: [
					   {text: myLabel.exported, url: "index.php?controller=pjAdminReport&action=pjActionExport", render: false, ajax: false},
					],

					gotoPage: true,

					paginate: true,

					total: true,

					rowCount: true

				},

				saveUrl: "index.php?controller=pjAdminReport&action=pjActionSaveBooking&id={:id}",

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
				return val.map(function (extra) {
					return extra.extra_name + ' (' + extra.extra_value + ')';
				}).join(', '); // join with comma
			}

			function formatStatus(val, obj) {
				if (val == 'confirmed') {
					return '<div class="btn bg-confirmed btn-xs no-margin"><i class="fa fa-check"></i> ' + myLabel.confirmed + '</div>';
				} else if (val == 'cancelled') {
					return '<div class="btn bg-cancelled btn-xs no-margin"><i class="fa fa-times"></i> ' + myLabel.cancelled + '</div>';
				} else if (val == 'pending') {
					return '<div class="btn bg-pending btn-xs no-margin"><i class="fa fa-exclamation-triangle"></i> ' + myLabel.pending + '</div>';
				}
				else if (val == 'completed') {
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
				buttons: [ //{type: "print", target: "_blank", url: "index.php?controller=pjAdminReport&action=pjActionPrint&id={:id}"},
					{ type: "refresh", url: "index.php?controller=pjAdminReport&action=pjActionRestore&id={:id}" },
					{ type: "delete", url: "index.php?controller=pjAdminReport&action=pjActionDeletePBooking&id={:id}" }
				],
				columns: [
					{ text: myLabel.client, type: "text", sortable: false },
					{ text: myLabel.fleet, type: "text", sortable: false },
					{ text: myLabel.pickup_address, type: "text", sortable: false },
					{ text: myLabel.return_address, type: "text", sortable: false },
					{ text: myLabel.distance, type: "text", sortable: false },
					{ text: myLabel.date_time, type: "text", sortable: false },
					{text: myLabel.total + "(" + (window.appCurrency || '')+ ")", type: "text", sortable: false},
					{text: myLabel.commission + "(" + (window.appCurrency || '')+ ")", type: "text", sortable: false},
					{text: myLabel.supplier_amount + "(" + (window.appCurrency || '')+ ")", type: "text", sortable: false},
				],
				dataUrl: "index.php?controller=pjAdminReport&action=pjActionGetDeletedBooking" + pjGrid.queryString,
				dataType: "json",
				fields: ['client', 'fleet', 'pickup_address', 'return_address', 'distance', 'date_time', 'total', 'commission_amount','supplier_amount'],

				paginator: {
					actions: [
					   {text: myLabel.exported, url: "index.php?controller=pjAdminReport&action=pjActionExport", render: false, ajax: false},
					],

					gotoPage: true,
					paginate: true,
					total: true,
					rowCount: true
				},

				saveUrl: "index.php?controller=pjAdminReport&action=pjActionSaveBooking&id={:id}",
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
			var endDate = $this.find("input[name='to_date']").val();

			$.extend(cache, {

				q: $this.find("input[name='q']").val(),
				status: $this.find("select[name='status']").val(),
				supplier: $this.find("select[name='supplier_id']").val(),
				start_date: startDate,
				end_date: endDate

			});

			$grid.datagrid("option", "cache", cache);

			$grid.datagrid("load", "index.php?controller=pjAdminReport&action=pjActionGetBooking" + pjGrid.queryString, "created", "DESC", content.page, content.rowCount);

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

			if (passengers > 0) {
				$('#tr_max_passengers').html("(" + myLabel.maximum + " " + passengers + ")");
				$("#passengers").trigger("touchspin.updatesettings", { max: passengers });
				$("#passengers").on('touchspin.on.startspin', function () { calcPrice(); });
				if (curr_passengers > passengers) {
					$("#passengers").val("");
				}
				$("#passengers").attr('data-value', passengers);

			}

			if (luggage > 0) {

				$('#tr_max_luggage').html("(" + myLabel.maximum + " " + luggage + ")");
				$("#luggage").trigger("touchspin.updatesettings", { max: luggage });
				if (curr_luggage > luggage) {
					$("#luggage").val("");
				}
				$("#luggage").attr('data-value', luggage);
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

				} else {

					$('.current-client-area').show();

					$('.current-client-area').find('.fdRequired').addClass('required');

					$('.new-client-area').hide();

					$('.new-client-area').find('.fdRequired').removeClass('required');

					$('#c_email').val("").valid();

				}

			});



		// $("#grid").on("click", 'a.pj-paginator-action:last', function (e) {

		// 	e.preventDefault();

		// 	var booking_id = $('.pj-table-select-row:checked').map(function (e) {

		// 		return $(this).val();

		// 	}).get();

		// 	if (booking_id != '' && booking_id != null) {

		// 		window.open('index.php?controller=pjAdminReport&action=pjActionPrint&record=' + booking_id, '_blank');

		// 	}

		// 	return false;

		// });

		function getExtras() {

			var $frm = null;

			if ($frmCreateBooking.length > 0) {

				$frm = $frmCreateBooking;

			}

			if ($frmUpdateBooking.length > 0) {

				$frm = $frmUpdateBooking;

			}

			$.post("index.php?controller=pjAdminReport&action=pjActionGetExtras", $frm.serialize()).done(function (data) {

				$('#extraBox').html(data);

				if ($('.i-checks').length > 0) {

					$('.i-checks').iCheck({

						checkboxClass: 'icheckbox_square-green',

						radioClass: 'iradio_square-green'

					});

					$('input').on('ifChanged', function (event) { $(event.target).trigger('change'); });

				}

				calcPrice();

			});

		}

		function calcPrice() {

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

			if (passengers > 0 && fleet_id > 0) {
				if ($('.pjAvailExtra').length > 0) {
					var params = $('.pjAvailExtra').serializeArray();
					params.push({ name: "fleet_id", value: fleet_id });
					params.push({ name: "passengers", value: passengers });
					params.push({ name: "distance", value: distance });
					params.push({ name: "durationInMin", value: durationInMin });
					params.push({ name: "from_city", value: from_city });
					params.push({ name: "to_city", value: to_city });
					params.push({ name: "booking_type", value: booking_type });
					params.push({ name: "booking_date", value: booking_date });
					params.push({ name: "return_date", value: return_date });

				} else {
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


				$.post(["index.php?controller=pjAdminReport&action=pjActionCalPrice"].join(""), params).done(function (data) {

					if (parseFloat(data.subtotal) > 0) {

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

					} else {
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

			} else {

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

	});

})(jQuery_1_8_2);
// =======================================================
// FINAL CORRECT FOOTER (USES BACKEND VALUES)
// =======================================================
(function ($) {

    var gridTotals = {
        grid: null,
        griddeleted: null
    };

    function buildFooter(gridId) {

        var data = gridTotals[gridId];
        if (!data) return;

        var $grid = $("#" + gridId);
        var $table = $grid.find("table");

        if (!$table.length) return;

        $table.find("tfoot").remove();

        var footer =
            '<tfoot>' +
                '<tr style="font-weight:bold;background:#f5f5f5;">' +

                    // ✅ FIXED ALIGNMENT (7 columns before totals)
                    '<td colspan="7" style="text-align:right;">Total:</td>' +

                    '<td>' + parseFloat(data.total_price || 0).toFixed(2) + '</td>' +
                    '<td>' + parseFloat(data.total_commission || 0).toFixed(2) + '</td>' +
                    '<td>' + parseFloat(data.total_supplier_amount || 0).toFixed(2) + '</td>' +

                '</tr>' +
            '</tfoot>';

        $table.append(footer);
    }

    $(document).ajaxSuccess(function (event, xhr, settings) {

        if (!settings || !settings.url) return;

        try {

            if (settings.url.indexOf("pjActionGetBooking") !== -1) {

                var json = JSON.parse(xhr.responseText);

                // ✅ store backend totals
                gridTotals.grid = json;

                setTimeout(function () {
                    buildFooter("grid");
                }, 200);
            }

            if (settings.url.indexOf("pjActionGetDeletedBooking") !== -1) {

                var json2 = JSON.parse(xhr.responseText);

                gridTotals.griddeleted = json2;

                setTimeout(function () {
                    buildFooter("griddeleted");
                }, 200);
            }

        } catch (e) {
            console.log("Footer JSON parse error", e);
        }

    });

})(jQuery_1_8_2);