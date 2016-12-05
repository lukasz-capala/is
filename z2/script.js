$(document).ready(function() {
	/** formatter for date inputs **/
	$("input[name=date_from], input[name=date_to], input[name=dt_owned]").formatter({
	  'pattern': '{{9999}}-{{99}}-{{99}}',
	  'persistent': false
	});
	
	/** slider change **/
	$("#form6 input[type=range]").on("input change", function() {
		$("#form6 span.cnt").text($(this).val());
	});
	
	/** standard behaviour **/
	$("p.entry").nextAll().hide();
	$("p.entry").click(function() {
		$("p.entry").not(this).nextAll().hide();
		$(this).nextAll().toggle(100);
	});
		
	$("#form1 button").click(function() {
		var parameters = [];
		parameters.push({mnf: $("#form1 select[name=mnf]").val()});
		
	    $.post("read.php?action=countMachinesByClass", JSON.stringify(parameters), function(data) {
				$("#d_content").attr('src', 'output.php');
	        }, "json");
	});
	
	$("#form2 button").click(function() {
		var parameters = [];
		parameters.push({year: $("#form2 input[name=year]").val()});
		
	    $.post("read.php?action=countMachinesByManufacturer", JSON.stringify(parameters), function(data) {
				$("#d_content").attr('src', 'output.php');
	        }, "json");
	});
	
	$("#form3 button").click(function() {
		var parameters = [];
		parameters.push({eff_only: $("#form3 input[name=eff_only]").is(":checked")});
		console.log(parameters);
		
	    $.post("read.php?action=get10MostExpensive", JSON.stringify(parameters), function(data) {
				$("#d_content").attr('src', 'output.php');
	        }, "json");
	});
	
	$("#form4 button").click(function() {
		var parameters = [];
		parameters.push({mnf: $("#form4 select[name=mnf]").val()});
		
	    $.post("read.php?action=getAverageCost", JSON.stringify(parameters), function(data) {
				$("#d_content").attr('src', 'output.php');
	        }, "json");
	});
	
	$("#form5 button").click(function() {
		var parameters = [];
		parameters.push(
		{date_from: $("#form5 input[name=date_from]").val()},
		{date_to: $("#form5 input[name=date_to]").val()}
		);
		
	    $.post("read.php?action=getLastIssues", JSON.stringify(parameters), function(data) {
				$("#d_content").attr('src', 'output.php');
	        }, "json");
	});
	
	$("#form6 button").click(function() {
		var parameters = [];
		parameters.push(
		{cnt: $("#form6 input[name=cnt]").val()},
		{dp_name: $("#form6 select[name=dp_name]").val()}
		);
		
	    $.post("read.php?action=getLastNonEfficient", JSON.stringify(parameters), function(data) {
				$("#d_content").attr('src', 'output.php');
	        }, "json");
	});
	
	$("#form7 button").click(function() {
		var parameters = [];
		parameters.push(
		{eff_only: $("#form7 input[name=eff_only]").is(":checked")},
		{min_fee: $("#form7 input[name=min_fee]").val()}
		);
		
	    $.post("read.php?action=getMicrosoftMachines", JSON.stringify(parameters), function(data) {
				$("#d_content").attr('src', 'output.php');
	        }, "json");
	});
	
	$("#form8 button").click(function() {
		var parameters = [];
		parameters.push(
		{eff_only: $("#form8 input[name=eff_only]").is(":checked")},
		{min_class: $("#form8 input[name=min_class]").val()},
		{dp_name: $("#form8 select[name=dp_name]").val()}
		);
		
	    $.post("read.php?action=getOwnersAndDescriptions", JSON.stringify(parameters), function(data) {
				$("#d_content").attr('src', 'output.php');
	        }, "json");
	});
	
	$("#form9 button").click(function() {
		var parameters = [];
		parameters.push({dt_owned: $("#form9 input[name=dt_owned]").val()});
		
	    $.post("read.php?action=getOwnersTop50", JSON.stringify(parameters), function(data) {
				$("#d_content").attr('src', 'output.php');
	        }, "json");
	});
	
	$("#form10 button").click(function() {
		var parameters = [];
		parameters.push({mnf: $("#form10 select[name=mnf]").val()});
		
	    $.post("read.php?action=getTotalCosts", JSON.stringify(parameters), function(data) {
				$("#d_content").attr('src', 'output.php');
	        }, "json");
	});
});