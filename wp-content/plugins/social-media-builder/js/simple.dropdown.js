jQuery(document).on('ready', function(){
  var dropdown = jQuery('div.dropdownWrapper'),
    drpBtn   = dropdown.find('div.dropdownLabel');
  drpBtn.on('click', function(e){
    e.stopPropagation();
    var element = jQuery(this).parent();
    element.find('.dropdownPanel').fadeToggle(200);
  });
  jQuery("body").click(function(){
    jQuery('.dropdownPanel').hide(200);
  });
});
