var jQuery_1_8_2 = jQuery_1_8_2 || $.noConflict();
(function ($, undefined) {
	$(function () {
		var $frmCreateCity = $("#frmCreateCity"),
			$frmUpdateCity = $("#frmUpdateCity"),
			validate = ($.fn.validate !== undefined),
			datagrid = ($.fn.datagrid !== undefined);
		
		if ($frmCreateCity.length > 0 && validate) {
			$frmCreateCity.validate({
				errorPlacement: function (error, element) {
					error.insertAfter(element.parent());
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em",
				ignore: "",
				invalidHandler: function (event, validator) {
					var localeId = $(validator.errorList[0].element, this).attr('lang');
					if(localeId != undefined)
					{
						$(".pj-multilang-wrap").each(function( index ) {
							if($(this).attr('data-index') == localeId)
							{
								$(this).css('display','block');
							}else{
								$(this).css('display','none');
							}
						});
						$(".pj-form-langbar-item").each(function( index ) {
							if($(this).attr('data-index') == localeId)
							{
								$(this).addClass('pj-form-langbar-item-active');
							}else{
								$(this).removeClass('pj-form-langbar-item-active');
							}
						});
					}
				}
			});
		}
		if ($frmUpdateCity.length > 0 && validate) {
			$frmUpdateCity.validate({
				errorPlacement: function (error, element) {
					error.insertAfter(element.parent());
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em",
				ignore: "",
				invalidHandler: function (event, validator) {
					var localeId = $(validator.errorList[0].element, this).attr('lang');
					if(localeId != undefined)
					{
						$(".pj-multilang-wrap").each(function( index ) {
							if($(this).attr('data-index') == localeId)
							{
								$(this).css('display','block');
							}else{
								$(this).css('display','none');
							}
						});
						$(".pj-form-langbar-item").each(function( index ) {
							if($(this).attr('data-index') == localeId)
							{
								$(this).addClass('pj-form-langbar-item-active');
							}else{
								$(this).removeClass('pj-form-langbar-item-active');
							}
						});
					}
				}
			});
		}
		if ($("#grid").length > 0 && datagrid) {
			var $grid = $("#grid").datagrid({
				buttons: [{type: "edit", url: "index.php?controller=pjAdminCities&action=pjActionUpdate&id={:id}"},
				          {type: "delete", url: "index.php?controller=pjAdminCities&action=pjActionDeleteCity&id={:id}"}
				          ],
				columns: [{text: myLabel.county_name, type: "text", sortable: true, editable: true, width: 600, editableWidth: 500}
				         ],
				dataUrl: "index.php?controller=pjAdminCities&action=pjActionGetCity",
				dataType: "json",
				fields: ['name'],
				paginator: {
					actions: [
					   {text: myLabel.delete_selected, url: "index.php?controller=pjAdminCities&action=pjActionDeleteCityBulk", render: true, confirmation: myLabel.delete_confirmation}
					],
					gotoPage: true,
					paginate: true,
					total: true,
					rowCount: true
				},
				saveUrl: "index.php?controller=pjAdminCities&action=pjActionSaveCity&id={:id}",
				select: {
					field: "id",
					name: "record[]"
				}
			});
		}
		
		$(document).on("submit", ".frm-filter", function (e) {
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
			$grid.datagrid("load", "index.php?controller=pjAdminCities&action=pjActionGetCity", "name", "ASC", content.page, content.rowCount);
			return false;
		});
	});
})(jQuery_1_8_2);