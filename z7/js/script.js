$(document).ready(function() {
	/** default theme options **/
	var theme_settings = {
		font_size: "13px",
		header_font_size: "24px",
		info_font_size: "14px",
		background_color: "#ffffff",
		text_color: "#000000",
		border_color: "#000000",
		border_width: "1px",
		cell_padding: "5px",
		even_row_color: "#e8e8e8",
		table_header_color: "#bababa",
		hover_color: "#ffff80",
		active_color: "#ffcc99"
	};
	
	var theme_settings_default = {
		font_size: "13px",
		header_font_size: "24px",
		info_font_size: "14px",
		background_color: "#ffffff",
		text_color: "#000000",
		border_color: "#000000",
		border_width: "1px",
		cell_padding: "5px",
		even_row_color: "#e8e8e8",
		table_header_color: "#bababa",
		hover_color: "#ffff80",
		active_color: "#ffcc99"
	};
	
	$("#format").val("xml");
	$("#theme").val("default");
	
	$("#custom-theme-settings input[type=range]").on("input change", function() {
		$(this).next("span.cnt").text($(this).val()+"px");
		theme_settings[$(this).attr("name")] = $(this).val()+"px";
	});
	
	// Initialize
	function initialize() {
		$("#custom-theme-settings input[type=text]").each(function() {
			$(this).val(theme_settings_default[$(this).attr("name")]);
			$(this).next("div.color").css("background-color", theme_settings_default[$(this).attr("name")]);
		});
		
		$("#custom-theme-settings input[type=range]").each(function() {
			$(this).val(parseInt(theme_settings_default[$(this).attr("name")]));
			$(this).next("span.cnt").text(theme_settings_default[$(this).attr("name")]);
		});			
	};
	initialize();
	
	// Show color picker
	$("#custom-theme-settings input[type=text]").on("click focusin", function() {
		var element = $(this);
		$(this).ColorPicker({
			color: theme_settings[$(element).attr("name")],
			onChange: function(hsb, hex, rgb) {
				element.val("#"+hex);
				theme_settings[$(element).attr("name")] = "#"+hex;
				
				$(element).next("div.color").css("background-color", theme_settings[$(element).attr("name")]);
			},
			onBeforeShow: function () {
				$(this).ColorPickerSetColor(element.val());
			}
		});
	});
	
	// Set color while typing
	$("#custom-theme-settings input[type=text]").on("input change", function() {
		theme_settings[$(this).attr("name")] = $(this).val();
		$(this).ColorPickerSetColor($(this).val());
		
		$(this).next("div.color").css("background-color", theme_settings[$(this).attr("name")]);
	});
	
	// Reset to default
	$("#custom-theme-settings button").click(function() {
		for(var key in theme_settings) {
			theme_settings[key] = theme_settings_default[key];
		}
		initialize();
	});
	
	/** formatter for date inputs **/
	$("input[name=date_from], input[name=date_to], input[name=dt_owned]").formatter({
	  'pattern': '{{9999}}-{{99}}-{{99}}',
	  'persistent': false
	});
	
	/** slider change **/
	$("#form6 input[type=range]").on("input change", function() {
		$("#form6 span.cnt").text($(this).val());
	});
	
	var checkReCaptcha = function() {
		var recaptcharesponse = grecaptcha.getResponse();
		var result = {};
		
		if(recaptcharesponse.length == 0)
			result = {is_validated: false};
		else
			result = {is_validated: true, response: recaptcharesponse};
		
		return result;
	};
	
	/* enable theme settings */
	$("#format").on("input change", function() {
		if($(this).val() == "xml") {
			$("span ~ #theme").fadeIn(200);
			$("#format").next("span").fadeIn(200);
			$("#theme").attr("disabled", false);
			$("#edit-custom-theme").text("Edytuj");
			$("#theme").val("default");
		}
		else {
			$("span ~ #theme").fadeOut(200);
			$("#format").next("span").fadeOut(200);
			$("#theme").attr("disabled", true);
			$("#custom-theme-settings").hide(200);
			$("#edit-custom-theme").fadeOut(200);
			$("#edit-custom-theme").text("Edytuj");
		}
	});
	
	$("#theme").on("input change", function() {
		if($(this).val() == "custom") {
			$("#edit-custom-theme").fadeIn(200);
			$("#edit-custom-theme").text("Edytuj");
		} else {
			$("#custom-theme-settings").hide(200);
			$("#edit-custom-theme").fadeOut(200);
		}
	});
	
	$("#edit-custom-theme").click(function() {
		if($("#custom-theme-settings").is(":visible")) {
			$("#custom-theme-settings").hide(200);
			$(this).text("Edytuj");
		} else {
			$("#custom-theme-settings").show(200);
			$(this).text("Gotowe");
		}
	});
	
	/** standard behaviour **/
	$("p.entry").nextAll().hide();
	$("p.entry").click(function() {
		$("p.entry").not(this).nextAll().hide();
		$(this).nextAll().toggle(100);
	});

	$("#form1 button").click(function() {
		var recaptcha_validation = checkReCaptcha();
		
		if(recaptcha_validation.is_validated) {
			var parameters ={};
			parameters = {
				mnf: $("#form1 select[name=mnf]").val(),
				'g-recaptcha-response': recaptcha_validation.response,
				format: $("#format").val(),
				theme: $("#theme").val(),
				custom_theme_settings: ($("#theme").val() == "custom" ? theme_settings : null)
			};

			$.post("read.php?action=countMachinesByClass", JSON.stringify(parameters), function(data) {
					$("#d_content").attr('src', data);
				}, "json");
				
			grecaptcha.reset();
		} else {
			alert("Pamiętaj o weryfikacji reCAPTCHA!");
		}
	});
	
	$("#form2 button").click(function() {
		var recaptcha_validation = checkReCaptcha();

		if(recaptcha_validation.is_validated) {
			var parameters = {};
			parameters = {
				year: $("#form2 input[name=year]").val(),
				'g-recaptcha-response': recaptcha_validation.response,
				format: $("#format").val(),
				theme: $("#theme").val(),
				custom_theme_settings: ($("#theme").val() == "custom" ? theme_settings : null)
			};
			
			$.post("read.php?action=countMachinesByManufacturer", JSON.stringify(parameters), function(data) {
					$("#d_content").attr('src', data);
				}, "json");
				
			grecaptcha.reset();
		} else {
			alert("Pamiętaj o weryfikacji reCAPTCHA!");
		}
	});
	
	$("#form3 button").click(function() {
		var recaptcha_validation = checkReCaptcha();

		if(recaptcha_validation.is_validated) {
			var parameters = {};
			parameters = {
				eff_only: $("#form3 input[name=eff_only]").is(":checked"),
				'g-recaptcha-response': recaptcha_validation.response,
				format: $("#format").val(),
				theme: $("#theme").val(),
				custom_theme_settings: ($("#theme").val() == "custom" ? theme_settings : null)
			};
			
			$.post("read.php?action=get10MostExpensive", JSON.stringify(parameters), function(data) {
					$("#d_content").attr('src', data);
				}, "json");
				
			grecaptcha.reset();
		} else {
			alert("Pamiętaj o weryfikacji reCAPTCHA!");
		}
	});
	
	$("#form4 button").click(function() {
		var recaptcha_validation = checkReCaptcha();

		if(recaptcha_validation.is_validated) {
			var parameters = {};
			parameters = {
				mnf: $("#form4 select[name=mnf]").val(),
				'g-recaptcha-response': recaptcha_validation.response,
				format: $("#format").val(),
				theme: $("#theme").val(),
				custom_theme_settings: ($("#theme").val() == "custom" ? theme_settings : null)
			};
			
			$.post("read.php?action=getAverageCost", JSON.stringify(parameters), function(data) {
					$("#d_content").attr('src', data);
				}, "json");
				
			grecaptcha.reset();
		} else {
			alert("Pamiętaj o weryfikacji reCAPTCHA!");
		}
	});
	
	$("#form5 button").click(function() {
		var recaptcha_validation = checkReCaptcha();

		if(recaptcha_validation.is_validated) {
			var parameters = {};
			parameters = {
				date_from: $("#form5 input[name=date_from]").val(),
				date_to: $("#form5 input[name=date_to]").val(),
				'g-recaptcha-response': recaptcha_validation.response,
				format: $("#format").val(),
				theme: $("#theme").val(),
				custom_theme_settings: ($("#theme").val() == "custom" ? theme_settings : null)
			};
			
			$.post("read.php?action=getLastIssues", JSON.stringify(parameters), function(data) {
					$("#d_content").attr('src', data);
				}, "json");
				
			grecaptcha.reset();
		} else {
			alert("Pamiętaj o weryfikacji reCAPTCHA!");
		}
	});
	
	$("#form6 button").click(function() {
		var recaptcha_validation = checkReCaptcha();

		if(recaptcha_validation.is_validated) {
			var parameters = {};
			parameters = {
				cnt: $("#form6 input[name=cnt]").val(),
				dp_name: $("#form6 select[name=dp_name]").val(),
				'g-recaptcha-response': recaptcha_validation.response,
				format: $("#format").val(),
				theme: $("#theme").val(),
				custom_theme_settings: ($("#theme").val() == "custom" ? theme_settings : null)
			};
			
			$.post("read.php?action=getLastNonEfficient", JSON.stringify(parameters), function(data) {
					$("#d_content").attr('src', data);
				}, "json");
				
			grecaptcha.reset();
		} else {
			alert("Pamiętaj o weryfikacji reCAPTCHA!");
		}
	});
	
	$("#form7 button").click(function() {
		var recaptcha_validation = checkReCaptcha();

		if(recaptcha_validation.is_validated) {
			var parameters = {};
			parameters = {
				eff_only: $("#form7 input[name=eff_only]").is(":checked"),
				min_fee: $("#form7 input[name=min_fee]").val(),
				'g-recaptcha-response': recaptcha_validation.response,
				format: $("#format").val(),
				theme: $("#theme").val(),
				custom_theme_settings: ($("#theme").val() == "custom" ? theme_settings : null)
			};
			
			$.post("read.php?action=getMicrosoftMachines", JSON.stringify(parameters), function(data) {
					$("#d_content").attr('src', data);
				}, "json");
				
			grecaptcha.reset();
		} else {
			alert("Pamiętaj o weryfikacji reCAPTCHA!");
		}
	});
	
	$("#form8 button").click(function() {
		var recaptcha_validation = checkReCaptcha();

		if(recaptcha_validation.is_validated) {
			var parameters = {};
			parameters ={
				eff_only: $("#form8 input[name=eff_only]").is(":checked"),
				min_class: $("#form8 input[name=min_class]").val(),
				dp_name: $("#form8 select[name=dp_name]").val(),
				'g-recaptcha-response': recaptcha_validation.response,
				format: $("#format").val(),
				theme: $("#theme").val(),
				custom_theme_settings: ($("#theme").val() == "custom" ? theme_settings : null)
			};
			
			$.post("read.php?action=getOwnersAndDescriptions", JSON.stringify(parameters), function(data) {
					$("#d_content").attr('src', data);
				}, "json");
				
			grecaptcha.reset();
		} else {
			alert("Pamiętaj o weryfikacji reCAPTCHA!");
		}
	});
	
	$("#form9 button").click(function() {
		var recaptcha_validation = checkReCaptcha();

		if(recaptcha_validation.is_validated) {
			var parameters = {};
			parameters = {
				dt_owned: $("#form9 input[name=dt_owned]").val(),
				'g-recaptcha-response': recaptcha_validation.response,
				format: $("#format").val(),
				theme: $("#theme").val(),
				custom_theme_settings: ($("#theme").val() == "custom" ? theme_settings : null)
			};
			
			$.post("read.php?action=getOwnersTop50", JSON.stringify(parameters), function(data) {
					$("#d_content").attr('src', data);
				}, "json");
				
			grecaptcha.reset();
		} else {
			alert("Pamiętaj o weryfikacji reCAPTCHA!");
		}
	});
	
	$("#form10 button").click(function() {
		var recaptcha_validation = checkReCaptcha();

		if(recaptcha_validation.is_validated) {
			var parameters = {};
			parameters = {
				mnf: $("#form10 select[name=mnf]").val(),
				'g-recaptcha-response': recaptcha_validation.response,
				format: $("#format").val(),
				theme: $("#theme").val(),
				custom_theme_settings: ($("#theme").val() == "custom" ? theme_settings : null)
			};
			
			$.post("read.php?action=getTotalCosts", JSON.stringify(parameters), function(data) {
					$("#d_content").attr('src', data);
				}, "json");
				
			grecaptcha.reset();
		} else {
			alert("Pamiętaj o weryfikacji reCAPTCHA!");
		}
	});
});