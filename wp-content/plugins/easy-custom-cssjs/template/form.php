<?php
//Bright
$themes1 = array(0 => 'chrome', 1 => 'clouds', 2 => 'crimson_editor',
    3 => 'dawn', 4 => 'dreamweaver', 5 => 'eclipse', 6 => 'github',
    7 => 'iplastic', 8 => 'solarized_light'
    , 9 => 'textmate', 10 => 'tomorrow', 11 => 'xcode', 11 => 'kuroir'
    , 12 => 'katzenmilch', 13 => 'sqlserver'
);

//Dark
$themes2 = array(14 => 'Ambiance', 15 => 'chaos', 16 => 'clouds_midnight',
    17 => 'cobalt', 18 => 'idle_fingers', 19 => 'kr_theme', 20 => 'merbivore',
    7 => 'merbivore_soft', 8 => 'mono_industrial'
    , 21 => 'monokai', 22 => 'pastel_on_dark', 23 => 'solarized_dark', 24 => 'terminal'
    , 25 => 'tomorrow_night', 26 => 'tomorrow_night_blue'
    , 27 => 'tomorrow_night_bright'
    , 28 => 'tomorrow_night_eighties'
    , 29 => 'twilight'
    , 30 => 'vibrant_ink'
);

$ptypes = get_post_types(array('public' => true, '_builtin' => false));


if (!defined('ABSPATH'))
       exit; // 

if (!current_user_can('manage_options'))
       return;

if ($_POST) {
       $retrieved_nonce = $_REQUEST['_wpnonce'];
       if (!wp_verify_nonce($retrieved_nonce, 'spcc_form_submit_action'))
              die('Failed security check');
}

if (isset($_POST['spcc_settings'])) {

       if (get_option('spcc_settings')) {
              update_option('spcc_settings', $_POST['spcc_settings']);
       } else {
              add_option('spcc_settings', $_POST['spcc_settings']);
       }
}
if (isset($_POST['main_custom_style'])) {

       $main_custom_style = $_POST['main_custom_style'];
       spcc_set_custom_css($main_custom_style);
       $success = true;
}

if (isset($_POST['main_custom_style_lg'])) {
       $main_custom_style = $_POST['main_custom_style_lg'];
       spcc_set_custom_css($main_custom_style, 'lg');
       $success = true;
}

if (isset($_POST['main_custom_style_md'])) {
       $main_custom_style = $_POST['main_custom_style_md'];
       spcc_set_custom_css($main_custom_style, 'md');
       $success = true;
}

if (isset($_POST['main_custom_style_sm'])) {
       $main_custom_style = $_POST['main_custom_style_sm'];
       spcc_set_custom_css($main_custom_style, 'sm');
       $success = true;
}

if (isset($_POST['main_custom_style_xs'])) {
       $main_custom_style = $_POST['main_custom_style_xs'];
       spcc_set_custom_css($main_custom_style, 'xs');
       $success = true;
}


if (isset($_POST['main_custom_style_less'])) {
       $main_custom_style = $_POST['main_custom_style_less'];
       spcc_set_custom_css($main_custom_style, 'less');
       $success = true;
}

if (isset($_POST['main_custom_style_sass'])) {
       $main_custom_style = $_POST['main_custom_style_sass'];
       spcc_set_custom_css($main_custom_style, 'sass');
       $success = true;
}
if (isset($_POST['main_custom_js'])) {
       $main_custom_style = '"' . $_POST['main_custom_js'] . '"';
       spcc_set_custom_css($main_custom_style, 'js');
       $success = true;
}

if (isset($_POST['main_custom_js_footer'])) {
       $main_custom_style = '"' . $_POST['main_custom_js_footer'] . '"';
       spcc_set_custom_css($main_custom_style, 'footer_js');
       $success = true;
}

if (isset($success)) {
       ?>
       <div class="updated">
           <p>
               <strong>Changes have been saved.</strong>
           </p>
       </div>
       <?php
}

$custom_css = esc_textarea(spcc_get_custom_css());
$custom_css_lg = esc_textarea(spcc_get_custom_css('lg'));
$custom_css_md = esc_textarea(spcc_get_custom_css('md'));
$custom_css_sm = esc_textarea(spcc_get_custom_css('sm'));
$custom_css_xs = esc_textarea(spcc_get_custom_css('xs'));

$custom_css_less = esc_textarea(spcc_get_custom_css('less'));
$custom_css_sass = esc_textarea(spcc_get_custom_css('sass'));
$custom_js = esc_textarea(trim(spcc_get_custom_css('js'), '"'));
$custom_js_footer = esc_textarea(trim(spcc_get_custom_css('footer_js'), '"'));

$current_tab = 'main';

if ($_REQUEST['tab'] == 'responsive')
       $current_tab = 'responsive';

if ($_REQUEST['tab'] == 'less')
       $current_tab = 'less';

if ($_REQUEST['tab'] == 'sass')
       $current_tab = 'sass';

if ($_REQUEST['tab'] == 'js')
       $current_tab = 'js';

$url = esc_url(menu_page_url('spcc_custom_css', FALSE));
if (get_option('spcc_settings')) {
       $edit_settings = get_option('spcc_settings');
} else {
       $edit_settings = array();
}
?><div class="wrap sppc_wrap">
    <input type="hidden" id="spcc_selected_theme" name="spcc_selected_theme" value="<?php
    if (isset($edit_settings['spcc_editor_theme']) && $edit_settings['spcc_editor_theme'] != '') {
           echo $edit_settings['spcc_editor_theme'];
    } else {
           echo 'chrome';
    }
    ?>">
    <h2 id="main-title">Custom CSS/JS </h2>
    <h2 class="nav-tab-wrapper woo-nav-tab-wrapper sppc_tab">
        <a href="<?php echo $url; ?>&tab=main" class="nav-tab<?php echo $current_tab == 'main' ? ' nav-tab-active' : ''; ?>">General Style</a>
        <a href="<?php echo $url; ?>&tab=responsive" class="nav-tab<?php echo $current_tab == 'responsive' ? ' nav-tab-active' : ''; ?>">Responsive</a>
        <a href="<?php echo $url; ?>&tab=less" class="nav-tab<?php echo $current_tab == 'less' ? ' nav-tab-active' : ''; ?>">LESS</a>
        <a href="<?php echo $url; ?>&tab=sass" class="nav-tab<?php echo $current_tab == 'sass' ? ' nav-tab-active' : ''; ?>">SASS</a>
        <a href="<?php echo $url; ?>&tab=js" class="nav-tab<?php echo $current_tab == 'js' ? ' nav-tab-active' : ''; ?>">Javascript</a>
    </h2>

    <?php if ($current_tab == 'main'): ?>

           <form method="post">
               <h4>
                   <small>
                       For all Screen
                   </small>

                   General Stylesheet
               </h4>
               <?php wp_nonce_field('spcc_form_submit_action'); ?>
               <div id="editor" class="mh200"><?php echo $custom_css; ?></div>
               <input type="hidden" id="spcc_style" name="main_custom_style" value=""><br>
               <button type="submit" class="button button-primary save" name="save_changes">Save Changes</button>
           </form>


    <?php elseif ($current_tab == 'responsive'): ?>
           <h3>Targeting custom screen sizes</h3>
           <form method="post">
               <?php wp_nonce_field('spcc_form_submit_action'); ?>
               <h4>
                   <small>
                       Minimum Screen Size: <strong>1200px</strong>
                   </small>

                   LG - Large Screen
               </h4>

               <div id="editor_lg" class="mh200"><?php echo $custom_css_lg; ?></div>
               <input type="hidden" id="spcc_style_lg" name="main_custom_style_lg" value=""><br>

               <h4>
                   <small>
                       Minimum Screen Size: <strong>992px</strong>
                   </small>

                   MD - Medium Screen
               </h4>
               <div id="editor_md" class="mh200"><?php echo $custom_css_md; ?></div>
               <input type="hidden" id="spcc_style_md" name="main_custom_style_md" value=""><br>

               <h4>
                   <small>
                       Minimum Screen Size: <strong>768px</strong>
                   </small>

                   SM - Small Screen
               </h4>

               <div id="editor_sm" class="mh200"><?php echo $custom_css_sm; ?></div>
               <input type="hidden" id="spcc_style_sm" name="main_custom_style_sm" value=""><br>


               <h4>
                   <small>
                       Maximum Screen Size: <strong>768px</strong>
                   </small>

                   XS - Extra Small Screen
               </h4>

               <div id="editor_xs" class="mh200"><?php echo $custom_css_xs; ?></div>
               <input type="hidden" id="spcc_style_xs" name="main_custom_style_xs" value=""><br>

               <button type="submit" class="button button-primary save" name="save_changes">Save Changes</button>
           </form>
    <?php elseif ($current_tab == 'less'): ?>
           <!--<h3>Apply your own style in <a href="http://www.lesscss.org/" target="_blank">LESS</a> language</h3>-->

           <form method="post">
               <h4>

                   Enter "LESS" here
               </h4>
               <?php wp_nonce_field('spcc_form_submit_action'); ?>
               <div id="editor_less" class="mh200"><?php echo $custom_css_less; ?></div>
               <input type="hidden" id="spcc_style_less" name="main_custom_style_less" value=""><br>
               <button type="submit" class="button button-primary save" name="save_changes">Save Changes</button>
           </form>

    <?php elseif ($current_tab == 'sass'): ?>
           <!--<h3>Apply your own style in <a href="http://sass-lang.com/" target="_blank">SASS</a> language</h3>-->

           <form method="post">
               <h4>

                   Enter "SASS" here
               </h4>
               <?php wp_nonce_field('spcc_form_submit_action'); ?>
               <div id="editor_sass" class="mh200"><?php echo $custom_css_sass; ?></div>
               <input type="hidden" id="spcc_style_sass" name="main_custom_style_sass" value=""><br>
               <button type="submit" class="button button-primary save" name="save_changes">Save Changes</button>
           </form>

    <?php elseif ($current_tab == 'js'): ?>


           <form method="post">
               <h4>
                   Enter Custom JS here
                   <small>
                       Add Without document.ready wrap function
                   </small>
               </h4><br>
               <?php wp_nonce_field('spcc_form_submit_action'); ?>
               <h4><b> Add Js in Header </b></h4>
               <div id="editor_js" class="mh200"><?php echo $custom_js; ?></div>
               <input type="hidden" id="spcc_style_js" name="main_custom_js" value=""><br>
               <h4><b> Add Js in Footer </b></h4>
               <div id="editor_js_footer" class="mh200"><?php echo $custom_js_footer; ?></div>
               <input type="hidden" id="spcc_style_js_footer" name="main_custom_js_footer" value=""><br>
               <button type="submit" class="button button-primary save" name="save_changes">Save Changes</button>
           </form>

    <?php endif; ?>

</div>
<div class="wrap sppc_wrap ">

    <form method="post">
        <h4>
            Settings
        </h4><br>
        <?php wp_nonce_field('spcc_form_submit_action'); ?>
        <label>Theme : </label>
        <select class="form-control" name="spcc_settings[spcc_editor_theme]" id="spcc_editor_theme"  >
            <option value="">Select Theme </option>
            <optgroup label="Bright Theme">
                <?php
                if (!empty($themes1)) {
                       foreach ($themes1 as $theme) {
                              ?>
                              <option value="<?php echo $theme; ?>" <?php
                              if ($edit_settings['spcc_editor_theme'] != '' && $edit_settings['spcc_editor_theme'] == $theme) {
                                     echo 'selected';
                              }
                              ?>><?php echo ucfirst(str_replace('_', ' ', $theme)); ?></option>
                                      <?php
                               }
                        }
                        ?>
            </optgroup><optgroup label="Dark Theme">
                <?php
                if (!empty($themes2)) {
                       foreach ($themes2 as $theme) {
                              ?>
                              <option value="<?php echo $theme; ?>" <?php
                              if ($edit_settings['spcc_editor_theme'] != '' && $edit_settings['spcc_editor_theme'] == $theme) {
                                     echo 'selected';
                              }
                              ?>><?php echo ucfirst(str_replace('_', ' ', $theme)); ?></option>
                                      <?php
                               }
                        }
                        ?>
            </optgroup>
        </select><br>
        <label>Enable css/js option in custom post type :</label>
        <select class="form-control" name="spcc_settings[spcc_post_type][]" id="spcc_post_type"  multiple="">
            <option value="">Select Post type </option>
            <?php
            if (!empty($ptypes)) {
                   foreach ($ptypes as $ptype) {
                          ?>
                          <option value="<?php echo $ptype; ?>" <?php
                          if (!empty($edit_settings['spcc_post_type']) && in_array($ptype, $edit_settings['spcc_post_type'])) {
                                 echo 'selected';
                          }
                          ?>><?php echo ucfirst(str_replace('_', ' ', $ptype)); ?></option>
                                  <?php
                           }
                    }
                    ?>
        </select>
        <br>
        <button type="submit" class="button button-primary save" name="save_changes">Save Settings</button>

    </form>

</div>
<div class="wrap spcc_promo text-center clearfix">
    <div class="col-md-5">
        <h2>Find all shortcut <a href="https://ace.c9.io/demo/keyboard_shortcuts.html">Here</a> for editor.</h2>
        <h2>Don't Forget to Rate Me!!. <a href="https://wordpress.org/support/view/plugin-reviews/easy-custom-cssjs">Click Here</a></h2>
        <h2>Get more Detail about this plugin. <a href="http://sunilprajapati.in/easy-custom-cssjs/">Click Here</a></h2>
        <h2>FindOut My Other plugins. <a href="https://profiles.wordpress.org/sunil25393#content-plugins">Click Here</a></h2>
    </div>
    <div class="col-md-3 adv">
        <h2>
            <a href="https://wordpress.org/plugins/woo-donation/"> Donation Plugin For Woocommerce</a></h2>
        <a href="https://wordpress.org/plugins/woo-donation/">
            <img src="http://sunilprajapati.in/wp-content/uploads/2016/05/banner-772x250-1.png" alt="woo-donation" style="    width: 100%;">
        </a>
    </div>
    <div class="col-md-3 adv">
        <h2>
            <a href="https://wordpress.org/plugins/filterize-gallery/"> Filterize Gallery</a></h2>
        <a href="https://wordpress.org/plugins/filterize-gallery/">
            <img src="http://sunilprajapati.in/wp-content/uploads/2016/07/banner-772x250.png" alt="woo-donation" style="    width: 100%;">
        </a>
    </div>
</div>


