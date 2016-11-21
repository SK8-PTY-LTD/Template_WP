function SGMBLivePreview()
{
	this.widget = '';
	this.roundButton = '';
	this.betweenSize = '1px'
	this.icon = 'default';
	this.iconEffect = jQuery("[name='iconsEffect']").val();
	this.buttonsEffect = jQuery("[name='buttonsEffect']").val();
}

SGMBLivePreview.prototype.getWidget = function()
{
	return this.widget;
}

SGMBLivePreview.prototype.setWidget = function(wdg)
{
	this.widget = wdg;
}

SGMBLivePreview.prototype.addSelectboxValuesIntoInput = function() {
	
	var selectedPosts = [];
	jQuery("#add-form").submit(function(e) {
		var posts = jQuery("select[data-selectbox='sgmbSelectedPosts'] > option:selected");	
		for(i=0; i<posts.length; i++) {
			selectedPosts.push(posts[i].value);
		}
		jQuery(".sgmb-all-selected-post").val(selectedPosts);
	});
}

SGMBLivePreview.prototype.init = function()
{
	var that = this;
	var sgmbColorPicker = '';
	jQuery('.dropdownWrapper').hide();
	jQuery('.sgmb-dropdown-advance-options').hide();
	jQuery('.showEveryPostOptions').hide();
	jQuery('.options-for-buttons-fixed-position').hide();
	this.roundButton = jQuery('[name = roundButton]');
	this.showLabels = jQuery('[name = showLabels]');
	this.betweenSize = jQuery('.sgmb-betweenButtons').val();
	this.addSelectboxValuesIntoInput();

	jQuery(".js-social-btn-text").bind('input', function() {
		var btnText = jQuery(this).val();
		var btnName = jQuery(this).attr('data-social-button');
		that.widget.changeButtonText(btnText, btnName);
		that.widget.changeToRoundButtons(that.roundButton.is(':checked'));
		that.widget.changeBetweenButtonsSize(that.betweenSize);
	});

	jQuery('[name = roundButton]').bind('change', function() {
		var inputValue = jQuery(this).is(':checked');
 		that.widget.changeToRoundButtons(inputValue);
 		that.widget.changeBetweenButtonsSize(that.betweenSize);
	});

	jQuery('[name = showLabels]').bind('change', function() {
		var inputValue = jQuery(this).is(':checked');
 		that.widget.showLabels(inputValue);
 		that.widget.changeToRoundButtons(that.roundButton.is(':checked'));
 		that.widget.changeButtonsEffect(that.buttonsEffect);
		that.widget.changeIconsEffect(that.iconEffect);
		that.widget.fbLikeParse();
		that.widget.twitterFollowLoad();
		that.widget.changeAttrOfButton();
		that.widget.changeBetweenButtonsSize(that.betweenSize);
	});

	jQuery('[name = showCounts]').bind('change', function() {
		var inputValue = jQuery(this).is(':checked');
 		that.widget.showCounts(inputValue);
 		that.widget.changeToRoundButtons(that.roundButton.is(':checked'));
 		that.widget.changeButtonsEffect(that.buttonsEffect);
		that.widget.changeIconsEffect(that.iconEffect);
		that.widget.fbLikeParse();
		that.widget.twitterFollowLoad();
		that.widget.changeAttrOfButton();
		that.widget.changeBetweenButtonsSize(that.betweenSize);
	});

	jQuery('[name = showButtonsAsList]').bind('change', function() {
		var inputValue = jQuery(this).is(':checked');
		if(inputValue == true) {
			jQuery('[name = setButtonsPosition]').attr('checked',false);
			var inputValueSetButtonsPosition = jQuery('[name = setButtonsPosition]').is(':checked');
			that.widget.showButtonsPositionChecked(inputValueSetButtonsPosition);
		}
 		that.widget.showButtonsAsList(inputValue);
	});

	jQuery('[name = showOnAllPost]').bind('change', function() {
		var inputValue = jQuery(this).is(':checked');
 		that.widget.disabledSelectPostsOption(inputValue);
	});
	
	jQuery('[name = showButtonsOnEveryPost]').bind('change', function() {
		var inputValue = jQuery(this).is(':checked');
		if(inputValue == true) {
			jQuery('[name = setButtonsPosition]').attr('checked',false);
			var inputValueSetButtonsPosition = jQuery('[name = setButtonsPosition]').is(':checked');
			that.widget.showButtonsPositionChecked(inputValueSetButtonsPosition);
		}
		that.widget.showButtonsOnEveryPostChecked(inputValue);
	});

	jQuery('[name = setButtonsPosition]').bind('change', function() {
		var inputValue = jQuery(this).is(':checked');
		if(inputValue == true) {
			jQuery('[name = showButtonsOnEveryPost]').attr('checked',false);
			jQuery('[name = showButtonsAsList]').attr('checked',false);
			var inputValueShowButtonsOnEveryPost = jQuery('[name = showButtonsOnEveryPost]').is(':checked');
			var inputValueShowButtonsAsList = jQuery('[name = showButtonsAsList]').is(':checked');
			that.widget.showButtonsOnEveryPostChecked(inputValueShowButtonsOnEveryPost);
			that.widget.showButtonsAsList(inputValueShowButtonsAsList);
		}
		that.widget.showButtonsPositionChecked(inputValue);
	});

	jQuery('.js-social-btn-status').on('socialButtonShow', function(e){

		that.widget.setShareUrl("http://google.com");
		var socialButtonName = jQuery(this).attr('data-social-button');
		var buttonCustomName = jQuery("input[type='text'][data-social-button="+socialButtonName+"]").val();
    	if(jQuery("[name='logo']").val() != '') {
    		that.icon = jQuery("[name='logo']").val();
    	}
    	else {
    		jQuery("[name='logo']").val('default');
    	}
   		that.widget.setSocialOptions(socialButtonName,buttonCustomName);
		that.widget.changeLogo(that.icon);
		that.widget.showLabels(that.showLabels.is(':checked'));
		that.widget.changeToRoundButtons(that.roundButton.is(':checked'));
		that.widget.changeButtonsEffect(that.buttonsEffect);
		that.widget.changeIconsEffect(that.iconEffect);
		that.widget.changeBetweenButtonsSize(that.betweenSize);
	});

	jQuery('.js-social-btn-status').on('dragComplete', function(e){
		that.widget.fbLikeParse();
		that.widget.twitterFollowLoad();
		that.widget.changeAttrOfButton();
	});

	jQuery("[name='fbLikeLayout']").bind('change',function() {
		var fbLikeLayout = jQuery(this).val();
		that.widget.setFbLikeLayout(fbLikeLayout);
		that.widget.changeBetweenButtonsSize(that.betweenSize);
	});

	jQuery("[name='fbLikeActionType']").bind('change',function() {
		var fbLikeActionType = jQuery(this).val();
		that.widget.setFbLikeActionType(fbLikeActionType);
		that.widget.twitterFollowLoad();
		that.widget.changeBetweenButtonsSize(that.betweenSize);
	});

	jQuery('.js-social-btn-status').on('socialButtonHide', function(e){
    	var socialButtonName = jQuery(this).attr('data-social-button');
		that.widget.socialButtonsHide(socialButtonName);
		that.widget.showLabels(that.showLabels.is(':checked'));
		that.widget.changeToRoundButtons(that.roundButton.is(':checked'));
		that.widget.changeButtonsEffect(that.buttonsEffect);
		that.widget.changeIconsEffect(that.iconEffect);
		that.widget.changeBetweenButtonsSize(that.betweenSize);
	});

	jQuery("[name='sgmbSocialButtonSize']").bind('change', function() {
		var fontSize = jQuery(this).val();
		that.widget.changeButtonSize(fontSize);
		that.widget.changeToRoundButtons(that.roundButton.is(':checked'));
		that.widget.changeBetweenButtonsSize(that.betweenSize);
	});

	jQuery("[name='betweenButtons']").bind('input', function() {
		that.betweenSize = jQuery(this).val();
		that.widget.changeBetweenButtonsSize(that.betweenSize);
	});
	
	jQuery("[name='sgmbDropdownLabelFontSize']").bind('change', function() {
		var fontSize = jQuery(this).val();
		that.widget.changeDropdownLabelSize(fontSize);
	});

	jQuery("input:radio[name=theme]").bind('change', function() {
		var theme = jQuery(this).val();
		var newTheme = sgmb.theme[theme]['socialTheme'];
		that.icon = sgmb.theme[theme]['icons'];
		that.switchTheme(newTheme,that.icon);
		jQuery("[name='socialTheme']").val(newTheme);
		jQuery("[name='logo']").val(that.icon);
	});

	jQuery("[name='buttonsPanelEffect']").bind('change', function() {
		var newEffect = jQuery(this).val();
		that.widget.changePanelEffect(newEffect);
	});

	jQuery("[name='buttonsEffect']").bind('change', function() {
		that.buttonsEffect = jQuery(this).val();
		that.widget.changeButtonsEffect(that.buttonsEffect);
	});

	jQuery("[name='iconsEffect']").bind('change', function() {
		that.iconEffect = jQuery(this).val();
		that.widget.changeIconsEffect(that.iconEffect);
	});
}

SGMBLivePreview.prototype.currentTheme = 'classic';

SGMBLivePreview.prototype.switchTheme = function(newTheme, icon) {
	var that = this;
	var $cssLink = jQuery('#jssocials_theme_tm-css');
	var cssPath = $cssLink.attr("href");
	if (jQuery("[name='socialTheme']").val() != '') {
		this.currentTheme = jQuery("[name='socialTheme']").val();
	}
	$cssLink.attr("href", cssPath.replace(this.currentTheme, newTheme));
	this.currentTheme = newTheme;
	that.widget.changeLogo(icon);
	that.widget.changeToRoundButtons(that.roundButton.is(':checked'));
	that.widget.changeButtonsEffect(that.buttonsEffect);
	that.widget.changeIconsEffect(that.iconEffect);
	that.widget.changeAttrOfButton();
}
