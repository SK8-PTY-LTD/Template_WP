jQuery(function($){


	var selected_room_data 	= {};
	var def_nights			= $('#noofnights').val();
	var s_box_val 			= '';
	var autocomplete_url	= $('#ogn_rw_def_autocomplete_url').val();
	var nights				= $('#ogn_rw_def_nights').val();
	var date_format			= $('#ogn_rw_def_date_format').val();
	var search_box_width	= $('#search_box_wrap').outerWidth();
	var start_date_gap		= 3;
	var start_min_date_gap	= start_date_gap+1;
	var cache = {};



	var content = '<label class="room_label">Room <label class="label label-primary">1</label></label><br>'+
					 	'<label>adults  <span class="badge">2</span></label><br>'+
					 	'<label>Children  <span class="badge">0</span></label><br><br>';
	
	resize_search_box();

 	
	$('#search-box').click(function(){
		s_box_val = $(this).val();
			$(this).val('');
	});

	
	$('#search-box').focusout(function(){
		if($(this).val()){

		}else{
			$(this).val(s_box_val);
		}
	});
    

	// set datepicker values
	$( "#check_in" ).datepicker({
      	defaultDate: '+1',
      	changeMonth: true,
      	minDate: 0,
      	dateFormat: date_format,
      	onSelect:function(selectedDate){
    	  	var date = $(this).datepicker('getDate');
    	  	var curr_date = new Date();
    	  	var date_diff = $('#dropdownMenu').attr('data-value');
    	  	var out_date = $(this).datepicker('getDate');
    	  	$( "#check_out" ).datepicker('option', 'minDate', new Date(date.getFullYear(),date.getMonth(),date.getDate()+1));
    	  	date.setDate(date.getDate() + parseInt(date_diff));
    	  	$( "#check_out" ).datepicker("setDate",  date);
    	  	var check_out_date = $('#check_out').datepicker('getDate');
    	  	var min_date = get_date_diff(out_date.getFullYear(),out_date.getMonth()+1,out_date.getDate(),check_out_date.getFullYear(),check_out_date.getMonth()+1,check_out_date.getDate());
				if(min_date > 30){
					min_date = 30;
		}
		$('#noofnights').val(min_date);
	  }
    });
    $( "#check_in" ).datepicker("setDate", '+'+start_date_gap);
    $( "#check_out" ).datepicker({
      	defaultDate: +1,
      	changeMonth: true,
      	dateFormat: date_format,
      	minDate: '+'+start_min_date_gap	,
      	onSelect:function(selectedDate){
    	  	var curr_date = $('#check_in').datepicker('getDate');
    	  	var date = $(this).datepicker('getDate');
    	 	var min_date = get_date_diff(curr_date.getFullYear(),curr_date.getMonth()+1,curr_date.getDate(),date.getFullYear(),date.getMonth()+1,date.getDate());
			if(min_date > 30){
				min_date = 30;
		}
		$('#nights_val').text(min_date+' Nights');
		$('#dropdownMenu').attr('data-value',min_date);
		$('#noofnights').val(min_date);
      }
    });
    var checkout_nights = parseInt(def_nights)+parseInt(start_date_gap);
    $( "#check_out" ).datepicker("setDate", '+'+checkout_nights);
    
	$('#nights_select li a').click(function(){

		var date = $("#check_in").datepicker('getDate');
    	var date_diff = $(this).text();
    	date.setDate(date.getDate() + parseInt(date_diff));
    	$( "#check_out" ).datepicker("setDate", date);
		$('#noofnights').val($(this).text());
		$('#dropdownMenu').attr('data-value',date_diff);
		$('#nights_val').text(date_diff+' Nights');
	});

	$('#rooms_select li a').click(function(){
		var hidden_fields = "";
		var room_count = parseInt($(this).attr('data-value'));
		var room_count_val = room_count;
		var val = $('#rooms_select li a[data-value = "'+room_count+'"]').text();
		if(room_count == "-1" || room_count == "-2"){
			room_count_val = "1";
		}
		$('#dropdownMenu1').attr('data-value',room_count);
		$('#noofrooms').val(room_count_val);
		$('#rooms_val').text(val);
		$('.modal-body').empty();
		if(room_count > 0){
			fields = "";

			for(i = 0;i < room_count; i++){

				if (selected_room_data[i] == null) {
					fields += '<div id="room_select_wrapper'+i+'" rel="'+i+'"><label class="room_label">Room <label class="label label-primary">'+(i+1)+'</label></label><br>'+
						 '<label>Adults</label><select id="adult_select'+i+'"><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option></select>'+
						 '<label>Children</label><select id="children_select'+i+'" class="children_select"><option value="0">0</option><option value="1">1</option><option value="2">2</option><option value="3">3</option></select><br><br></div>';
					
					hidden_fields += '<input type="hidden" name="adults" value="1"/>'+
						  '<input type="hidden" name="children" value="0"/>';

				}else{
					a_select = "";
					c_select = "";
					var children_age_fields = '';
					hidden_fields +=  '<input type="hidden" name="adults" value="'+selected_room_data[i]['adults']+'"/>';
					hidden_fields +=  '<input type="hidden" name="children" value="'+selected_room_data[i]['childrens']+'"/>';
					// generate adults select options
					for(j = 1; j< 6; j++){
						if(selected_room_data[i]['adults'] == j){
							a_select += '<option value="'+j+'" selected>'+j+'</option>';
						}else{
							a_select += '<option value="'+j+'">'+j+'</option>';
						}
					}

					// Generate childrens select options
					for(k = 0; k < 4 ; k++){
						if(selected_room_data[i]['childrens'] == k){
							c_select += '<option value="'+k+'" selected>'+k+'</option>';
						}else{
							c_select += '<option value="'+k+'">'+k+'</option>';
						}
					}
					// Generate age options
					if(selected_room_data[i]['childrens'] > '0' && selected_room_data[i]['ages'] != null ){

						children_age_fields = '<div id="children_age_warpper'+i+'"><label>Age of Children</label><br>';
						for(var l = 1; l <= selected_room_data[i]['childrens'] ; l++){
							  var p = l-1;
							
								
								var options = '';
								for(var m = 1; m < 18 ; m++){
									if(selected_room_data[i]['ages'][p] == m){
										options += '<option value="'+m+'" selected>'+m+'</option>';
									}else{
										options += '<option value="'+m+'">'+m+'</option>';
									}
								}
								children_age_fields += '<select id="age_select_'+i+'_'+l+'">'+options+'</select>';
								hidden_fields += '<input type="hidden" name="age" value="'+selected_room_data[i]['ages'][p]+'"/>';
						}
						children_age_fields += "</div>";
					}
					fields += '<div id="room_select_wrapper'+i+'" rel="'+i+'"><label class="room_label">Room <label class="label label-primary">'+(i+1)+'</label></label><br>'+
						 '<label>Adults</label><select id="adult_select'+i+'">'+a_select+'</select>'+
						 '<label>Children</label><select id="children_select'+i+'" class="children_select">'+c_select+'</select>'+children_age_fields+'<br><br></div>';
				}
			}

			$('#room_details_wrap').empty();
			$('#room_details_wrap').append(hidden_fields);
			
			$('.modal-body').append(fields);
			$('#ogn_rw_modal').modal();
			load_children_select();

		}else if(room_count == "-1"){

			var fields = "";
			fields 	+= 	'<input type="hidden" name="adults" value="1"/>'+
		  				'<input type="hidden" name="children" value="0"/>'+
		  				'<input type="hidden" name="rooms" value="1"/>';
			$('#room_details_wrap').empty();
			$('#room_details_wrap').append(fields);

			content = 	'<label class="room_label">Room <label class="label label-primary">1</label></label><br>'+
			 			'<label>adults  <span class="badge">1</span></label><br>'+
			 			'<label>Children  <span class="badge">0</span></label><br><br>';

			set_popover_value(content);

		}else if(room_count == "-2"){

			var fields = "";
			fields 	+= 	'<input type="hidden" name="adults" value="2"/>'+
			  			'<input type="hidden" name="children" value="0"/>'+
			  			'<input type="hidden" name="rooms" value="1"/>';
			$('#room_details_wrap').empty();
			$('#room_details_wrap').append(fields);

			content = 	'<label class="room_label">Room <label class="label label-primary">1</label></label><br>'+
			 			'<label>adults  <span class="badge">2</span></label><br>'+
			 			'<label>Children  <span class="badge">0</span></label><br><br>';

			set_popover_value(content);
		}
		
	});

	// update hidden fields and popover values
	$('#room_details_update_btn').click(function(){
		
		var room_count = parseInt($('#dropdownMenu1').attr('data-value'));
		var fields = '';
			var content = '';
			
			for(i=0; i < room_count;i++){
				var count = i - 1;
				fields += '<input type="hidden" name="adults" value="'+$('#adult_select'+i).val()+'"/>'+
						  '<input type="hidden" name="children" value="'+$('#children_select'+i).val()+'"/>';

			   content += '<label class="room_label">Room <label class="label label-primary">'+(i+1)+'</label></label><br>'+
			   			 '<label>adults  <span class="badge">'+$('#adult_select'+i).val()+'</span></label><br>'+
			   			 '<label>Children  <span class="badge">'+$('#children_select'+i).val()+'</span></label><br><br>';

			   selected_room_data[i] = {};

				selected_room_data[i]['childrens'] = $('#children_select'+i).val();
				selected_room_data[i]['adults']	= $('#adult_select'+i).val();
				

				var children_count = $('#children_select'+i).val();
				selected_room_data[i]['ages'] = {};
				
				for(var j = 1; j <= children_count; j++){
					var ch = j -1;
					fields += '<input type="hidden" name="age" value="'+$('#age_select_'+i+'_'+j).val()+'"/>';
					selected_room_data[i]['ages'][ch] = $('#age_select_'+i+'_'+j).val();
				}
			}

			set_popover_value(content);
			
			
			$('#room_details_wrap').empty();
			$('#room_details_wrap').append(fields);

		$('#ogn_rw_modal').modal('hide');


	});

	try{
    	$("#search-box").autocomplete({
    		source : function(request, response) {
    		    if ( request.term in cache ) {
	    			response( cache[ request.term ] );
	    			return;
    		    } 
    		    $.ajax({
    			url : autocomplete_url,
    			type: "POST",
    			dataType : "jsonp",
    			data : {
    			    featureClass : "P",
    			    style : "full",
    			    maxRows : 12,
    			    term : request.term
    			},
    			success : function(data) {
    			    returnData = $.map(data.cityNameIdList, function(item) {
	    				return {
	    				    label : item.myValue,
	    				    cityId: item.myKey
	    				};
    			    }); 
    			    cache[ request.term ] = returnData;
    			    response(returnData);
    			}
    		    });
    		},
    		
    		delay: 300,
    		minLength : 3,
    		scroll: true,
    		height:   200,
    		select : function(event, ui) {
    		    $('#cityName').val(ui.item.label);
    		    $('#cityId').val(ui.item.cityId);
    		},
    		open : function() {
    		    $(this).removeClass("ui-corner-all").addClass("ui-corner-top");
    		},
    		close : function() {
    		    $(this).removeClass("ui-corner-top").addClass("ui-corner-all");
    		}
    	});	
	
	}catch(ex){
		console.log("ERRROR while generating widget .....");
	}

    // checking city selection on form submit
    $('form').submit(function(){
			if($('#cityId').val()){
			     
			     var m_names = new Array("Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec");
			     var fromDate = new Date($("#check_in").datepicker('getDate'));
			     var toDate = new Date($("#check_out").datepicker('getDate'));
			     
			     var newDateFrom = fromDate.getDate() + "-" + m_names[fromDate.getMonth()] + "-" + fromDate.getFullYear();
			     var newDateTo = toDate.getDate() + "-" + m_names[toDate.getMonth()] + "-" + toDate.getFullYear();
			     $("#datefrom").val(newDateFrom);
			     $("#dateto").val(newDateTo);
			     $("#check_in").val(newDateFrom);
			     $("#check_out").val(newDateTo);

			     return true;

			}else{
				$('#search-box').tooltip('show');
				return false;
			}
	});

    
	set_popover_value(content);

    function get_date_diff(from_year,from_month,from_date,to_year,to_month,to_date){
		var oneDay = 24 * 60 * 60 * 1000; // hours*minutes*seconds*milliseconds
		var firstDate = new Date(from_year,from_month,from_date);
		var secondDate = new Date(to_year, to_month, to_date);
		 
		return diffDays = Math.round(Math.abs((firstDate.getTime() - secondDate.getTime()) / (oneDay)));
	}

	function set_popover_value(content){
		$('[data-toggle="popover"]').popover({html:true}); 
		$('#rooms_select').attr('data-content',content);
		$('#rooms_select').attr('title','Room Occupancy Details');
	}

	function load_children_select(){
			
		$('.children_select').change(function(){
			var children_count = $(this).val();
			var parent_id = $(this).parent().attr('rel');
			$('#children_age_warpper'+parent_id).remove();
			if(children_count > 0){
				var field = '<div id="children_age_warpper'+parent_id+'"><label>Age of Children</label><br>'; 
				for(var i = 1; i <= children_count; i++){
					var options = '';
					for(var j = 1; j < 18 ; j++){
						options += '<option value="'+j+'">'+j+'</option>';
					}
					field += '<select id="age_select_'+parent_id+'_'+i+'">'+options+'</select>';
				}
				field += '</div>';
				$(field).insertAfter(this);
			}
		});
	}

	function resize_search_box(){

		if(search_box_width < 600){
			$('.ogh_searchbox').removeClass('col-lg-6');
			$('.ogh_checkin').removeClass('col-lg-3');
			$('.ogh_checkout').removeClass('col-lg-3');
			$('.ogh_nights').removeClass('col-lg-4');
			$('.ogh_rooms').removeClass('col-lg-4');
		}
	}

});