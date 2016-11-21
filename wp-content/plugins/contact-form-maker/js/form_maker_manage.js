function fm_check_page_load() {
  if (!document.getElementById('araqel') || (document.getElementById('araqel').value == '0')) {
    alert('Please wait while page is loading.');
    return false;
  }
  else {
    return true;
  }
}

function remove_whitespace(node) {
  var ttt;
  for (ttt = 0; ttt < node.childNodes.length; ttt++) {
    if (node.childNodes[ttt] && node.childNodes[ttt].nodeType == '3' && !/\S/.test(node.childNodes[ttt].nodeValue)) {
      node.removeChild(node.childNodes[ttt]);
      ttt--;
    }
    else {
      if (node.childNodes[ttt].childNodes.length) {
        remove_whitespace(node.childNodes[ttt]);
      }
    }
  }
  return;
}

function remove_empty_columns() {
	jQuery('.wdform_section').each(function() {
		if(jQuery(this).find('.wdform_column').last().prev().html()=='') {
			if(jQuery(this).children().length>2) {
				jQuery(this).find('.wdform_column').last().prev().remove();
				remove_empty_columns();
			}
		}	
	});
}


function sortable_columns() {
  jQuery( ".wdform_column" ).sortable({
		connectWith: ".wdform_column",
		cursor: 'move',
		placeholder: "highlight",
		start: function(e,ui){
			jQuery('.wdform_column').each(function() {
				if(jQuery(this).html()) {
					jQuery(this).append(jQuery('<div class="wdform_empty_row" style="height:80px;"></div>'));
					jQuery( ".wdform_column" ).sortable( "refresh" );
				}
			});			
		},
		update: function(event, ui) {
			jQuery('.wdform_section .wdform_column:last-child').each(function() {
				if(jQuery(this).html()) {
					jQuery(this).parent().append(jQuery('<div></div>').addClass("wdform_column"));	
					sortable_columns();
				}		
			});
		},
		stop: function(event, ui) {
			jQuery('.wdform_empty_row').remove();	
			remove_empty_columns();	
		}
  });
}

function all_sortable_events()
{
	jQuery(document).on( "click", ".wdform_row, .wdform_tr_section_break", function() {
		var this2=this; 
		setTimeout( function(){		
			if(jQuery("#wdform_arrows"+jQuery(this2).attr("wdid")).attr("class")=="wdform_arrows_show") {
				jQuery("#wdform_field"+jQuery(this2).attr("wdid")).css({"background-color":"#fff", "border":"none", "margin-top":""});
				jQuery("#wdform_arrows"+jQuery(this2).attr("wdid")).removeClass("wdform_arrows_show");
				jQuery("#wdform_arrows"+jQuery(this2).attr("wdid")).addClass("wdform_arrows");
				jQuery("#wdform_arrows"+jQuery(this2).attr("wdid")).hide();
			}
		else {
			jQuery(".wdform_arrows_show").addClass("wdform_arrows");
			jQuery(".wdform_arrows").hide();
			jQuery(".wdform_arrows_show").removeClass("wdform_arrows_show");
			jQuery(".wdform_field, .wdform_field_section_break").css("background-color","#fff");
			jQuery(".wdform_field").css("margin-top","");
			
			if(jQuery("#wdform_field"+jQuery(this2).attr("wdid")).attr("type")=='type_editor')
				jQuery("#wdform_field"+jQuery(this2).attr("wdid")).css("margin-top","-5px");
			
			jQuery("#wdform_field"+jQuery(this2).attr("wdid")).css({"background-color":"#fff"});
			jQuery("#wdform_field"+jQuery(this2).attr("wdid")).css({"border":"none"});
			jQuery("#wdform_arrows"+jQuery(this2).attr("wdid")).removeClass("wdform_arrows");
			jQuery("#wdform_arrows"+jQuery(this2).attr("wdid")).addClass("wdform_arrows_show");
			jQuery("#wdform_arrows"+jQuery(this2).attr("wdid")).show();
		}

	},300)});

	jQuery(document).on( "hover", ".wdform_tr_section_break", function() {
		jQuery("#wdform_field"+jQuery(this).attr("wdid")).css({"background-color":"#F5F5F5"});
	});

	jQuery(document).on( "hover", ".wdform_row", function() {
		jQuery("#wdform_field"+jQuery(this).attr("wdid")).css({"cursor":"move","background-color":"#F5F5F5"});
	});

	jQuery(document).on( "mouseleave", ".wdform_row, .wdform_tr_section_break", function() {
		jQuery("#wdform_field"+jQuery(this).attr("wdid")).css({"background-color":"#fff", "border":"none"});
		if(jQuery("#wdform_arrows"+jQuery(this).attr("wdid")).attr("class")!="wdform_arrows_show") {
			jQuery("#wdform_arrows"+jQuery(this).attr("wdid")).addClass("wdform_arrows");
		}
	});
}

jQuery(document).on( "dblclick", ".wdform_row, .wdform_tr_section_break", function() {
		edit(jQuery(this).attr("wdid"));
});
	
	
function fm_change_radio(elem) {	
	if(jQuery( elem ).hasClass( "fm-yes" )) {
		jQuery( elem ).val('0');
		jQuery( elem ).next().val('0');
		jQuery( elem ).removeClass('fm-yes').addClass('fm-no');
		jQuery(elem).find("span").animate({
			right: parseInt(jQuery( elem ).css( "width")) - 14 + 'px'
		}, 400, function() {
		}); 
	}	
	else {
		jQuery( elem ).val('1');
		jQuery( elem ).next().val('1');
		jQuery(elem).find("span").animate({
			right: 0
		}, 400, function() {
			jQuery( elem ).removeClass('fm-no').addClass('fm-yes');
		}); 
	}	
	if(jQuery( elem ).next().attr('name') == 'mail_verify') {
		show_verify_options(jQuery( elem ).val() == 1 ? true : false);
	}	
}
		
function enable_drag(elem) {
	if(jQuery('#enable_sortable').val() != 1) {
		jQuery('.wdform_column').sortable( "enable" );
		jQuery( ".wdform_arrows" ).slideUp(700);
		all_sortable_events();
	}
	else {
		jQuery('.wdform_column').sortable( "disable" );	
		jQuery(".wdform_column").css("border","none");		
		jQuery( ".wdform_row, .wdform_tr_section_break" ).die("click");
		jQuery( ".wdform_row" ).die("hover");
		jQuery( ".wdform_tr_section_break" ).die("hover");
		jQuery( ".wdform_field" ).css("cursor","default");
		jQuery( ".wdform_field, .wdform_field_section_break" ).css("background-color","#fff");
		jQuery( ".wdform_field, .wdform_field_section_break" ).css("border","none");
		jQuery( ".wdform_arrows_show" ).hide();
		jQuery( ".wdform_arrows_show" ).addClass("wdform_arrows");
		jQuery( ".wdform_arrows_show" ).removeClass("wdform_arrows_show");
		jQuery( ".wdform_arrows" ).slideDown(600);	
	}
	
	fm_change_radio(elem);
}

function refresh_() {
	document.getElementById('counter').value = gen;
	for (i = 1; i <= form_view_max; i++) {
		if (document.getElementById('form_id_tempform_view' + i)) {
			if (document.getElementById('page_next_' + i)) {
				document.getElementById('page_next_' + i).removeAttribute('src');
      }
			if (document.getElementById('page_previous_' + i)) {
				document.getElementById('page_previous_' + i).removeAttribute('src');
      }
			document.getElementById('form_id_tempform_view' + i).parentNode.removeChild(document.getElementById('form_id_tempform_view_img' + i));
			document.getElementById('form_id_tempform_view' + i).removeAttribute('style');
		}
  }
	document.getElementById('form_front').value = document.getElementById('take').innerHTML;
}

function refresh_old() {
  document.getElementById('form').value = document.getElementById('take').innerHTML;
  document.getElementById('counter').value = gen;
  n = gen;
  for (i = 0; i < n; i++) {
    if (document.getElementById(i)) {
      for (z = 0; z < document.getElementById(i).childNodes.length; z++) {
        if (document.getElementById(i).childNodes[z].nodeType == 3) {
          document.getElementById(i).removeChild(document.getElementById(i).childNodes[z]);
        }
      }
      if (document.getElementById(i).getAttribute('type') == "type_captcha" || document.getElementById(i).getAttribute('type') == "type_recaptcha") {
        if (document.getElementById(i).childNodes[10]) {
          document.getElementById(i).removeChild(document.getElementById(i).childNodes[2]);
          document.getElementById(i).removeChild(document.getElementById(i).childNodes[2]);
          document.getElementById(i).removeChild(document.getElementById(i).childNodes[2]);
          document.getElementById(i).removeChild(document.getElementById(i).childNodes[2]);
          document.getElementById(i).removeChild(document.getElementById(i).childNodes[2]);
          document.getElementById(i).removeChild(document.getElementById(i).childNodes[2]);
          document.getElementById(i).removeChild(document.getElementById(i).childNodes[2]);
          document.getElementById(i).removeChild(document.getElementById(i).childNodes[2]);
          document.getElementById(i).removeChild(document.getElementById(i).childNodes[2]);
        }
        else {
          document.getElementById(i).removeChild(document.getElementById(i).childNodes[1]);
          document.getElementById(i).removeChild(document.getElementById(i).childNodes[1]);
          document.getElementById(i).removeChild(document.getElementById(i).childNodes[1]);
          document.getElementById(i).removeChild(document.getElementById(i).childNodes[1]);
          document.getElementById(i).removeChild(document.getElementById(i).childNodes[1]);
          document.getElementById(i).removeChild(document.getElementById(i).childNodes[1]);
          document.getElementById(i).removeChild(document.getElementById(i).childNodes[1]);
          document.getElementById(i).removeChild(document.getElementById(i).childNodes[1]);
          document.getElementById(i).removeChild(document.getElementById(i).childNodes[1]);
        }
        continue;
      }

      if (document.getElementById(i).getAttribute('type') == "type_section_break") {
        document.getElementById(i).removeChild(document.getElementById(i).childNodes[1]);
        document.getElementById(i).removeChild(document.getElementById(i).childNodes[1]);
        document.getElementById(i).removeChild(document.getElementById(i).childNodes[1]);
        continue;
      }

      if (document.getElementById(i).childNodes[10]) {
        document.getElementById(i).removeChild(document.getElementById(i).childNodes[2]);
        document.getElementById(i).removeChild(document.getElementById(i).childNodes[2]);
        document.getElementById(i).removeChild(document.getElementById(i).childNodes[2]);
        document.getElementById(i).removeChild(document.getElementById(i).childNodes[2]);
        document.getElementById(i).removeChild(document.getElementById(i).childNodes[2]);
        document.getElementById(i).removeChild(document.getElementById(i).childNodes[2]);
        document.getElementById(i).removeChild(document.getElementById(i).childNodes[2]);
        document.getElementById(i).removeChild(document.getElementById(i).childNodes[2]);
        document.getElementById(i).removeChild(document.getElementById(i).childNodes[2]);
      }
      else {
        document.getElementById(i).removeChild(document.getElementById(i).childNodes[1]);
        document.getElementById(i).removeChild(document.getElementById(i).childNodes[1]);
        document.getElementById(i).removeChild(document.getElementById(i).childNodes[1]);
        document.getElementById(i).removeChild(document.getElementById(i).childNodes[1]);
        document.getElementById(i).removeChild(document.getElementById(i).childNodes[1]);
        document.getElementById(i).removeChild(document.getElementById(i).childNodes[1]);
        document.getElementById(i).removeChild(document.getElementById(i).childNodes[1]);
        document.getElementById(i).removeChild(document.getElementById(i).childNodes[1]);
        document.getElementById(i).removeChild(document.getElementById(i).childNodes[1]);
      }
    }
  }

  for (i = 0; i <= n; i++) {
    if (document.getElementById(i)) {
      type = document.getElementById(i).getAttribute("type");
      switch (type) {
        case "type_text":
        case "type_number":
        case "type_password":
        case "type_submitter_mail":
        case "type_own_select":
        case "type_country":
        case "type_hidden":
        case "type_map":
        case "type_mark_map":
        case "type_paypal_select": {
          remove_add_(i + "_elementform_id_temp");
          break;
        }
        case "type_submit_reset": {
          remove_add_(i + "_element_submitform_id_temp");
          if (document.getElementById(i + "_element_resetform_id_temp"))
            remove_add_(i + "_element_resetform_id_temp");
          break;
        }
        case "type_captcha": {
          remove_add_("_wd_captchaform_id_temp");
          remove_add_("_element_refreshform_id_temp");
          remove_add_("_wd_captcha_inputform_id_temp");
          break;
        }
        case "type_recaptcha": {
          document.getElementById("public_key").value = document.getElementById("wd_recaptchaform_id_temp").getAttribute("public_key");
          document.getElementById("private_key").value = document.getElementById("wd_recaptchaform_id_temp").getAttribute("private_key");
          document.getElementById("recaptcha_theme").value = document.getElementById("wd_recaptchaform_id_temp").getAttribute("theme");
          document.getElementById('wd_recaptchaform_id_temp').innerHTML = '';
          remove_add_("wd_recaptchaform_id_temp");
          break;
        }
        case "type_file_upload": {
          remove_add_(i + "_elementform_id_temp");
          break;
        }
        case "type_textarea": {
          remove_add_(i + "_elementform_id_temp");
          break;
        }
        case "type_name": {
          if (document.getElementById(i + "_element_titleform_id_temp")) {
            remove_add_(i + "_element_titleform_id_temp");
            remove_add_(i + "_element_firstform_id_temp");
            remove_add_(i + "_element_lastform_id_temp");
            remove_add_(i + "_element_middleform_id_temp");
          }
          else {
            remove_add_(i + "_element_firstform_id_temp");
            remove_add_(i + "_element_lastform_id_temp");
          }
          break;
        }
        case "type_phone": {
          remove_add_(i + "_element_firstform_id_temp");
          remove_add_(i + "_element_lastform_id_temp");
          break;
        }
        case "type_paypal_price": {
          remove_add_(i + "_element_dollarsform_id_temp");
          remove_add_(i + "_element_centsform_id_temp");
          break;
        }
        case "type_address": {
          if (document.getElementById(id_for_country+"_disable_fieldsform_id_temp")) {
            if (document.getElementById(id_for_country+"_disable_fieldsform_id_temp").getAttribute('street1') == 'no') {
              remove_add_(i+"_street1form_id_temp");
            }
            if (document.getElementById(id_for_country+"_disable_fieldsform_id_temp").getAttribute('street2') == 'no')	{
              remove_add_(i+"_street2form_id_temp");
            }
            if (document.getElementById(id_for_country+"_disable_fieldsform_id_temp").getAttribute('city') == 'no') {
              remove_add_(i+"_cityform_id_temp");
            }
            if (document.getElementById(id_for_country+"_disable_fieldsform_id_temp").getAttribute('state') == 'no') {
              remove_add_(i+"_stateform_id_temp");
            }
            if (document.getElementById(id_for_country+"_disable_fieldsform_id_temp").getAttribute('postal') == 'no') {
              remove_add_(i+"_postalform_id_temp");
            }
            if (document.getElementById(id_for_country+"_disable_fieldsform_id_temp").getAttribute('country') == 'no') {
              remove_add_(i+"_countryform_id_temp");
            }
          }
          break;
        }
        case "type_checkbox":
        case "type_radio":
        case "type_paypal_checkbox":
        case "type_paypal_radio":
        case "type_paypal_shipping": {
          is = true;
          for (j = 0; j < 100; j++) {
            if (document.getElementById(i + "_elementform_id_temp" + j)) {
              remove_add_(i + "_elementform_id_temp" + j);
            }
          }
          break;
        }
        case "type_star_rating": {
          remove_add_(i+"_elementform_id_temp");
          break;
        }
        case "type_scale_rating": {
          remove_add_(i+"_elementform_id_temp");
          break;
        }
        case "type_spinner": {
          remove_add_(i+"_elementform_id_temp");
          break;
        }
        case "type_slider": {
          remove_add_(i+"_elementform_id_temp");
          break;
        }
        case "type_range": {
          remove_add_(i+"_elementform_id_temp0");
          remove_add_(i+"_elementform_id_temp1");
          break;
        }
        case "type_grading": {
          for (k = 0; k < 100; k++) {
            if (document.getElementById(i+"_elementform_id_temp"+k)) {
              remove_add_(i+"_elementform_id_temp"+k);
            }
          }
          break;
        }
        case "type_matrix": {
          remove_add_(i+"_elementform_id_temp");
          break;
        }
        case "type_button": {
          for (j = 0; j < 100; j++) {
            if (document.getElementById(i + "_elementform_id_temp" + j)) {
              remove_add_(i + "_elementform_id_temp" + j);
            }
          }
          break;
        }
        case "type_time": {
          if (document.getElementById(i + "_ssform_id_temp")) {
            remove_add_(i + "_ssform_id_temp");
            remove_add_(i + "_mmform_id_temp");
            remove_add_(i + "_hhform_id_temp");
          }
          else {
            remove_add_(i + "_mmform_id_temp");
            remove_add_(i + "_hhform_id_temp");
          }
          break;
        }
        case "type_date": {
          remove_add_(i + "_elementform_id_temp");
          remove_add_(i + "_buttonform_id_temp");
          break;
        }
        case "type_date_fields": {
          remove_add_(i + "_dayform_id_temp");
          remove_add_(i + "_monthform_id_temp");
          remove_add_(i + "_yearform_id_temp");
          break;
        }
      }
    }
  }

  for (i = 1; i <= form_view_max; i++) {
    if (document.getElementById('form_id_tempform_view' + i)) {
      if (document.getElementById('page_next_' + i)) {
        document.getElementById('page_next_' + i).removeAttribute('src');
      }
      if (document.getElementById('page_previous_' + i)) {
        document.getElementById('page_previous_' + i).removeAttribute('src');
      }
      document.getElementById('form_id_tempform_view' + i).parentNode.removeChild(document.getElementById('form_id_tempform_view_img' + i));
      document.getElementById('form_id_tempform_view' + i).removeAttribute('style');
    }
  }

  for (t = 1; t <= form_view_max; t++) {
    if (document.getElementById('form_id_tempform_view' + t)) {
      form_view_element = document.getElementById('form_id_tempform_view' + t);
      remove_whitespace(form_view_element);
      n = form_view_element.childNodes.length - 2;
      for (q = 0; q <= n; q++) {
        if (form_view_element.childNodes[q]) {
          if (form_view_element.childNodes[q].nodeType != 3) {
            if (!form_view_element.childNodes[q].id) {
              del = true;
              GLOBAL_tr = form_view_element.childNodes[q];
              for (x = 0; x < GLOBAL_tr.firstChild.childNodes.length; x++) {
                table = GLOBAL_tr.firstChild.childNodes[x];
                tbody = table.firstChild;
                if (tbody.childNodes.length) {
                  del = false;
                }
              }
              if (del) {
                form_view_element.removeChild(form_view_element.childNodes[q]);
              }
            }
          }
        }
      }
    }
  }
  document.getElementById('form_front').value = document.getElementById('take').innerHTML;
}


function cfm_create_input(toAdd_id, value_id, parent_id, cfm_url) {
  var value = jQuery("#" + value_id).val();
  if (value) {
    jQuery("#" + value_id).attr("style", "width: 250px;");
    var mail_div = jQuery("<div>").attr("class", "fm_mail_div").prependTo("#" + parent_id).text(value);
    jQuery("<img>").attr("src", cfm_url + "/images/delete.png").attr("class", "fm_delete_img").attr("onclick", "fm_delete_mail(this, '" + value + "')").attr("title", "Delete Email").appendTo(mail_div);
    jQuery("#" + value_id).val("");
    jQuery("#" + toAdd_id).val(jQuery("#" + toAdd_id).val() + value + ",");
  }
}

function fm_delete_mail(img, value) {
  jQuery(img).parent().remove();
  jQuery("#mail").val(jQuery("#mail").val().replace(value + ',', ''));
}

function form_maker_options_tabs(id) {
  if (fm_check_email('mailToAdd') || fm_check_email('from_mail') || fm_check_email('reply_to') || fm_check_email('mail_from_user') || fm_check_email('reply_to_user') || fm_check_email('mail_from_other') || fm_check_email('reply_to_other') || fm_check_email('paypal_email')) {
    return false;
  }
  jQuery("#fieldset_id").val(id);
  jQuery(".fm_fieldset_active").removeClass("fm_fieldset_active").addClass("fm_fieldset_deactive");
  jQuery("#" + id + "_fieldset").removeClass("fm_fieldset_deactive").addClass("fm_fieldset_active");
  jQuery(".fm_fieldset_tab").removeClass("active");
  jQuery("#" + id).addClass("active");
  return false;
}

function codemirror_for_javascript() {
  var editor = CodeMirror.fromTextArea(document.getElementById("form_javascript"), {
  lineNumbers: true,
  lineWrapping: true,
  mode: "javascript"
  });
  
  CodeMirror.commands["selectAll"](editor);
  editor.autoFormatRange(editor.getCursor(true), editor.getCursor(false));
  editor.scrollTo(0,0);
}

function set_type(type) {
  switch(type) {
    case 'post':
    document.getElementById('post').removeAttribute('style');
    document.getElementById('page').setAttribute('style','display:none');
    document.getElementById('custom_text').setAttribute('style','display:none');
    document.getElementById('url').setAttribute('style','display:none');
    document.getElementById('none').setAttribute('style','display:none');
    break;
    case 'page':
      document.getElementById('page').removeAttribute('style');
      document.getElementById('post').setAttribute('style','display:none');
      document.getElementById('custom_text').setAttribute('style','display:none');
      document.getElementById('url').setAttribute('style','display:none');
      document.getElementById('none').setAttribute('style','display:none');
      break;
    case 'custom_text':
      document.getElementById('page').setAttribute('style','display:none');
      document.getElementById('post').setAttribute('style','display:none');
      document.getElementById('custom_text').removeAttribute('style');
      document.getElementById('url').setAttribute('style','display:none');
      document.getElementById('none').setAttribute('style','display:none');
      break;
    case 'url':
      document.getElementById('page').setAttribute('style','display:none');
      document.getElementById('post').setAttribute('style','display:none');
      document.getElementById('custom_text').setAttribute('style','display:none');
      document.getElementById('url').removeAttribute('style');
      document.getElementById('none').setAttribute('style','display:none');
      break;
    case 'none':
      document.getElementById('page').setAttribute('style','display:none');
      document.getElementById('post').setAttribute('style','display:none');
      document.getElementById('custom_text').setAttribute('style','display:none');
      document.getElementById('url').setAttribute('style','display:none');
      document.getElementById('none').removeAttribute('style');
      break;
  }
}

function insertAtCursor(myField, myValue) {
  if (myField.style.display == "none") {
    tinyMCE.execCommand('mceInsertContent', false, "%" + myValue + "%");
    return;
  }
  if (document.selection) {
    myField.focus();
    sel = document.selection.createRange();
    sel.text = myValue;
  }
  else if (myField.selectionStart || myField.selectionStart == '0') {
    var startPos = myField.selectionStart;
    var endPos = myField.selectionEnd;
    myField.value = myField.value.substring(0, startPos)
      + "%" + myValue + "%"
      + myField.value.substring(endPos, myField.value.length);
  }
  else {
    myField.value += "%" + myValue + "%";
  }
}

function check_isnum(e) {
  var chCode1 = e.which || e.keyCode;
  if (chCode1 > 31 && (chCode1 < 48 || chCode1 > 57)) {
    return false;
  }
  return true;
}

// Check Email.
function fm_check_email(id) {
  if (document.getElementById(id) && jQuery('#' + id).val() != '') {
    var email_array = jQuery('#' + id).val().split(',');
    for (var email_id = 0; email_id < email_array.length; email_id++) {
      var email = email_array[email_id].replace(/^\s+|\s+$/g, '');
      if (email.search(/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/) == -1) {
        alert('This is not a valid email address.');
        jQuery('#' + id).css('border', '1px solid #FF0000');
        jQuery('#' + id).focus();
        jQuery('html, body').animate({
          scrollTop:jQuery('#' + id).offset().top - 200
        }, 500);
        return true;
      }
    }
  }
  return false;
}

function fm_edit_ip(id) {
	var ip = jQuery("#ip" + id).html();
	jQuery("#td_ip_" + id).html('<input id="ip' + id + '" class="input_th' + id + '" type="text" onkeypress="return fm_check_isnum(event)" value="' + ip + '" name="ip' + id + '" />');
	jQuery("#td_edit_" + id).html('<button class="fm-icon add-block-ip-icon" onclick="if (fm_check_required(\'ip' + id + '\', \'IP\')) {return false;} fm_set_input_value(\'task\', \'save\'); fm_set_input_value(\'current_id\', ' + id + '); fm_save_ip(' + id + '); return false;"></button>');
}

function fm_save_ip(id) {
	var ip = jQuery("#ip" + id).val();
	var post_data = {};
	post_data["ip"] = ip;
	post_data["current_id"] = id;
	post_data["task"] = "save";

	jQuery.post(jQuery("#blocked_ips").attr("action"), post_data, function (data) {
			jQuery("#td_ip_" + id).html('<a id="ip' + id + '" class="pointer" title="Edit" onclick="fm_edit_ip(' + id + ')">' + ip + '</a>');
			jQuery("#td_edit_" + id).html('<button class="fm-icon edit-icon" onclick="fm_edit_ip(' + id + ');"></button>');
		}	
	).success(function (data, textStatus, errorThrown) {
		jQuery(".update, .error").hide();
		jQuery("#fm_blocked_ips_message").html("<div class='updated'><strong><p>Items Succesfully Saved.</p></strong></div>");
		jQuery("#fm_blocked_ips_message").show();
	});
}

function wdhide(id) {
	document.getElementById(id).style.display = "none";
}
function wdshow(id) {
	document.getElementById(id).style.display = "block";
}
function delete_field_condition(id) {
	var cond_id = id.split("_");
	document.getElementById("condition"+cond_id[0]).removeChild(document.getElementById("condition_div"+id));
}

function change_choices(value, ids, types, params) {
	value = value.split("_");
	global_index = value[0];
	id = value[1];
	index = value[2];
	ids_array = ids.split("@@**@@");
	types_array = types.split("@@**@@");
	params_array = params.split("@@**@@");

	switch(types_array[id]) {
		case "type_text":
		case "type_password":
		case "type_textarea":
		case "type_name":
		case "type_submitter_mail":
		case "type_number":
		case "type_phone":
		case "type_paypal_price":
		case "type_spinner":
			if(types_array[id]=="type_number" || types_array[id]=="type_phone")
				var keypress_function = "return check_isnum_space(event)";
			else
				if(types_array[id]=="type_paypal_price")
					var keypress_function = "return check_isnum_point(event)";
				else
					var keypress_function = "";
		
			if(document.getElementById("field_value"+global_index+"_"+index).tagName=="SELECT") {
				document.getElementById("condition_div"+global_index+"_"+index).removeChild(document.getElementById("field_value"+global_index+"_"+index));				
				var label_input = document.createElement('input');
					label_input.setAttribute("id", "field_value"+global_index+'_'+index);
					label_input.setAttribute("type", "text");
					label_input.setAttribute("value", "");	
					label_input.style.cssText = "vertical-align: top; width:200px;";		
					label_input.setAttribute("onKeyPress", keypress_function);

				document.getElementById("condition_div"+global_index+"_"+index).insertBefore(label_input,document.getElementById("delete_condition"+global_index+"_"+index));
				document.getElementById("condition_div"+global_index+"_"+index).insertBefore(document.createTextNode(' '),document.getElementById("delete_condition"+global_index+"_"+index));
			}
			else {
				document.getElementById("field_value"+global_index+'_'+index).value="";
				document.getElementById("field_value"+global_index+'_'+index).setAttribute("onKeyPress", keypress_function);
			}
		break;
		
		case "type_own_select":
		case "type_radio":
		case "type_checkbox":
			if(types_array[id]=="type_own_select")
				w_size = params_array[id].split('*:*w_size*:*');
			else
				w_size = params_array[id].split('*:*w_flow*:*');
		
			w_choices = w_size[1].split('*:*w_choices*:*');
			w_choices_array = w_choices[0].split('***');

			var choise_select = document.createElement('select');
				choise_select.setAttribute("id", "field_value"+global_index+'_'+index);
				choise_select.style.cssText = "vertical-align: top; width:200px;";
				if(types_array[id]== "type_checkbox") {
					choise_select.setAttribute('multiple', 'multiple');
					choise_select.setAttribute('class', 'multiple_select');
				}

			for(k=0; k<w_choices_array.length; k++) {
				var choise_option = document.createElement('option');
					choise_option.setAttribute("id", "choise_"+global_index+'_'+k);
					choise_option.setAttribute("value", w_choices_array[k]);
					choise_option.innerHTML = w_choices_array[k];	
					if(w_choices_array[k].indexOf('[') === -1 && w_choices_array[k].indexOf(']') === -1 && w_choices_array[k].indexOf(':') === -1) {
            choise_select.appendChild(choise_option);
          }
			}
			
			document.getElementById("condition_div"+global_index+"_"+index).removeChild(document.getElementById("field_value"+global_index+"_"+index));
			document.getElementById("condition_div"+global_index+"_"+index).insertBefore(choise_select,document.getElementById("delete_condition"+global_index+"_"+index));
			document.getElementById("condition_div"+global_index+"_"+index).insertBefore(document.createTextNode(' '),document.getElementById("delete_condition"+global_index+"_"+index));
		
		break;	
		
		case "type_paypal_select":	
		case "type_paypal_radio":
		case "type_paypal_checkbox":
		case "type_paypal_shipping":
			if(types_array[id]=="type_paypal_select")
				w_size = params_array[id].split('*:*w_size*:*');
			else
				w_size = params_array[id].split('*:*w_flow*:*');
		
			w_choices = w_size[1].split('*:*w_choices*:*');
			w_choices_array = w_choices[0].split('***');

			w_choices_price = w_choices[1].split('*:*w_choices_price*:*');
			w_choices_price_array = w_choices_price[0].split('***');
			
			var choise_select = document.createElement('select');
				choise_select.setAttribute("id", "field_value"+global_index+'_'+index);
				choise_select.style.cssText = "vertical-align: top; width:200px;";
				if(types_array[id]== "type_paypal_checkbox") {
					choise_select.setAttribute('multiple', 'multiple');
					choise_select.setAttribute('class', 'multiple_select');
				}

			for(k=0; k<w_choices_array.length; k++) {
				var choise_option = document.createElement('option');
					choise_option.setAttribute("id", "choise_"+global_index+'_'+k);
					choise_option.setAttribute("value", w_choices_array[k]+'*:*value*:*'+w_choices_price_array[k]);
					choise_option.innerHTML = w_choices_array[k];	
					if(w_choices_array[k].indexOf('[') === -1 && w_choices_array[k].indexOf(']') === -1 && w_choices_array[k].indexOf(':') === -1) {
						choise_select.appendChild(choise_option);
					}
			}
			
			document.getElementById("condition_div"+global_index+"_"+index).removeChild(document.getElementById("field_value"+global_index+"_"+index));
			document.getElementById("condition_div"+global_index+"_"+index).insertBefore(choise_select,document.getElementById("delete_condition"+global_index+"_"+index));
			document.getElementById("condition_div"+global_index+"_"+index).insertBefore(document.createTextNode(' '),document.getElementById("delete_condition"+global_index+"_"+index));
		break;	
		case "type_address":

			coutries=["Afghanistan","Albania",	"Algeria","Andorra","Angola","Antigua and Barbuda","Argentina","Armenia","Australia","Austria","Azerbaijan","Bahamas","Bahrain","Bangladesh","Barbados","Belarus","Belgium","Belize","Benin","Bhutan","Bolivia","Bosnia and Herzegovina","Botswana","Brazil","Brunei","Bulgaria","Burkina Faso","Burundi","Cambodia","Cameroon","Canada","Cape Verde","Central African Republic","Chad","Chile","China","Colombi","Comoros","Congo (Brazzaville)","Congo","Costa Rica","Cote d'Ivoire","Croatia","Cuba","Cyprus","Czech Republic","Denmark","Djibouti","Dominica","Dominican Republic","East Timor (Timor Timur)","Ecuador","Egypt","El Salvador","Equatorial Guinea","Eritrea","Estonia","Ethiopia","Fiji","Finland","France","Gabon","Gambia, The","Georgia","Germany","Ghana","Greece","Grenada","Guatemala","Guinea","Guinea-Bissau","Guyana","Haiti","Honduras","Hungary","Iceland","India","Indonesia","Iran","Iraq","Ireland","Israel","Italy","Jamaica","Japan","Jordan","Kazakhstan","Kenya","Kiribati","Korea, North","Korea, South","Kuwait","Kyrgyzstan","Laos","Latvia","Lebanon","Lesotho","Liberia","Libya","Liechtenstein","Lithuania","Luxembourg","Macedonia","Madagascar","Malawi","Malaysia","Maldives","Mali","Malta","Marshall Islands","Mauritania","Mauritius","Mexico","Micronesia","Moldova","Monaco","Mongolia","Morocco","Mozambique","Myanmar","Namibia","Nauru","Nepa","Netherlands","New Zealand","Nicaragua","Niger","Nigeria","Norway","Oman","Pakistan","Palau","Panama","Papua New Guinea","Paraguay","Peru","Philippines","Poland","Portugal","Qatar","Romania","Russia","Rwanda","Saint Kitts and Nevis","Saint Lucia","Saint Vincent","Samoa","San Marino","Sao Tome and Principe","Saudi Arabia","Senegal","Serbia and Montenegro","Seychelles","Sierra Leone","Singapore","Slovakia","Slovenia","Solomon Islands","Somalia","South Africa","Spain","Sri Lanka","Sudan","Suriname","Swaziland","Sweden","Switzerland","Syria","Taiwan","Tajikistan","Tanzania","Thailand","Togo","Tonga","Trinidad and Tobago","Tunisia","Turkey","Turkmenistan","Tuvalu","Uganda","Ukraine","United Arab Emirates","United Kingdom","United States","Uruguay","Uzbekistan","Vanuatu","Vatican City","Venezuela","Vietnam","Yemen","Zambia","Zimbabwe"];	
		
		var choise_select = document.createElement('select');
			choise_select.setAttribute("id", "field_value"+global_index+'_'+m);
			choise_select.style.cssText = "vertical-align: top; width:200px;";
				
			for(k=0; k<coutries.length; k++) {
				var choise_option = document.createElement('option');
					choise_select.setAttribute("id", "field_value"+global_index+'_'+index);
					choise_select.style.cssText = "vertical-align: top; width:200px;";
					choise_option.setAttribute("value", coutries[k]);
					choise_option.innerHTML = coutries[k];	
					
				choise_select.appendChild(choise_option);	
			}
			
			document.getElementById("condition_div"+global_index+"_"+index).removeChild(document.getElementById("field_value"+global_index+"_"+index));
			document.getElementById("condition_div"+global_index+"_"+index).insertBefore(choise_select,document.getElementById("delete_condition"+global_index+"_"+index));
			document.getElementById("condition_div"+global_index+"_"+index).insertBefore(document.createTextNode(' '),document.getElementById("delete_condition"+global_index+"_"+index));
			
		break;
	}
}

function add_condition_fields(num, ids1, labels1, types1, params1) {
	ids = ids1.split("@@**@@");
	labels = labels1.split("@@**@@");
	types = types1.split("@@**@@");
	params = params1.split("@@**@@");
	
	for(i=500; i>=0; i--) {
		if(document.getElementById('condition_div'+num+'_'+i))
			break;
	}
	m=i+1;
	
	var condition_div = document.createElement('div');
		condition_div.setAttribute("id", "condition_div"+num+'_'+m);
	
	var labels_select = document.createElement('select');
		labels_select.setAttribute("id", "field_labels"+num+'_'+m);
		labels_select.setAttribute("onchange", "change_choices(options[selectedIndex].id+'_"+m+"','"+ids1+"','"+types1+"','"+params1.replace(/\'/g,"\\'")+"')");
		labels_select.style.cssText="width:300px; vertical-align:top;";

	for(k=0; k<labels.length; k++) {
		if(ids[k]!=document.getElementById('fields'+num).value) {
			var labels_option = document.createElement('option');
				labels_option.setAttribute("id", num+"_"+k);
				labels_option.setAttribute("value", ids[k]);
				labels_option.innerHTML = labels[k];	
				
			labels_select.appendChild(labels_option);	
		}
	}
	
	condition_div.appendChild(labels_select);	
	condition_div.appendChild(document.createTextNode(' '));
	
	var is_select = document.createElement('select');
		is_select.setAttribute("id", "is_select"+num+'_'+m);
		is_select.style.cssText = "width:94px;";

	var	is_option = document.createElement('option');
		is_option.setAttribute("id", "is");
		is_option.setAttribute("value", "==");
		is_option.innerHTML = "is";

	var	is_notoption = document.createElement('option');
		is_notoption.setAttribute("id", "is_not");
		is_notoption.setAttribute("value", "!=");
		is_notoption.innerHTML = "is not";
	
	
	
	var	is_likoption = document.createElement('option');
		is_likoption.setAttribute("id", "like");
		is_likoption.setAttribute("value", "%");
		is_likoption.innerHTML = "like";
		
	var	is_notlikoption = document.createElement('option');
		is_notlikoption.setAttribute("id", "not_like");
		is_notlikoption.setAttribute("value", "!%");
		is_notlikoption.innerHTML = "not like";
		
	var	is_emptyoption = document.createElement('option');
		is_emptyoption.setAttribute("id", "empty");
		is_emptyoption.setAttribute("value", "=");
		is_emptyoption.innerHTML = "empty";
		
	var	is_notemptyoption = document.createElement('option');
		is_notemptyoption.setAttribute("id", "not_empty");
		is_notemptyoption.setAttribute("value", "!");
		is_notemptyoption.innerHTML = "not empty";
		
		
		is_select.appendChild(is_option);	
		is_select.appendChild(is_notoption);
        is_select.appendChild(is_likoption);
        is_select.appendChild(is_notlikoption);
        is_select.appendChild(is_emptyoption);
        is_select.appendChild(is_notemptyoption);		

		condition_div.appendChild(is_select);
		condition_div.appendChild(document.createTextNode(' '));
		
	if(ids[0]!=document.getElementById('fields'+num).value)
		var index_of_field = 0;
	else
		var index_of_field = 1;
	
	switch(types[index_of_field]) {
		case "type_text":
		case "type_password":
		case "type_textarea":
		case "type_name":
		case "type_submitter_mail":
		case "type_phone":
		case "type_number":
		case "type_paypal_price":
		case "type_spinner":
		if(types[index_of_field]=="type_number" || types[index_of_field]=="type_phone")
				var keypress_function = "return check_isnum_space(event)";
			else
				if(types[index_of_field]=="type_paypal_price")
					var keypress_function = "return check_isnum_point(event)";
				else
					var keypress_function = "";
		
		var label_input = document.createElement('input');
			label_input.setAttribute("id", "field_value"+num+'_'+m);
			label_input.setAttribute("type", "text");
			label_input.setAttribute("value", "");	
			label_input.style.cssText = "width:200px;";
			label_input.setAttribute("onKeyPress", keypress_function);
			
		condition_div.appendChild(label_input);
		
		break;
		
		case "type_checkbox":
		case "type_radio":
		case "type_own_select":
			if(types[index_of_field]=="type_own_select")
				w_size = params[index_of_field].split('*:*w_size*:*');
			else
				w_size = params[index_of_field].split('*:*w_flow*:*');
				
			w_choices = w_size[1].split('*:*w_choices*:*');
			w_choices_array = w_choices[0].split('***');
			
			var choise_select = document.createElement('select');
				choise_select.setAttribute("id", "field_value"+num+'_'+m);
				choise_select.style.cssText = "vertical-align: top; width:200px;";
				if(types[index_of_field]== "type_checkbox") {
					choise_select.setAttribute('multiple', 'multiple');
					choise_select.setAttribute('class', 'multiple_select');
				}
					
			for(k=0; k<w_choices_array.length; k++)	 {
				var choise_option = document.createElement('option');
					choise_option.setAttribute("id", "choise_"+num+'_'+k);
					choise_option.setAttribute("value", w_choices_array[k]);
					choise_option.innerHTML = w_choices_array[k];	
					
				if(w_choices_array[k].indexOf('[') === -1 && w_choices_array[k].indexOf(']') === -1 && w_choices_array[k].indexOf(':') === -1) {
					choise_select.appendChild(choise_option);
				}
			}
			condition_div.appendChild(choise_select);	
			
		break;
		
		case "type_paypal_select":
		case "type_paypal_checkbox":
		case "type_paypal_radio":
		case "type_paypal_shipping":
			if(types[index_of_field]=="type_paypal_select")
				w_size = params[index_of_field].split('*:*w_size*:*');
			else
				w_size = params[index_of_field].split('*:*w_flow*:*');
				
			w_choices = w_size[1].split('*:*w_choices*:*');
			w_choices_array = w_choices[0].split('***');
			
			w_choices_price = w_choices[1].split('*:*w_choices_price*:*');
			w_choices_price_array = w_choices_price[0].split('***');
			
			var choise_select = document.createElement('select');
				choise_select.setAttribute("id", "field_value"+num+'_'+m);
				choise_select.style.cssText = "vertical-align: top; width:200px;";
				if(types[index_of_field]== "type_paypal_checkbox") {
					choise_select.setAttribute('multiple', 'multiple');
					choise_select.setAttribute('class', 'multiple_select');
				}
					
			for(k=0; k<w_choices_array.length; k++)	 {
				var choise_option = document.createElement('option');
					choise_option.setAttribute("id", "choise_"+num+'_'+k);
					choise_option.setAttribute("value", w_choices_array[k]+'*:*value*:*'+w_choices_price_array[k]);
					choise_option.innerHTML = w_choices_array[k];	
					
				if(w_choices_array[k].indexOf('[') === -1 && w_choices_array[k].indexOf(']') === -1 && w_choices_array[k].indexOf(':') === -1) {
					choise_select.appendChild(choise_option);
				}
			}
			condition_div.appendChild(choise_select);	
		break;
		
		case "type_address":
			coutries=["Afghanistan","Albania",	"Algeria","Andorra","Angola","Antigua and Barbuda","Argentina","Armenia","Australia","Austria","Azerbaijan","Bahamas","Bahrain","Bangladesh","Barbados","Belarus","Belgium","Belize","Benin","Bhutan","Bolivia","Bosnia and Herzegovina","Botswana","Brazil","Brunei","Bulgaria","Burkina Faso","Burundi","Cambodia","Cameroon","Canada","Cape Verde","Central African Republic","Chad","Chile","China","Colombi","Comoros","Congo (Brazzaville)","Congo","Costa Rica","Cote d'Ivoire","Croatia","Cuba","Cyprus","Czech Republic","Denmark","Djibouti","Dominica","Dominican Republic","East Timor (Timor Timur)","Ecuador","Egypt","El Salvador","Equatorial Guinea","Eritrea","Estonia","Ethiopia","Fiji","Finland","France","Gabon","Gambia, The","Georgia","Germany","Ghana","Greece","Grenada","Guatemala","Guinea","Guinea-Bissau","Guyana","Haiti","Honduras","Hungary","Iceland","India","Indonesia","Iran","Iraq","Ireland","Israel","Italy","Jamaica","Japan","Jordan","Kazakhstan","Kenya","Kiribati","Korea, North","Korea, South","Kuwait","Kyrgyzstan","Laos","Latvia","Lebanon","Lesotho","Liberia","Libya","Liechtenstein","Lithuania","Luxembourg","Macedonia","Madagascar","Malawi","Malaysia","Maldives","Mali","Malta","Marshall Islands","Mauritania","Mauritius","Mexico","Micronesia","Moldova","Monaco","Mongolia","Morocco","Mozambique","Myanmar","Namibia","Nauru","Nepa","Netherlands","New Zealand","Nicaragua","Niger","Nigeria","Norway","Oman","Pakistan","Palau","Panama","Papua New Guinea","Paraguay","Peru","Philippines","Poland","Portugal","Qatar","Romania","Russia","Rwanda","Saint Kitts and Nevis","Saint Lucia","Saint Vincent","Samoa","San Marino","Sao Tome and Principe","Saudi Arabia","Senegal","Serbia and Montenegro","Seychelles","Sierra Leone","Singapore","Slovakia","Slovenia","Solomon Islands","Somalia","South Africa","Spain","Sri Lanka","Sudan","Suriname","Swaziland","Sweden","Switzerland","Syria","Taiwan","Tajikistan","Tanzania","Thailand","Togo","Tonga","Trinidad and Tobago","Tunisia","Turkey","Turkmenistan","Tuvalu","Uganda","Ukraine","United Arab Emirates","United Kingdom","United States","Uruguay","Uzbekistan","Vanuatu","Vatican City","Venezuela","Vietnam","Yemen","Zambia","Zimbabwe"];	
		
		var choise_select = document.createElement('select');
			choise_select.setAttribute("id", "field_value"+num+'_'+m);
			choise_select.style.cssText = "width:200px;";
				
			for(k=0; k<coutries.length; k++) {
				var choise_option = document.createElement('option');
					choise_option.setAttribute("id", "choise_"+num+'_'+k);
					choise_option.setAttribute("value", coutries[k]);
					choise_option.innerHTML = coutries[k];	
					
				choise_select.appendChild(choise_option);	
			}
			condition_div.appendChild(choise_select);	
		break;
	}
	condition_div.appendChild(document.createTextNode(' '));
	
	var	img=document.createElement('img');
		img.setAttribute('src',plugin_url + '/images/delete.png');
		img.setAttribute('id','delete_condition'+num+'_'+m);
		img.setAttribute('onClick','delete_field_condition("'+num+'_'+m+'")');
		img.style.cssText = "vertical-align: middle";

	condition_div.appendChild(img);
	document.getElementById('condition'+num).appendChild(condition_div);
}

function add_condition(ids1, labels1, types1, params1, all_ids, all_labels) {
	for(i=500; i>=0; i--) {
		if(document.getElementById('condition'+i))
			break;
	}
	
	num=i+1;

	ids = all_ids.split("@@**@@");
	labels = all_labels.split("@@**@@");

	var condition_div = document.createElement('div');
		condition_div.setAttribute("id", "condition"+num);
		condition_div.setAttribute("class", "fm-condition");
	
	var conditional_fields_div = document.createElement('div');
		conditional_fields_div.setAttribute("id", "conditional_fileds"+num);
	
	var show_hide_select = document.createElement('select');
		show_hide_select.setAttribute("id", "show_hide"+num);
		show_hide_select.setAttribute("name", "show_hide"+num);
		show_hide_select.style.cssText="width:80px;";

	var show_option = document.createElement('option');
		show_option.setAttribute("value", "1");
		show_option.innerHTML = "show";

	var hide_option = document.createElement('option');
		hide_option.setAttribute("value", "0");
		hide_option.innerHTML = "hide";	
	
	show_hide_select.appendChild(show_option);
	show_hide_select.appendChild(hide_option);
	
	var fields_select = document.createElement('select');
		fields_select.setAttribute("id", "fields"+num);
		fields_select.setAttribute("name", "fields"+num);
		fields_select.style.cssText="width:303px;";
		
	for(k=0; k<labels.length; k++) {
		var fields_option = document.createElement('option');
			fields_option.setAttribute("value", ids[k]);
			fields_option.innerHTML = labels[k];
			
		fields_select.appendChild(fields_option);
	}

	var span = document.createElement('span');
		span.innerHTML = 'if';	
				
	var all_any_select = document.createElement('select');
		all_any_select.setAttribute("id", "all_any"+num);
		all_any_select.setAttribute("name", "all_any"+num);
		all_any_select.style.cssText="width:60px;";

	var all_option = document.createElement('option');
		all_option.setAttribute("value", "and");
		all_option.innerHTML = "all";

	var any_option = document.createElement('option');
		any_option.setAttribute("value", "or");
		any_option.innerHTML = "any";	
		
	all_any_select.appendChild(all_option);
	all_any_select.appendChild(any_option);

	var span1 = document.createElement('span');
		span1.innerHTML = 'of the following match:';	


	var add_img = document.createElement('img');
		add_img.setAttribute('src',plugin_url + '/images/add.png');
		add_img.setAttribute('onClick','add_condition_fields("'+num+'", "'+ids1+'", "'+labels1.replace(/\'/g,"\\'").replace(/\"/g,"&quot;")+'", "'+types1.replace(/\'/g,"\\'").replace(/\"/g,"&quot;")+'", "'+params1.replace(/\'/g,"\\'").replace(/\"/g,"&quot;")+'")');
		add_img.style.cssText = "cursor: pointer; vertical-align: middle;";
	
	var delete_img = document.createElement('img');
		delete_img.setAttribute('src',plugin_url + '/images/page_delete.png');
		delete_img.setAttribute('onClick','delete_condition("'+num+'")');
		delete_img.style.cssText = "cursor: pointer; vertical-align: middle;";
	
	conditional_fields_div.appendChild(show_hide_select);	
	conditional_fields_div.appendChild(document.createTextNode(' '));
	conditional_fields_div.appendChild(fields_select);
	conditional_fields_div.appendChild(document.createTextNode(' '));
	conditional_fields_div.appendChild(span);	
	conditional_fields_div.appendChild(document.createTextNode(' '));
	conditional_fields_div.appendChild(all_any_select);	
	conditional_fields_div.appendChild(document.createTextNode(' '));
	conditional_fields_div.appendChild(span1);	
	conditional_fields_div.appendChild(document.createTextNode(' '));
	conditional_fields_div.appendChild(add_img);	
	conditional_fields_div.appendChild(document.createTextNode(' '));
	conditional_fields_div.appendChild(delete_img);	

	condition_div.appendChild(conditional_fields_div);	
	document.getElementById('conditions_fieldset').appendChild(condition_div);	
}

function delete_condition(num) {
	document.getElementById('conditions_fieldset').removeChild(document.getElementById('condition'+num));	
}

function acces_level(length) {
	var value='';
	for(i=0; i<=parseInt(length); i++) {
    if (document.getElementById('user_'+i).checked) {
      value=value+document.getElementById('user_'+i).value+',';			
    }	
  }
	document.getElementById('user_id_wd').value=value;
}

function check_isnum_space(e) {
	var chCode1 = e.which || e.keyCode;	
	if (chCode1 ==32) {
		return true;
  }
  if (chCode1 > 31 && (chCode1 < 48 || chCode1 > 57)) {
		return false;
  }
	return true;
}

function check_isnum_point(e) {
  var chCode1 = e.which || e.keyCode;	
	if (chCode1 ==46) {
		return true;
	}
	if (chCode1 > 31 && (chCode1 < 48 || chCode1 > 57)) {
    return false;
  }
	return true;
}
