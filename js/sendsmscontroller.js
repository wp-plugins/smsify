$=jQuery;
$(document).ready(function() {

	jQuery("#smsify-schedule-date").datepicker({
        dateFormat : 'dd-M-yy',
        minDate: 0,
        maxDate: 365
    });
    
    $("#smsify-scheduler").click( function (e) {
    	if(e.target.checked) {
    		$("#smsify-scheduleblock1").show();
    		$("#smsify-scheduleblock2").show();		
    	} else {
    		$("#smsify-scheduleblock1").hide();
    		$("#smsify-scheduleblock2").hide();
    	}
   	});
    	
	$(".smsify-send-group-sms").click( function (e) {
		e.preventDefault();
		var tag_id = $("#edittag").find('input[name="tag_ID"]').val();
		var taxonomy = $("#edittag").find('input[name="taxonomy"]').val();
		var message = $("#smsify_message").val();
		var sender_id = $("#smsify_sender_id").val();
		
		var scheduler = $("#smsify-scheduler").prop("checked");
		var schedule_date = $("#smsify-schedule-date").val();
		var schedule_time = $("#smsify-schedule-time").val();
		var schedule_date_time = "";
		var confirmationMessage = $("#smsify_confirmation").val();
		
		if(scheduler && schedule_date != "" && schedule_time != "") {
			var d = new Date(schedule_date);
			var schedule_date_time = addMinutes(d, schedule_time);
		}
		
		//convert boolean to 0 and 1 for proper php conversion
		if(scheduler) {
			scheduler = 1;
		} else {
			scheduler = 0;
		}
		
		var smsifyData = {
			"action": "smsify_sms_group_handler",
			"method": "sendBulkSMS",
			"message": message,
			"taxonomy": taxonomy,
			"tag_id": tag_id,
			"scheduler": scheduler,
			"schedule_date_time": schedule_date_time,
		};
		
		if(sender_id) {
			smsifyData["sender_id"] = sender_id;
		}
		
		if(confirm(confirmationMessage)) {
			showSending();
			$.ajax({
			  url: ajaxurl,
			  type: "POST",
			  data: smsifyData,
			  success: onSuccess,
			  error: smsError
			});
		}
	});
	
	$(".smsify-sendsms").click( function (e) {
		e.preventDefault();
		showSending();
		var user_id = $("#user_id").val();
		var first_name = $("#first_name").val();
		var last_name = $("#last_name").val();
		var mobile = $("#smsify_mobile").val();
		var message = $("#smsify_message").val();
		var sender_id = $("#smsify_sender_id").val();
		var scheduler = $("#smsify-scheduler").prop("checked");
		var schedule_date = $("#smsify-schedule-date").val();
		var schedule_time = $("#smsify-schedule-time").val();
		var schedule_date_time = "";
		
		if(scheduler && schedule_date != "" && schedule_time != "") {
			var d = new Date(schedule_date);
			var schedule_date_time = addMinutes(d, schedule_time);
		}
		
		//convert boolean to 0 and 1 for proper php conversion
		if(scheduler) {
			scheduler = 1;
		} else {
			scheduler = 0;
		}
		
		var smsifyData = {
			"action": "smsify_sms_handler",
			"method": "sendBulkSMS",
			"user_id": user_id,
			"first_name": first_name,
			"last_name": last_name,
			"send_to": mobile,
			"scheduler": scheduler,
			"schedule_date_time": schedule_date_time,
			"message": message
		};
		
		if(sender_id) {
			smsifyData["sender_id"] = sender_id;
		}
		$.ajax({
		  url: ajaxurl,
		  type: "POST",
		  data: smsifyData,
		  success: onSuccess,
		  error: smsError
		});
    });
    
    function onSuccess(e) {
    	var response = $.parseJSON(e);
    	var type = "";
    	if(response.status == true) {
    		type = "updated";
    	} else {
    		type = "error";
    	}
    	showMessage(response.message,type);
    }
    
    function smsError(e) {
    	var response = $.parseJSON(e);
    	var type = "error";
    	showMessage(response.message,type);
    	console.log(e);
    }
    
    function showSending() {
    	$(".smsify-send .form-table").hide();
    	$(".smsify-confirmation").hide();
		$(".smsify-sending").show();	
   	}
   	
   	function resetForm() {
    	$(".smsify-send .form-table").show();
    	$(".smsify-confirmation").show();
		$(".smsify-sending").hide();	
   	}
   	
   	function showMessage(message,type) {
		$(".smsify-sending").hide();
		$(".smsify-send .form-table").show();
		$(".smsify-confirmation").addClass(type);
		$(".smsify-confirmation").html('<p>'+message+'</p>');
		$(".smsify-confirmation").show();
   	}
   	
   	function addMinutes(date, minutes) {
	    return new Date(date.getTime() + minutes*60000);
	}
	
});

function toggleAPIKey(e, val1, val2, val3, val4) {
	e.preventDefault();
	if($("#apiKey").val() == val1) {
		$("#apiKey").attr("value",val2);
		$("#smsify_toggle_key").html(val4);
	} else {
		$("#apiKey").attr("value",val1);
		$("#smsify_toggle_key").html(val3);
	}
}