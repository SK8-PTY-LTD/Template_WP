function tableOrdering(order, dir, task) {
  var form = document.admin_form;
  form.filter_order2.value = order;
  form.filter_order_Dir2.value = dir;
  submitform(task);
}

function ordering(name, as_or_desc) {
  document.getElementById('asc_or_desc').value = as_or_desc;
  document.getElementById('order_by').value = name;
  document.getElementById('admin_form').submit();
}

function renderColumns() {
  allTags = document.getElementsByTagName('*');
  for (curTag in allTags) {
    if (typeof(allTags[curTag].className) != "undefined") {
      if (allTags[curTag].className.indexOf('_fc') > 0) {
        curLabel = allTags[curTag].className.replace('_fc', '');
        curLabel = curLabel.replace('table_large_col ', '');
        if (document.forms.admin_form.hide_label_list.value.indexOf('@' + curLabel + '@') >= 0) {
          allTags[curTag].style.display = 'none';
        }
        else {
          allTags[curTag].style.display = '';
        }
      }
    }
    if (typeof(allTags[curTag].id) != "undefined") {
      if (allTags[curTag].id.indexOf('_fc') > 0) {
        curLabel = allTags[curTag].id.replace('_fc','');
        if (document.forms.admin_form.hide_label_list.value.indexOf('@' + curLabel + '@') >= 0) {
          allTags[curTag].style.display = 'none';
        }
        else {
          allTags[curTag].style.display = '';
        }
      }
    }
  }
}

function clickLabChB(label, ChB) { 
  document.forms.admin_form.hide_label_list.value = document.forms.admin_form.hide_label_list.value.replace('@' + label + '@', '');
  if (document.forms.admin_form.hide_label_list.value == '') {
    document.getElementById('ChBAll').checked = true;
  }
  if (!(ChB.checked)) {
    document.forms.admin_form.hide_label_list.value += '@' + label + '@';
    document.getElementById('ChBAll').checked = false;
  }
  renderColumns();
}

function toggleChBDiv(flag) { 
  if (flag) {
    /* sizes = window.getSize().size;*/
    var width = jQuery(window).width();
    var height = jQuery(window).height();
    document.getElementById("sbox-overlay").style.width = width + "px";
    document.getElementById("sbox-overlay").style.height = height + "px";
    document.getElementById("ChBDiv").style.left = Math.floor((width - 350) / 2) + "px";

    document.getElementById("ChBDiv").style.display = "block";
    document.getElementById("sbox-overlay").style.display = "block";
  }
  else {
    document.getElementById("ChBDiv").style.display = "none";
    document.getElementById("sbox-overlay").style.display = "none";
  }
}

function submit_del(href_in) {
  document.getElementById('admin_form').action = href_in;
  document.getElementById('admin_form').submit();
}

function submitbutton(pressbutton) {
  var form = document.adminForm;
  if (pressbutton == 'cancel_theme') {
    submitform(pressbutton);
    return;
  }
  if (document.getElementById('title').value == '') {
    alert('The theme must have a title.')
    return;
  }
  submitform(pressbutton);
}

function submitform(pressbutton) {
  document.getElementById('adminForm').action = document.getElementById('adminForm').action + "&task=" + pressbutton;
  document.getElementById('adminForm').submit();
}
