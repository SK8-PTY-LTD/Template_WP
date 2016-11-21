<?php

class FMViewFormMakerPreview_fmc {
  ////////////////////////////////////////////////////////////////////////////////////////
  // Events                                                                             //
  ////////////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////////////
  // Constants                                                                          //
  ////////////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////////////
  // Variables                                                                          //
  ////////////////////////////////////////////////////////////////////////////////////////
  private $model;


  ////////////////////////////////////////////////////////////////////////////////////////
  // Constructor & Destructor                                                           //
  ////////////////////////////////////////////////////////////////////////////////////////
  public function __construct($model) {
    $this->model = $model;
  }
  ////////////////////////////////////////////////////////////////////////////////////////
  // Public Methods                                                                     //
  ////////////////////////////////////////////////////////////////////////////////////////
  public function display() {
    $form_id = ((isset($_GET['form_id'])) ? esc_html(stripslashes($_GET['form_id'])) : 0);
    $form = (($form_id) ? $this->model->get_form($form_id) : '');
    wp_print_scripts('jquery');
    wp_print_scripts('jquery-ui-widget');
    wp_print_scripts('jquery-ui-slider');
    wp_print_scripts('jquery-ui-spinner');
	$fmc_settings = get_option('fmc_settings');
	$map_key = isset($fmc_settings['map_key']) ? $fmc_settings['map_key'] : '';
    ?>
    <script src="https://maps.google.com/maps/api/js?v=3.exp&key=<?php echo $map_key ?>" type="text/javascript"></script>
    <script src="<?php echo WD_FMC_URL . '/js/if_gmap_front_end.js'; ?>" type="text/javascript"></script>
    <script src="<?php echo WD_FMC_URL . '/js/calendar/calendar.js'; ?>" type="text/javascript"></script>
    <script src="<?php echo WD_FMC_URL . '/js/calendar/calendar_function.js'; ?>" type="text/javascript"></script>
    <link media="all" type="text/css" href="<?php echo WD_FMC_URL . '/css/calendar-jos.css'; ?>" rel="stylesheet">
    <link media="all" type="text/css" href="<?php echo WD_FMC_URL . '/css/jquery-ui-1.10.3.custom.css'; ?>" rel="stylesheet">
    <link media="all" type="text/css" href="<?php echo WD_FMC_URL . '/css/jquery-ui-spinner.css'; ?>" rel="stylesheet">
    <?php
    if (isset($_GET['test_theme'])) {
      wp_print_scripts('jquery-effects-shake');
      wp_register_script('main_div_front_end', WD_FMC_URL . '/js/main_div_front_end.js', array(), get_option("wd_form_maker_version"));
      $theme_id = esc_html(stripslashes($_GET['test_theme']));
      require_once (WD_FMC_DIR . '/frontend/controllers/FMControllerForm_maker_fmc.php');
      $controller = new FMControllerForm_maker_fmc();
      echo $controller->execute($form_id, $theme_id);
      die();
    }
    $theme_id = ((isset($_GET['id'])) ? esc_html(stripslashes($_GET['id'])) : '');
    $css = $this->model->get_theme_css($theme_id);
    $id = 'form_id_temp';
    ?>
    <script src="<?php echo WD_FMC_URL . '/js/main_front_end.js'; ?>"></script>
    <style>
      <?php
      echo str_replace('[SITE_ROOT]', WD_FMC_URL, $css);
      ?>
    </style>
    <div id="form_id_temppages" class="wdform_page_navigation" show_title="" show_numbers="" type=""></div>
    <form id="form_preview"><?php echo $form; ?></form>
    <?php
    if ($form) { // Preview from options page.
      die();
    }
    ?>
    <input type="hidden" id="counter<?php echo $id; ?>" value="" name="counter<?php echo $id; ?>" />
    <script>
      var plugin_url = "<?php echo WD_FMC_URL; ?>";
      /*JURI_ROOT = '<?php echo WD_FMC_URL . '/js' ?>';*/
      document.getElementById('form_preview').innerHTML = window.parent.document.getElementById('take').innerHTML;
      document.getElementById('form_id_temppages').setAttribute('show_title', window.parent.document.getElementById('pages').getAttribute('show_title'));
      document.getElementById('form_id_temppages').setAttribute('show_numbers', window.parent.document.getElementById('pages').getAttribute('show_numbers'));
      document.getElementById('form_id_temppages').setAttribute('type', window.parent.document.getElementById('pages').getAttribute('type'));
      document.getElementById('counterform_id_temp').value = window.parent.gen;
      form_view_count<?php echo $id ?>= 0;
      for (i = 1; i <= 30; i++) {
        if (document.getElementById('<?php echo $id ?>form_view' + i)) {
          form_view_count<?php echo $id ?>++;
          form_view_max<?php echo $id ?>= i;
          document.getElementById('<?php echo $id ?>form_view' + i).parentNode.removeAttribute('style');
        }
      }
      refresh_first();
      if (form_view_count<?php echo $id ?>> 1) {
        for (i = 1; i <= form_view_max<?php echo $id ?>; i++) {
          if (document.getElementById('<?php echo $id ?>form_view' + i)) {
            first_form_view<?php echo $id ?>= i;
            break;
          }
        }
        generate_page_nav(first_form_view<?php echo $id ?>, '<?php echo $id ?>', form_view_count<?php echo $id ?>, form_view_max<?php echo $id ?>);
      }
      function remove_add_(id) {
        attr_name = new Array();
        attr_value = new Array();
        var input = document.getElementById(id);
        atr = input.attributes;
        for (v = 0; v < 30; v++)
          if (atr[v]) {
            if (atr[v].name.indexOf("add_") == 0) {
              attr_name.push(atr[v].name.replace('add_', ''));
              attr_value.push(atr[v].value);
              input.removeAttribute(atr[v].name);
              v--;
            }
          }
        for (v = 0; v < attr_name.length; v++) {
          input.setAttribute(attr_name[v], attr_value[v])
        }
      }
      function refresh_first() {
        n = window.parent.gen;
        for (i = 0; i < n; i++) {
          if (document.getElementById(i)) {
            for (z = 0; z < document.getElementById(i).childNodes.length; z++) {
              if (document.getElementById(i).childNodes[z].nodeType == 3) {
                document.getElementById(i).removeChild(document.getElementById(i).childNodes[z]);
              }
            }
            if (document.getElementById(i).getAttribute('type') == "type_map") {
              if_gmap_init(i);
              for (q = 0; q < 20; q++) {
                if (document.getElementById(i + "_elementform_id_temp").getAttribute("long" + q)) {
                  w_long = parseFloat(document.getElementById(i + "_elementform_id_temp").getAttribute("long" + q));
                  w_lat = parseFloat(document.getElementById(i + "_elementform_id_temp").getAttribute("lat" + q));
                  w_info = parseFloat(document.getElementById(i + "_elementform_id_temp").getAttribute("info" + q));
                  add_marker_on_map(i, q, w_long, w_lat, w_info, false);
                }
              }
            }
            if (document.getElementById(i).getAttribute('type') == "type_mark_map") {
              if_gmap_init(i);
              w_long = parseFloat(document.getElementById(i + "_elementform_id_temp").getAttribute("long" + 0));
              w_lat = parseFloat(document.getElementById(i + "_elementform_id_temp").getAttribute("lat" + 0));
              w_info = parseFloat(document.getElementById(i + "_elementform_id_temp").getAttribute("info" + 0));
              add_marker_on_map(i, 0, w_long, w_lat, w_info, true);
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
              case "type_map": {
                remove_add_(i + "_elementform_id_temp");
                break;
              }
              case "type_submit_reset": {
                remove_add_(i + "_element_submitform_id_temp");
                if (document.getElementById(i + "_element_resetform_id_temp")) {
                  remove_add_(i + "_element_resetform_id_temp");
                }
                break;
              }

              case "type_captcha": {
                remove_add_("_wd_captchaform_id_temp");
                remove_add_("_element_refreshform_id_temp");
                remove_add_("_wd_captcha_inputform_id_temp");
                break;
              }
              case "type_recaptcha": {
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
              case "type_address": {
                if(document.getElementById(i+"_disable_fieldsform_id_temp").getAttribute('street1')=='no')
                  remove_add_(i+"_street1form_id_temp");
                if(document.getElementById(i+"_disable_fieldsform_id_temp").getAttribute('street2')=='no')	
                  remove_add_(i+"_street2form_id_temp");
                if(document.getElementById(i+"_disable_fieldsform_id_temp").getAttribute('city')=='no')
                  remove_add_(i+"_cityform_id_temp");
                if(document.getElementById(i+"_disable_fieldsform_id_temp").getAttribute('state')=='no')
                  remove_add_(i+"_stateform_id_temp");
                if(document.getElementById(i+"_disable_fieldsform_id_temp").getAttribute('postal')=='no')
                  remove_add_(i+"_postalform_id_temp");
                if(document.getElementById(i+"_disable_fieldsform_id_temp").getAttribute('country')=='no')
                  remove_add_(i+"_countryform_id_temp");
                break;
              }
              case "type_checkbox":
              case "type_radio": {
                is = true;
                for (j = 0; j < 100; j++) {
                  if (document.getElementById(i + "_elementform_id_temp" + j)) {
                    remove_add_(i + "_elementform_id_temp" + j);
                  }
                }
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
                var spinner_value = document.getElementById(i+"_elementform_id_temp").getAttribute( "aria-valuenow" );
                var spinner_min_value = document.getElementById(i+"_min_valueform_id_temp").value;
                var spinner_max_value = document.getElementById(i+"_max_valueform_id_temp").value;
                var spinner_step = document.getElementById(i+"_stepform_id_temp").value;
                jQuery( "#"+i+"_elementform_id_temp" ).removeClass( "ui-spinner-input" )
                  .prop( "disabled", false )
                  .removeAttr( "autocomplete" )
                  .removeAttr( "role" )
                  .removeAttr( "aria-valuemin" )
                  .removeAttr( "aria-valuemax" )
                  .removeAttr( "aria-valuenow" );
                span_ui= document.getElementById(i+"_elementform_id_temp").parentNode;
                span_ui.parentNode.appendChild(document.getElementById(i+"_elementform_id_temp"));
                span_ui.parentNode.removeChild(span_ui);
                jQuery("#"+i+"_elementform_id_temp")[0].spin = null;
                spinner = jQuery( "#"+i+"_elementform_id_temp" ).spinner();
                spinner.spinner( "value", spinner_value );
                jQuery( "#"+i+"_elementform_id_temp" ).spinner({ min: spinner_min_value});    
                jQuery( "#"+i+"_elementform_id_temp" ).spinner({ max: spinner_max_value});
                jQuery( "#"+i+"_elementform_id_temp" ).spinner({ step: spinner_step});
                break;
              }
              case "type_slider": {	
                remove_add_(i+"_elementform_id_temp");	
                var slider_value = document.getElementById(i+"_slider_valueform_id_temp").value;
                var slider_min_value = document.getElementById(i+"_slider_min_valueform_id_temp").value;
                var slider_max_value = document.getElementById(i+"_slider_max_valueform_id_temp").value;
                var slider_element_value = document.getElementById( i+"_element_valueform_id_temp" );
                var slider_value_save = document.getElementById( i+"_slider_valueform_id_temp" );
                document.getElementById(i+"_elementform_id_temp").innerHTML = "";
                document.getElementById(i+"_elementform_id_temp").removeAttribute( "class" );
                document.getElementById(i+"_elementform_id_temp").removeAttribute( "aria-disabled" );
                jQuery("#"+i+"_elementform_id_temp")[0].slide = null;	
                jQuery( "#"+i+"_elementform_id_temp").slider({
                  range: "min",
                  value: eval(slider_value),
                  min: eval(slider_min_value),
                  max: eval(slider_max_value),
                  slide: function( event, ui ) {	
                    slider_element_value.innerHTML = "" + ui.value ;
                    slider_value_save.value = "" + ui.value; 

                  }
                });
                break;
              }
              case "type_range": {	
                remove_add_(i+"_elementform_id_temp0");
                remove_add_(i+"_elementform_id_temp1");
                var spinner_value0 = document.getElementById(i+"_elementform_id_temp0").getAttribute( "aria-valuenow" );
                var spinner_step = document.getElementById(i+"_range_stepform_id_temp").value;
                jQuery( "#"+i+"_elementform_id_temp0" ).removeClass( "ui-spinner-input" )
                  .prop( "disabled", false )
                  .removeAttr( "autocomplete" )
                  .removeAttr( "role" )
                  .removeAttr( "aria-valuenow" );
                span_ui= document.getElementById(i+"_elementform_id_temp0").parentNode;
                span_ui.parentNode.appendChild(document.getElementById(i+"_elementform_id_temp0"));
                span_ui.parentNode.removeChild(span_ui);
                jQuery("#"+i+"_elementform_id_temp0")[0].spin = null;
                jQuery("#"+i+"_elementform_id_temp1")[0].spin = null;
                spinner0 = jQuery( "#"+i+"_elementform_id_temp0" ).spinner();
                spinner0.spinner( "value", spinner_value0 );
                jQuery( "#"+i+"_elementform_id_temp0" ).spinner({ step: spinner_step});
                var spinner_value1 = document.getElementById(i+"_elementform_id_temp1").getAttribute( "aria-valuenow" );
                jQuery( "#"+i+"_elementform_id_temp1" ).removeClass( "ui-spinner-input" )
                  .prop( "disabled", false )
                  .removeAttr( "autocomplete" )
                  .removeAttr( "role" )
                  .removeAttr( "aria-valuenow" );
                span_ui1= document.getElementById(i+"_elementform_id_temp1").parentNode;
                span_ui1.parentNode.appendChild(document.getElementById(i+"_elementform_id_temp1"));
                span_ui1.parentNode.removeChild(span_ui1);
                spinner1 = jQuery( "#"+i+"_elementform_id_temp1" ).spinner();
                spinner1.spinner( "value", spinner_value1 );
                jQuery( "#"+i+"_elementform_id_temp1" ).spinner({ step: spinner_step});
                break;
              }
              case "type_grading": {
                for (k=0; k<100; k++) {
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
            }
          }
        }
        for (t = 1; t <= form_view_max<?php echo $id ?>; t++) {
          if (document.getElementById('form_id_tempform_view' + t)) {
            form_view_element = document.getElementById('form_id_tempform_view' + t);
            remove_whitespace(form_view_element);
            xy = form_view_element.childNodes.length - 2;
            for (z = 0; z <= xy; z++) {
              if (form_view_element.childNodes[z]) {
                if (form_view_element.childNodes[z].nodeType != 3) {
                  if (!form_view_element.childNodes[z].id) {
                    del = true;
                    GLOBAL_tr = form_view_element.childNodes[z];
                    //////////////////////////////////////////////////////////////////////////////////////////
                    for (x = 0; x < GLOBAL_tr.firstChild.childNodes.length; x++) {
                      table = GLOBAL_tr.firstChild.childNodes[x];
                      tbody = table.firstChild;
                      if (tbody.childNodes.length) {
                        del = false;
                      }
                    }
                    if (del) {
                      form_view_element.removeChild(form_view_element.childNodes[z]);
                    }
                  }
                }
              }
            }
          }
        }
        for (i = 1; i <= window.parent.form_view_max; i++) {
          if (document.getElementById('form_id_tempform_view' + i)) {
            document.getElementById('form_id_tempform_view' + i).parentNode.removeChild(document.getElementById('form_id_tempform_view_img' + i));
            document.getElementById('form_id_tempform_view' + i).removeAttribute('style');
          }
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
    </script>
    <?php
    die();
  }
  
  ////////////////////////////////////////////////////////////////////////////////////////
  // Getters & Setters                                                                  //
  ////////////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////////////
  // Private Methods                                                                    //
  ////////////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////////////
  // Listeners                                                                          //
  ////////////////////////////////////////////////////////////////////////////////////////
}