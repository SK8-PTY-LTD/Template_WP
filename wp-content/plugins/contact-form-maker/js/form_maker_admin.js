function fm_select_value(obj) {
  obj.focus();
  obj.select();
}

function fm_doNothing(event) {
  var keyCode = event.keyCode ? event.keyCode : event.which ? event.which : event.charCode;
  if (keyCode == 13) {
    if (event.preventDefault) {
      event.preventDefault();
    }
    else {
      event.returnValue = false;
    }
  }
}

function fm_ajax_save(form_id) {
  var search_value = jQuery("#search_value").val();
  var current_id = jQuery("#current_id").val();
  var page_number = jQuery("#page_number").val();
  var search_or_not = jQuery("#search_or_not").val();
  var ids_string = jQuery("#ids_string").val();
  var image_order_by = jQuery("#image_order_by").val();
  var asc_or_desc = jQuery("#asc_or_desc").val();
  var ajax_task = jQuery("#ajax_task").val();
  var image_current_id = jQuery("#image_current_id").val();
  ids_array = ids_string.split(",");

  var post_data = {};
  post_data["search_value"] = search_value;
  post_data["current_id"] = current_id;
  post_data["page_number"] = page_number;
  post_data["image_order_by"] = image_order_by;
  post_data["asc_or_desc"] = asc_or_desc;
  post_data["ids_string"] = ids_string;
  post_data["task"] = "ajax_search";
  post_data["ajax_task"] = ajax_task;
  post_data["image_current_id"] = image_current_id;

  jQuery.post(
    jQuery('#' + form_id).action,
    post_data,

    function (data) {
      var str = jQuery(data).find('#images_table').html();
      jQuery('#images_table').html(str);
      var str = jQuery(data).find('#tablenav-pages').html();
      jQuery('#tablenav-pages').html(str);
      jQuery("#show_hide_weights").val("Hide order column");
      fm_show_hide_weights();
      fm_run_checkbox();
    }
  ).success(function (jqXHR, textStatus, errorThrown) {
  });
  return false;
}

function fm_run_checkbox() {
  jQuery("tbody").children().children(".check-column").find(":checkbox").click(function (l) {
    if ("undefined" == l.shiftKey) {
      return true
    }
    if (l.shiftKey) {
      if (!i) {
        return true
      }
      d = jQuery(i).closest("form").find(":checkbox");
      f = d.index(i);
      j = d.index(this);
      h = jQuery(this).prop("checked");
      if (0 < f && 0 < j && f != j) {
        d.slice(f, j).prop("checked", function () {
          if (jQuery(this).closest("tr").is(":visible")) {
            return h
          }
          return false
        })
      }
    }
    i = this;
    var k = jQuery(this).closest("tbody").find(":checkbox").filter(":visible").not(":checked");
    jQuery(this).closest("table").children("thead, tfoot").find(":checkbox").prop("checked", function () {
      return(0 == k.length)
    });
    return true
  });
  jQuery("thead, tfoot").find(".check-column :checkbox").click(function (m) {
    var n = jQuery(this).prop("checked"), l = "undefined" == typeof toggleWithKeyboard ? false : toggleWithKeyboard, k = m.shiftKey || l;
    jQuery(this).closest("table").children("tbody").filter(":visible").children().children(".check-column").find(":checkbox").prop("checked", function () {
      if (jQuery(this).is(":hidden")) {
        return false
      }
      if (k) {
        return jQuery(this).prop("checked")
      } else {
        if (n) {
          return true
        }
      }
      return false
    });
    jQuery(this).closest("table").children("thead,  tfoot").filter(":visible").children().children(".check-column").find(":checkbox").prop("checked", function () {
      if (k) {
        return false
      } else {
        if (n) {
          return true
        }
      }
      return false
    })
  });
}

// Set value by id.
function fm_set_input_value(input_id, input_value) {
  if (document.getElementById(input_id)) {
    document.getElementById(input_id).value = input_value;
  }
}

// Submit form by id.
function fm_form_submit(event, form_id, task, id) {
  if (document.getElementById(form_id)) {
    document.getElementById(form_id).submit();
  }
  if (event.preventDefault) {
    event.preventDefault();
  }
  else {
    event.returnValue = false;
  }
}

// Check if required field is empty.
function fm_check_required(id, name) {
  if (jQuery('#' + id).val() == '') {
    alert(name + '* field is required.');
    jQuery('#' + id).attr('style', 'border-color: #FF0000; border-style: solid; border-width: 1px;');
    jQuery('#' + id).focus();
    jQuery('html, body').animate({
      scrollTop:jQuery('#' + id).offset().top - 200
    }, 500);
    return true;
  }
  else {
    return false;
  }
}

// Show/hide order column and drag and drop column.
function fm_show_hide_weights() {
  if (jQuery("#show_hide_weights").val() == 'Show order column') {
    jQuery(".connectedSortable").css("cursor", "default");
    jQuery("#tbody_arr").find(".handle").hide(0);
    jQuery("#th_order").show(0);
    jQuery("#tbody_arr").find(".fm_order").show(0);
    jQuery("#show_hide_weights").val("Hide order column");
    if (jQuery("#tbody_arr").sortable()) {
      jQuery("#tbody_arr").sortable("disable");
    }
  }
  else {
    jQuery(".connectedSortable").css("cursor", "move");
    var page_number;
    if (jQuery("#page_number") && jQuery("#page_number").val() != '' && jQuery("#page_number").val() != 1) {
      page_number = (jQuery("#page_number").val() - 1) * 20 + 1;
    }
    else {
      page_number = 1;
    }
    jQuery("#tbody_arr").sortable({
      handle:".connectedSortable",
      connectWith:".connectedSortable",
      update:function (event, tr) {
        jQuery("#draganddrop").attr("style", "");
        jQuery("#draganddrop").html("<strong><p>Changes made in this table should be saved.</p></strong>");
        var i = page_number;
        jQuery('.fm_order').each(function (e) {
          if (jQuery(this).find('input').val()) {
            jQuery(this).find('input').val(i++);
          }
        });
      }
    });//.disableSelection();
    jQuery("#tbody_arr").sortable("enable");
    jQuery("#tbody_arr").find(".handle").show(0);
    jQuery("#tbody_arr").find(".handle").attr('class', 'handle connectedSortable');
    jQuery("#th_order").hide(0);
    jQuery("#tbody_arr").find(".fm_order").hide(0);
    jQuery("#show_hide_weights").val("Show order column");
  }
}

function fm_popup(id) {
  if (typeof id === 'undefined') {
    var id = '';
  }
  var thickDims, tbWidth, tbHeight;
      thickDims = function() {
        var tbWindow = jQuery('#TB_window'), H = jQuery(window).height(), W = jQuery(window).width(), w, h;
        w = (tbWidth && tbWidth < W - 90) ? tbWidth : W - 40;
        h = (tbHeight && tbHeight < H - 60) ? tbHeight : H - 40;
        if (tbWindow.size()) {
          tbWindow.width(w).height(h);
          jQuery('#TB_iframeContent').width(w).height(h - 27);
          tbWindow.css({'margin-left': '-' + parseInt((w / 2),10) + 'px'});
          if (typeof document.body.style.maxWidth != 'undefined') {
            tbWindow.css({'top':(H-h)/2,'margin-top':'0'});
          }
        }
      };
  thickDims();
  jQuery(window).resize(function() { thickDims() });
  jQuery('a.thickbox-preview' + id).click( function() {
    tb_click.call(this);
    var alink = jQuery(this).parents('.available-theme').find('.activatelink'), link = '', href = jQuery(this).attr('href'), url, text;
    if (tbWidth = href.match(/&width=[0-9]+/)) {
      tbWidth = parseInt(tbWidth[0].replace(/[^0-9]+/g, ''), 10);
    }
    else {
      tbWidth = jQuery(window).width() - 120;
    }
    
    if (tbHeight = href.match(/&height=[0-9]+/)) {
      tbHeight = parseInt(tbHeight[0].replace(/[^0-9]+/g, ''), 10);
    }
    else {
      tbHeight = jQuery(window).height() - 120;
    }
    if (alink.length) {
      url = alink.attr('href') || '';
      text = alink.attr('title') || '';
      link = '&nbsp; <a href="' + url + '" target="_top" class="tb-theme-preview-link">' + text + '</a>';
    }
    else {
      text = jQuery(this).attr('title') || '';
      link = '&nbsp; <span class="tb-theme-preview-link">' + text + '</span>';
    }
    jQuery('#TB_title').css({'background-color':'#222','color':'#dfdfdf'});
    jQuery('#TB_closeAjaxWindow').css({'float':'right'});
    jQuery('#TB_ajaxWindowTitle').css({'float':'left'}).html(link);
    jQuery('#TB_iframeContent').width('100%');
    thickDims();
    return false;
  });
  // Theme details
  jQuery('.theme-detail').click(function () {
    jQuery(this).siblings('.themedetaildiv').toggle();
    return false;
  });
}

function bwg_inputs() {
  jQuery(".fm_int_input").keypress(function (event) {
    var chCode1 = event.which || event.paramlist_keyCode;
    if (chCode1 > 31 && (chCode1 < 48 || chCode1 > 57) && (chCode1 != 46) && (chCode1 != 45)) {
      return false;
    }
    return true;
  });
}


function fm_check_isnum(e) {
  var chCode1 = e.which || e.paramlist_keyCode;
  if (chCode1 > 31 && (chCode1 < 48 || chCode1 > 57) && (chCode1 != 46) && (chCode1 != 45)) {
    return false;
  }
  return true;
}

function stopRKey(evt) { 
  var evt = (evt) ? evt : ((event) ? event : null); 
  var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null); 
  if ((evt.keyCode == 13) && (node.type=="text"))  {return false;} 
} 

document.onkeypress = stopRKey;