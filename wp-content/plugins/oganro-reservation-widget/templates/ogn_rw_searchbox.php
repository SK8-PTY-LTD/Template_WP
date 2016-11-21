<?php
	if ( ! defined( 'ABSPATH' ) ) exit;
?>
<div id="search_box_wrap" class="entry-content" 
		style="	background-color:rgba(<?php echo esc_attr($ogn_rw_background_rgba); ?>, <?php echo esc_attr($ogn_rw_opacity); ?>);
				padding:10px;
				border-radius:<?php echo esc_attr($ogn_rw_border_radius);?>px;
				border: <?php echo esc_attr($ogn_rw_border_width); ?>px solid <?php echo esc_attr($ogn_rw_border_color); ?>;
				width:<?php echo esc_attr($ogn_rw_search_box_width); ?>%;
				display: inline-block;
				margin:0;">

	<div class="" >
		<div id="main_top_bg" class="ogh_formwrap">
			<div class="top_searchbox_row">

				<div class="background_title col-lg-12 text-center">
					<div class="maintitle_italic">
						<h2 style="color:<?php echo esc_attr($ogn_rw_title_color); ?>;font-size:<?php echo esc_attr($ogn_rw_title_size); ?>px;" id="search_title"><?php echo esc_html($ogn_rw_title); ?></h2>
					</div>
				</div>

				<div class="clearfix"></div>

				<form method="POST" action="<?php echo esc_url($ogn_rw_submit_url); ?>">
					<div id="search_box_main_wrap">

						<div class="form-group">
							<div class="form-group col-lg-6 col-sm-12 col-custom-full ogh_searchbox">
			        			<label class="control-label siteheader_title" for="exampleInputEmail1" style="color:<?php echo esc_attr($ogn_rw_label_color); ?>;"><?php echo esc_html($ogn_rw_location_title); ?></label>
					  			<div class="input-group special_englishfont_load control-label">
			           				<span class="input-group-addon"><i class="glyphicon glyphicon-map-marker" style="color:<?php echo esc_attr($ogn_rw_icon_color); ?>;"></i></span>
									<input class="form-control ui-autocomplete-input" type="text" id="search-box" placeholder="<?php echo esc_attr($ogn_rw_location_placeholder); ?>" name="autocompleter_city" value="" required="" data-toggle="tooltip" data-placement="bottom" title="Please type in and select a city from the list." autocomplete="off" style="height:inherit;">
								</div>	
							</div>

							<div class="form-group col-lg-3 col-md-12 col-sm-12  col-custom-full ogh_checkin">
						      	<label class="control-label" for="exampleInputEmail1" style="color:<?php echo esc_attr($ogn_rw_label_color); ?>;"><?php echo esc_html($ogn_rw_checkin_title); ?></label>
						      	<div class="input-group date  from_date">
						        	<input id="check_in" name="tmp_date_from" type="text" class="form-control" required="" style="height:inherit;">
						        	<span class="input-group-addon"><i class="glyphicon glyphicon-calendar" style="color:<?php echo esc_attr($ogn_rw_icon_color); ?>;"></i></span>
						      	</div>
						    </div>

						    <div class="form-group col-lg-3 col-md-12 col-sm-12 col-custom-full ogh_checkout">
						      	<label class="control-label" for="exampleInputEmail1" style="color:<?php echo esc_attr($ogn_rw_label_color) ?>;"><?php echo esc_html($ogn_rw_checkout_title); ?></label>
						      		<div class="input-group date to_date">
						        
						        	<input id="check_out" name="tmp_date_to" type="text" class="form-control" required="" style="height:inherit;">
						        	<span class="input-group-addon"><i class="glyphicon glyphicon-calendar" style="color:<?php echo esc_attr($ogn_rw_icon_color); ?>;"></i></span>
						      	</div>
						    </div>
					    </div>

					    <div class="form-group">
							<div class="form-group col-lg-4 col-md-12 col-sm-12 ogh_nights">
						 		<label class="control-label" for="exampleInputEmail1" style="color:<?php echo esc_attr($ogn_rw_label_color); ?>;"><?php echo esc_html($ogn_rw_nights_title); ?></label>
					 			<div class="dropdown" id="nights" data-toggle="popover" data-trigger="hover" data-content=""  data-placement="top">
									<button class="btn btn-default" type="button" id="dropdownMenu" data-toggle="dropdown" data-value="<?php echo esc_attr($ogn_rw_nights); ?>" aria-haspopup="true" aria-expanded="true" style="width:100%;text-align:left;">
									    <span id="nights_val"><?php echo esc_html($ogn_rw_nights); ?> Nights</span> 
									    <span class="caret" style="position: absolute;right: 0;margin: 10px 15px;color:<?php echo esc_attr($ogn_rw_icon_color); ?>;"></span>
									</button>
									<ul class="dropdown-menu" aria-labelledby="dropdownMenu" id="nights_select" style="max-height:200px;overflow-x:hidden;">
									    <?php for($i = 1; $i < 31;$i++): ?>
									    	<li><a data-value="<?php echo $i ?>" ><?php echo $i ?></a></li>
									    <?php endfor; ?>
									</ul>
								</div>
					 		</div>

					 		<div class="form-group rooms col-lg-4 col-md-12 col-sm-12 col-custom-full ogh_rooms">
								<label class="control-label" for="exampleInputEmail1" style="color:<?php echo esc_attr($ogn_rw_label_color); ?>;"><?php echo esc_html($ogn_rw_rooms_title); ?></label>
								<div class="dropdown" id="rooms_select" data-toggle="popover" data-trigger="hover" data-content="" data-title="Room Occupancy Details" data-placement="top">
									<button class="btn btn-default" type="button" id="dropdownMenu1" data-toggle="dropdown" data-value="-2" aria-haspopup="true" aria-expanded="true" style="width:100%;text-align:left;">
									    <span id="rooms_val">1 Room 2 Adults</span> 
									    <span class="caret" style="position: absolute;right: 0;margin: 10px 15px;color:<?php echo esc_attr($ogn_rw_icon_color); ?>;"></span>
									</button>
									<ul class="dropdown-menu" aria-labelledby="dropdownMenu1" id="rooms_select">
									    <li><a data-value="-2">1 Room 2 adults</a></li>
									    <li><a data-value="-1">1 Room 1 adults</a></li>
									    <li role="separator" class="divider"></li>
									    <li><a data-value="1">1 Room</a></li>
									    <li><a data-value="2">2 Room</a></li>
									    <li><a data-value="3">3 Room</a></li>
									    <li><a data-value="4">4 Room</a></li>
									    <li><a data-value="5">5 Room</a></li>
									</ul>

								</div>
							</div> 
							<div class="hidden_fields">
								<input type="hidden" name="hotelMappingId" value="0" id="hotelMappingId"/>
								<input type="hidden" name="changedfiled" value="" id="changedfiled"/>
								<input type="hidden" name="showcity" value="true" id="showcity"/>
								<input type="hidden" name="reqcurrency" value="GBP" id="box_reqcurrency"/>
								<input type="hidden" name="cityName" value="" id="cityName"/> 
								<input type="hidden" name="cityId" value="" id="cityId"/>
								<input type="hidden" name="datefrom" value="" id="datefrom"/>
								<input type="hidden" name="dateto" value="" id="dateto"/>
								<input type="hidden" name="noofnights" value="<?php echo esc_attr($ogn_rw_nights); ?>" id="noofnights"/>
								<input type="hidden" name="starrate" value="-2" id="starrate"/>
								<input type="hidden" name="noofrooms" value="1" id="noofrooms"/>
									
								<?php foreach($ogn_rw_hidden_fields as $hidden_field): ?>
									<input type="hidden" name="<?php echo esc_attr($hidden_field->name); ?>" value="<?php echo esc_attr($hidden_field->value); ?>">
								<?php endforeach; ?>
								<div id="room_details_wrap">
									<input type="hidden" name="adults" value="2"/>
									<input type="hidden" name="children" value="0"/>
								</div>
							</div>

							<?php
								if($ogn_rw_rooms_title && $ogn_rw_nights_title){
									$margin_top = 4;
								}else{
									$margin_top = 0;
								}
							?>

							<div class="form-group rooms col-lg-4 col-md-12 col-sm-12 col-custom-full ogh_rooms">
								<label class="control-label" for="exampleInputEmail1"></label>
								<input type="submit" value="<?php echo esc_attr($ogn_rw_button_text); ?>" class="btn btn-primary btn-block" style="color:<?php echo esc_attr($ogn_rw_button_text_color); ?>;background-color:<?php echo esc_attr($ogn_rw_button_background_color); ?>;margin-top:<?php echo esc_attr($margin_top); ?>px"/>
						    </div>
					    </div>

					</div>
				</form>

				<!-- Room ocupancies data modal -->
				<div class="modal fade" id="ogn_rw_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
				  <div class="modal-dialog" role="document">
				    <div class="modal-content">
				      <div class="modal-header">
				        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				        <h4 class="modal-title" id="myModalLabel">Room Occupancy Details</h4>
				      </div>
				      <div class="modal-body">
				        
				      </div>
				      <div class="modal-footer">
				        <button type="button" id="room_details_update_btn" class="btn btn-primary">Update</button>
				      </div>
				    </div>
				  </div>
				</div>

				<input type="hidden" value="<?php echo esc_attr($ogn_rw_nights); ?>" id="ogn_rw_def_nights">
				<input type="hidden" value="<?php echo esc_attr($ogn_rw_submit_url); ?>" id="ogn_rw_def_submit_url">
				<input type="hidden" value="<?php echo esc_attr($ogn_rw_autocomplete_url); ?>" id="ogn_rw_def_autocomplete_url">
				<input type="hidden" value="<?php echo esc_attr($ogn_rw_date_format); ?>" id="ogn_rw_def_date_format">

			</div>
		</div>
	</div>
</div>
<style>
	.popover .popover-title{
		margin-top: 0;
	}

	#search_title{
		margin-bottom: 0px;
	}

	@media screen and (max-width: 768px) {
	    #search_title{
			margin-bottom: 15px;
		}
	}
</style>