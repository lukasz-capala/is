var readCSV;

$(document).ready(function() {
	var computers = [];

	$("#get").click(function() {
		computers = [];
		
		$.get("data.csv", function(data) {
			var text = data;
			var lines = [];
			
			lines = text.split("\n");
			var line = [];
			var headers = lines[0].split("|");
			var computer = {};
			
			for(var i = 1; i < lines.length; i++) {
				line = lines[i].split("|");
				for(var j = 0; j < headers.length; j++) {
					computer[headers[j]] = line[j];
				}
				
				computers.push(computer);
				computer = {};
			}
			
			$("#send").prop("disabled", false);
			$("#csv_loaded p").text("✓ Załadowano pomyślnie "+computers.length+" wierszy.");
			$("#csv_loaded").show(200);
		});
		
		
	});
	
	$("#send").click(function() {
		$("#send").prop("disabled", true);
		$("#send").text("Proszę czekać");
		
	    $.post("write.php", JSON.stringify(computers), function(data) {
				$("#write_results p").text("✓ Zapisano pomyślnie "+data+" rekordów.");
				$("#write_results").show(200);
				
				$("#send").text("Zapisz");
	        }, "json");
	});
});

//✓