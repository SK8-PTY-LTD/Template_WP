<?php

class FMViewFormcontactwindow {
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
    $rows = $this->model->get_form_data();
    ?>
    <html xmlns="http://www.w3.org/1999/xhtml">
      <head>
        <title>Contact Form Maker</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <script language="javascript" type="text/javascript" src="<?php echo site_url(); ?>/wp-includes/js/tinymce/tiny_mce_popup.js"></script>
        <script language="javascript" type="text/javascript" src="<?php echo site_url(); ?>/wp-includes/js/tinymce/utils/mctabs.js"></script>
        <script language="javascript" type="text/javascript" src="<?php echo site_url(); ?>/wp-includes/js/tinymce/utils/form_utils.js"></script>

        <?php
        wp_print_scripts('jquery');
        ?>
        <base target="_self">
      </head>
      <body id="link" onLoad="tinyMCEPopup.executeOnLoad('init();');document.body.style.display='';" dir="ltr" class="forceColors">
        <div class="tabs" role="tablist" tabindex="-1">
          <ul>
            <li id="display_tab" class="current" role="tab" tabindex="0">
              <span>
                <a href="javascript:mcTabs.displayTab('display_tab','display_panel');" onMouseDown="return false;" tabindex="-1">Contact Form Maker</a>
              </span>
            </li>
            <li id="submissions_tab" class="" role="tab" tabindex="0">
              <span>
                <a href="javascript:mcTabs.displayTab('submissions_tab','submissions_panel');" onMouseDown="return false;" tabindex="-1">Submissions</a>
              </span>
            </li>
          </ul>
        </div>
        <style>
          .panel_wrapper {
            height: 220px !important;
          }
          .smaller_font {
            font-size: 12px !important;
            vertical-align: middle; 
            text-align: left;
          }
          .smaller_font ul li {
            display: flex !important;
          }
          .smaller_font ul {
            margin: 0 !important;
          }
        </style>
        <div class="panel_wrapper" style="overflow: hidden;">
          <div id="display_panel" class="panel current">
            <table>
              <tr>
                <td style="vertical-align: middle; text-align: left;">Select a Form</td>
                <td style="vertical-align: middle; text-align: left;">
                  <select name="form_maker_id" id="form_maker_id" style="width: 230px; text-align: left;">
                    <option value="- Select Form -" selected="selected">- Select a Form -</option>
                    <?php
                    foreach ($rows as $row) {
                      ?>
                    <option value="<?php echo $row->id; ?>"><?php echo $row->title; ?></option>
                      <?php
                    }
                    ?>
                  </select>
                </td>
              </tr>
            </table>
          </div>
          <div id="submissions_panel" class="panel">
            <div class="error" style="position: relative; padding: 5px; font-size: 20px; color: red; opacity: 1; font-weight: bolder;">Front end submissions are disabled in free version.</div>
            <div style="position: absolute; background: gray; width: 92%; height: 65%; opacity: 0.3;">
            </div>
            <table>
              <tr>
                <td class="smaller_font">Select a Form:</td>
                <td class="smaller_font">
                  <select name="submissions_id" id="submissions_id" style="width: 230px; text-align: left;">
                    <option value="- Select Form -" selected="selected">- Select a Form -</option>
                    <?php
                    foreach ($rows as $row) {
                      ?>
                    <option value="<?php echo $row->id; ?>"><?php echo $row->title; ?></option>
                      <?php
                    }
                    ?>
                  </select>
                </td>
              </tr>
              <tr>
                <td class="smaller_font">Select Date Range:</td>
                <td class="smaller_font">
                  <!--<label style="min-width:30px !important;">From:</label>-->
                  <input class="inputbox" type="text" name="startdate" id="startdate" size="10" maxlength="10" value="" />
                  <input type="reset" style="width: 22px; border-radius: 3px !important;" class="button" value="..." onclick="return showCalendar('startdate','%Y-%m-%d');" />
                  <label style="min-width:30px !important;">To:</label>
                  <input class="inputbox" type="text" name="enddate" id="enddate" size="10" maxlength="10" value="" />
                  <input type="reset" style="width: 22px; border-radius: 3px !important;" class="button" value="..." onclick="return showCalendar('enddate','%Y-%m-%d');" />
                </td>
              </tr>
              <tr>
                <td colspan="2">
                  <table>
                    <tr>
                      <td style="border-right: 1px solid black;">
                        <table>
                          <tr>
                            <td class="smaller_font" style="vertical-align: top;">Select fields:</td>
                            <td class="smaller_font">
                              <ul>
                                <li>
                                  <input type="checkbox" checked="checked" id="submit_date" name="submit_date" value="submit_date">
                                  <label for="submit_date">Submit Date</label>
                                </li>
                                <li>
                                  <input type="checkbox" checked="checked" id="submitter_ip" name="submitter_ip" value="submitter_ip">
                                  <label for="submitter_ip">Submitter's IP Address</label>
                                </li>
                                <li>
                                  <input type="checkbox" checked="checked" id="username" name="username" value="username">
                                  <label for="username">Submitter's Username</label>
                                </li>
                                <li>
                                  <input type="checkbox" checked="checked" id="useremail" name="useremail" value="useremail">
                                  <label for="useremail">Submitter's Email Address</label>
                                </li>
                                <li>
                                  <input type="checkbox" checked="checked" id="form_fields" name="form_fields" value="form_fields">
                                  <label for="form_fields">Form Fields</label>                                  
                                </li>
                                <li><label style="font-size: 10px; width: 160px;">You can hide specific form fields from Form General Options.</label></li>
                              </ul>
                            </td>
                          </tr>
                          <tr>
                            <td class="smaller_font" style="vertical-align: top;">Export to:</td>
                            <td class="smaller_font">
                              <ul>
                                <li>
                                  <input type="checkbox" checked="checked" id="csv" name="csv" value="csv">
                                  <label for="csv">CSV</label>
                                </li>
                                <li>
                                  <input type="checkbox" checked="checked" id="xml" name="xml" value="xml">
                                  <label for="xml">XML</label>
                                </li>
                              </ul>
                            </td>
                          </tr>
                        </table>
                      </td>
                      <td style="vertical-align: top;">
                        <table>
                          <tr>
                            <td class="smaller_font" style="vertical-align: top;">Show:</td>
                            <td class="smaller_font">
                              <ul>
                                <li>
                                  <input type="checkbox" checked="checked" id="title" name="title" value="title">
                                  <label for="title">Title</label>
                                </li>
                                <li>
                                  <input type="checkbox" checked="checked" id="search" name="search" value="search">
                                  <label for="search">Search</label>
                                </li>
                                <li>
                                  <input type="checkbox" checked="checked" id="ordering" name="ordering" value="ordering">
                                  <label for="ordering">Ordering</label>
                                </li>
                                <li>
                                  <input type="checkbox" checked="checked" id="entries" name="entries" value="entries">
                                  <label for="entries">Entries</label>
                                </li>
                                <li>
                                  <input type="checkbox" checked="checked" id="views" name="views" value="views">
                                  <label for="views">Views</label>
                                </li>
                                <li>
                                  <input type="checkbox" checked="checked" id="conversion_rate" name="conversion_rate" value="conversion_rate">
                                  <label for="conversion_rate">Conversion Rate</label>
                                </li>
                                <li>
                                  <input type="checkbox" checked="checked" id="pagination" name="pagination" value="pagination">
                                  <label for="pagination">Pagination</label>
                                </li>
                                <li>
                                  <input type="checkbox" checked="checked" id="stats" name="stats" value="stats">
                                  <label for="stats">Statistics</label>
                                </li>
                              </ul>
                            </td>
                          </tr>
                        </table>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
            </table>
          </div>
        </div>
        <div class="mceActionPanel">
          <div style="float: left;">
            <input type="button" id="cancel" name="cancel" value="Cancel" onClick="tinyMCEPopup.close();"/>
          </div>
          <div style="float: right;">
            <input type="submit" id="insert" name="insert" value="Insert" onClick="form_maker_insert_shortcode();"/>
          </div>
        </div>
        <script type="text/javascript">
          var short_code = get_params("wd_contact_form");
          if (short_code) {
            if (!short_code['type']) {
              document.getElementById("form_maker_id").value = short_code['id'];
            }
          }
          // Get shortcodes attributes.
          function get_params(module_name) {
            var selected_text = tinyMCE.activeEditor.selection.getContent();
            var module_start_index = selected_text.indexOf("[" + module_name);
            var module_end_index = selected_text.indexOf("]", module_start_index);
            var module_str = "";
            if ((module_start_index == 0) && (module_end_index > 0)) {
              module_str = selected_text.substring(module_start_index + 1, module_end_index);
            }
            else {
              return false;
            }
            var params_str = module_str.substring(module_str.indexOf(" ") + 1);
            var key_values = params_str.split(" ");
            var short_code_attr = new Array();
            for (var key in key_values) {
              var short_code_index = key_values[key].split('=')[0];
              var short_code_value = key_values[key].split('=')[1];
              short_code_value = short_code_value.substring(1, short_code_value.length - 1);
              short_code_attr[short_code_index] = short_code_value;
            }
            return short_code_attr;
          }
          function form_maker_insert_shortcode() {
            if (document.getElementById('display_panel').className !== 'panel') {
              if (document.getElementById('form_maker_id').value == '- Select Form -') {
                tinyMCEPopup.close();
              }
              else {
                var tagtext;
              tagtext = '[wd_contact_form id="' + document.getElementById('form_maker_id').value + '"]';
                window.tinyMCE.execCommand('mceInsertContent', false, tagtext);
                tinyMCEPopup.close();
              }
            }            
            else {
              alert("Front end submissions are disabled in free version.");
            }
          }
        </script>
      </body>
    </html>
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