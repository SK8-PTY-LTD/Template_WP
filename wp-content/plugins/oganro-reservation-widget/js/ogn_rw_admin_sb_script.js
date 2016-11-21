jQuery(function($){

	var count = $('#hidden_fields_wrap').children().length;
	var bootstrap = $('#ogn_sb_bootstrap').val();
	

	$('#add_field_btn').click(function(){

		count++;

		var fields 	= 	'<div class="hfield" id="hfield'+count+'">'+
						'<input type="text" value="" name="ogn_hxsw_opt_hfields['+count+'][name]" placeholder="Name">'+
					 	'<input type="text" value="" name="ogn_hxsw_opt_hfields['+count+'][value]" placeholder="Value" style="margin-left:5px;">'+
					 	'<a class="button button-danger h_field_rmv_btn" onClick="remove_field(hfield'+count+')" style="background-color:#e04848;color:white;border-radius:15px;margin-left:3px">X</a>'+
					 	'</div>';
		$('#hidden_fields_wrap').prepend(fields);


	});

	$("#ogn_rw_reset_btn").click(function(){
		if(confirm("Are you sure ?")){
			$('#ogn_rw_reset_form').submit();
		}
		
	});

	$("#ToggleSwitchSample").toggleSwitch();

	$('#ToggleSwitchSample').toggleCheckedState(parseInt(bootstrap));

	$(".toggle-selector").toggleSwitch({
	  highlight: true, // default
	  width: 25, // default
	  change: function(e) {
	    // default null
	  },
	  stop: function(e,val) {
	    // default null
	  }
	});


	$( "#ogn_rw_slider" ).slider({
      range: "max",
      min: 0,
      max: 100,
      value: ($('#opacity').val() * 100),
      step:10,
      animate: "fast",
      slide: function( event, ui ) {
        $( "#opacity" ).val( (ui.value / 100 ) );
      }
    }).each(function() {

  //
  // Add labels to slider whose values 
  // are specified by min, max and whose
  // step is set to 1
  //

  // Get the options for this slider
  var opt = $(this).data().uiSlider.options;
  
  // Get the number of possible values
  var vals = opt.max - opt.min;
  vals = vals/10;
  // Space out values
  for (var i = 0; i <= vals; i++) {
    
    var el = $('<label>'+(i/10)+'</label>').css('left',(i/vals*100)+'%');
  
    $( "#ogn_rw_slider" ).append(el);
    
  }
  
});
;
    $( "#opacity" ).val( ($( "#opacity" ).slider( "value" ) / 100) );


});

function remove_field(id){
	id.remove();
}