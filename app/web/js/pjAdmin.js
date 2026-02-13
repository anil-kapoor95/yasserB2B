var jQuery_1_8_2 = jQuery_1_8_2 || $.noConflict();
(function ($, undefined) {
	$(function () {
		"use strict";
		var $frmLoginAdmin = $("#frmLoginAdmin"),
			$frmForgotAdmin = $("#frmForgotAdmin"),
			$frmUpdateProfile = $("#frmUpdateProfile"),
			chosen = ($.fn.chosen !== undefined),
			validate = ($.fn.validate !== undefined);
		
		if (chosen) {
			$("#country_id").chosen();
		}
		if ($frmLoginAdmin.length > 0 && validate) {
			$frmLoginAdmin.validate({
				rules: {
					login_email: {
						required: true,
						email: true
					},
					login_password: "required"
				},
				errorPlacement: function (error, element) {
					error.insertAfter(element.parent());
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em"
			});
		}
		
		if ($frmForgotAdmin.length > 0 && validate) {
			$frmForgotAdmin.validate({
				rules: {
					forgot_email: {
						required: true,
						email: true
					}
				},
				errorPlacement: function (error, element) {
					error.insertAfter(element.parent());
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em"
			});
		}
		
		if ($frmUpdateProfile.length > 0 && validate) {
			$frmUpdateProfile.validate({
				rules: {
					"email": {
						required: true,
						email: true
					},
					"password": "required",
					"name": "required"
				},
				errorPlacement: function (error, element) {
					error.insertAfter(element.parent());
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em"
			});
		}
	});
})(jQuery_1_8_2);