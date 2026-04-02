var jQuery_1_8_2 = jQuery_1_8_2 || $.noConflict();
(function ($, undefined) {
	$(function () {
		"use strict";
		var $frmCreateClient = $("#frmCreateClient"),
			$frmUpdateClient = $("#frmUpdateClient"),
			select2 = ($.fn.select2 !== undefined),
			datagrid = ($.fn.datagrid !== undefined);
			
			datetimeOptions = null;
		
		if (select2 && $(".select-item").length) {
            $(".select-item").select2({
            	placeholder: "-- " + myLabel.choose + " --",
                allowClear: true
            });
        };
		if($frmCreateClient.length > 0)
		{
			$frmCreateClient.validate({
				rules: {
					"email": {
						required: true,
						email: true,
						remote: "index.php?controller=pjAdminDrivers&action=pjActionCheckEmail"
					}
				},
				messages: {
					"email": {
						remote: myLabel.email_exists
					}
				}
			});
		}
		if ($frmUpdateClient.length > 0) {
			$frmUpdateClient.validate({
				rules: {
					"email": {
						required: true,
						email: true,
						remote: "index.php?controller=pjAdminDrivers&action=pjActionCheckEmail&id=" + $frmUpdateClient.find("input[name='id']").val()
					}
				},
				messages: {
					"email": {
						remote: myLabel.email_exists
					}
				}
			});
		}
		function formatOrders(str, obj) 
		{
			if(parseInt(str, 10) > 0)
			{
				return '<a href="index.php?controller=pjAdminBookings&action=pjActionIndex&client_id='+obj.id+'">'+str+'</a>';
			}else{
				return str;
			}
		}
		if ($("#grid").length > 0 && datagrid) 
		{
			var buttonOpts = [];
			var actionOpts = [];
			if(pjGrid.hasDeleteMulti)
			{
				actionOpts.push({text: myLabel.delete_selected, url: "index.php?controller=pjAdminSuppliers&action=pjActionDeleteDriverBulk", render: true, confirmation: myLabel.delete_confirmation});
			}
			buttonOpts.push({
				type: "eye",
				url: "index.php?controller=pjAdminSuppliers&action=pjActionGetDriverResAdminIndex&id={:id}",
				icon: "fa fa-eye"
			});

			if(pjGrid.hasUpdate)
			{
				buttonOpts.push({type: "edit", url: "index.php?controller=pjAdminSuppliers&action=pjActionDriverUpdate&id={:id}"});
			}
			if(pjGrid.hasDeleteSingle)
			{
				buttonOpts.push({type: "delete", url: "index.php?controller=pjAdminSuppliers&action=pjActionDeleteDriver&id={:id}"});
			}
			if(pjGrid.hasRevertStatus)
			{
				actionOpts.push({text: myLabel.revert_status, url: "index.php?controller=pjAdminDrivers&action=pjActionStatusDriver", render: true});
			}
			if(pjGrid.hasExport)
			{
				actionOpts.push({text: myLabel.exported, url: "index.php?controller=pjAdminDrivers&action=pjActionExportDriver", ajax: false});
			}

			function formatImage(val, obj) {
				
			var src = val != null ? val : 'app/web/img/backend/no-image.png';
			return ['<a href="#', obj.id ,'"><img src="', src, '" style="width: 100px" /></a>'].join("");
		}

			var $grid = $("#grid").datagrid({
				buttons: buttonOpts,
				columns: [
						{text: myLabel.thumb_path, type: "text", sortable: false, editable: false, renderer: formatImage},
						{text: myLabel.name, type: "text", sortable: true, editable: false},
						{text: myLabel.email, type: "text", sortable: true, editable: pjGrid.hasUpdate},
						{text: myLabel.phone, type: "text", sortable: true, editable: pjGrid.hasUpdate},
						{text: myLabel.license_number, type: "text", sortable: true, editable: pjGrid.hasUpdate},
						{text: myLabel.license_expiry, type: "text", sortable: true, editable: pjGrid.hasUpdate},
						{text: myLabel.vehicle_id, type: "text", sortable: true, editable: pjGrid.hasUpdate},
						{text: myLabel.status, type: "toggle", sortable: true, editable: pjGrid.hasUpdate, positiveLabel: myLabel.active, positiveValue: "T", negativeLabel: myLabel.inactive, negativeValue: "F"},
				          ],
				dataUrl: "index.php?controller=pjAdminSuppliers&action=pjActionGetDriver",
				dataType: "json",
				fields: ['thumb_path', 'name', 'email', 'phone', 'license_number', 'license_expiry', 'vehicle_id', 'status'],
				paginator: {
					actions: actionOpts,
					gotoPage: true,
					paginate: true,
					total: true,
					rowCount: true
				},
				saveUrl: "index.php?controller=pjAdminDrivers&action=pjActionSaveClient&id={:id}",
				select: {
					field: "id",
					name: "record[]",
					cellClass: 'cell-width-2'
				}
			});
		}
			
		if ($('#dateTimePickerOptionss').length)
		{
			var currentDate = new Date(),
				$optionsEle = $('#dateTimePickerOptionss');
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
			$('.datetimepick').datetimepicker(dateOnlyOptions);
			$('.datetimepick_dob').datetimepicker(dateOnlyOptions);
		}

		if ($("#grid-reservations-admin").length > 0 && datagrid) 
		{
			var buttonOpts = [];
			var actionOpts = [];

			function formatFleet(val, obj) {
				return val ? '<span class="badge badge-info">'+ val +'</span>' : '';
			}

			function getUrlParam(param) {
				var urlParams = new URLSearchParams(window.location.search);
				return urlParams.get(param);
			}

			function formatStatus(val, obj) {
				
				if(val == 'confirmed')
				{
					return '<div class="btn bg-confirmed btn-xs no-margin"><i class="fa fa-check"></i> ' + myLabel.confirmed + '</div>';
				}else if(val == 'cancelled'){
					return '<div class="btn bg-cancelled btn-xs no-margin"><i class="fa fa-times"></i> ' + myLabel.cancelled + '</div>';
				}else if(val == 'pending'){
					return '<div class="btn bg-pending btn-xs no-margin"><i class="fa fa-exclamation-triangle"></i> ' + myLabel.pending + '</div>';
				}else if(val == 'completed'){
					return '<div class="btn bg-completed btn-xs no-margin"><i class="fa fa-check"></i>' + myLabel.completed + '</div>';
				}
			}

			var driverId = getUrlParam("id"); // ✅ dynamic from URL

			var $grid = $("#grid-reservations-admin").datagrid({
				buttons: buttonOpts,
				columns: [
					{text: myLabel.fleet, type: "text", sortable: false, editable: false},
					{text: myLabel.pickup_address, type: "text", sortable: true, editable: false},
					{text: myLabel.return_address, type: "text", sortable: true, editable: false},
					{text: myLabel.distance, type: "text", sortable: true, editable: false},
					{text: myLabel.booking_date, type: "text", sortable: true, editable: false},
					{text: myLabel.booking_status, type: "text", sortable: true, editable: false, renderer: formatStatus},
					{text: myLabel.created, type: "text", sortable: true, editable: false},
				],
				dataUrl: "index.php?controller=pjAdminDrivers&action=pjActionDriverResAdminList&id=" + driverId,

				dataType: "json",
				fields: ['fleet', 'pickup_address', 'return_address', 'distance', 'booking_date', 'booking_status', 'created'],
				paginator: {
					actions: actionOpts,
					gotoPage: true,
					paginate: true,
					total: true,
					rowCount: true
				},
				select: {
					field: "id",
					name: "record[]",
					cellClass: 'cell-width-2'
				}
			});
			}


		if ($("#grid-reservations").length > 0 && datagrid) 
			{
				var buttonOpts = [];
				var actionOpts = [];

				function formatFleet(val, obj) {
					return val ? '<span class="badge badge-info">'+ val +'</span>' : '';
				}

				function getUrlParam(param) {
					var urlParams = new URLSearchParams(window.location.search);
					return urlParams.get(param);
				}

				function formatStatus(val, obj) {
					if(val == 'confirmed')
					{
						return '<div class="btn bg-confirmed btn-xs no-margin"><i class="fa fa-check"></i> ' + myLabel.confirmed + '</div>';
					}else if(val == 'cancelled'){
						return '<div class="btn bg-cancelled btn-xs no-margin"><i class="fa fa-times"></i> ' + myLabel.cancelled + '</div>';
					}else if(val == 'pending'){
						return '<div class="btn bg-pending btn-xs no-margin"><i class="fa fa-exclamation-triangle"></i> ' + myLabel.pending + '</div>';
					}else if(val == 'completed'){
					return '<div class="btn bg-completed btn-xs no-margin"><i class="fa fa-check"></i>' + myLabel.completed + '</div>';
				}
				}

				var driverId = getUrlParam("id"); // ✅ dynamic from URL

				var $grid = $("#grid-reservations").datagrid({
					buttons: buttonOpts,
					columns: [
						{text: myLabel.fleet, type: "text", sortable: false, editable: false},
						{text: myLabel.pickup_address, type: "text", sortable: true, editable: false},
						{text: myLabel.return_address, type: "text", sortable: true, editable: false},
						{text: myLabel.distance, type: "text", sortable: true, editable: false},
						{text: myLabel.booking_date, type: "text", sortable: true, editable: false},
						{text: myLabel.booking_status, type: "text", sortable: true, editable: false, renderer: formatStatus},
						{text: myLabel.created, type: "text", sortable: true, editable: false},
					],
					dataUrl: "index.php?controller=pjAdminDrivers&action=pjActionDriverReservationsList",

					dataType: "json",
					fields: ['fleet', 'pickup_address', 'return_address', 'distance', 'booking_date','booking_status','created'],
					paginator: {
						actions: actionOpts,
						gotoPage: true,
						paginate: true,
						total: true,
						rowCount: true
					},
					select: {
						field: "id",
						name: "record[]",
						cellClass: 'cell-width-2'
					}
				});
			}

		$(document).on("click", ".btn-all", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			$(this).addClass("btn-primary active").removeClass("btn-default")
				.siblings(".btn").removeClass("btn-primary active").addClass("btn-default");
			var content = $grid.datagrid("option", "content"),
				cache = $grid.datagrid("option", "cache");
			$.extend(cache, {
				status: "",
				q: ""
			});
			$grid.datagrid("option", "cache", cache);
			$grid.datagrid("load", "index.php?controller=pjAdminSuppliers&action=pjActionGetDriver", "name", "ASC", content.page, content.rowCount);
			
		}).on("click", ".image_btn", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var fileId = $(this).val(); // get value attribute
    		 // Call your AJAX delete function here
			$.ajax({
				url: "index.php?controller=pjAdminDrivers&action=pjActionDeleteDriverFiles&id=" + fileId,
				type: 'POST',
				data: { id: fileId },
				success: function(response) {
					console.log('Deleted:', response);
					// Optionally remove file from UI:
					// $(this).closest('.file-wrapper').remove();
				},
				error: function(xhr) {
					console.error('Delete failed:', xhr);
				}
			});
			
		}).on("click", ".btn-filter", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $this = $(this),
				content = $grid.datagrid("option", "content"),
				cache = $grid.datagrid("option", "cache"),
				obj = {};
			$this.addClass("btn-primary active").removeClass("btn-default")
				.siblings(".btn").removeClass("btn-primary active").addClass("btn-default");
			obj.status = "";
			obj[$this.data("column")] = $this.data("value");
			$.extend(cache, obj);
			$grid.datagrid("option", "cache", cache);
			$grid.datagrid("load", "index.php?controller=pjAdminSuppliers&action=pjActionGetDriver", "name", "ASC", content.page, content.rowCount);
			
		}).on("submit", ".frm-filter", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $this = $(this),
				content = $grid.datagrid("option", "content"),
				cache = $grid.datagrid("option", "cache");
			$.extend(cache, {
				q: $this.find("input[name='q']").val()
			});
			$grid.datagrid("option", "cache", cache);
			$grid.datagrid("load", "index.php?controller=pjAdminSuppliers&action=pjActionGetDriver", "name", "ASC", content.page, content.rowCount);
			return false;
		});
	});
})(jQuery_1_8_2);