var $ = jQuery;
function SGMBWidget() {
	var that = this;
	this.media = '';
	this.options = {
		_getShareUrl: function() {
			var url = jsSocials.Socials.prototype._getShareUrl.apply(this, arguments);
			var title = 'Sharing';
			var w = 700;
			var h = 700;
			var left = (screen.width/2)-(w/2);
 			var top = (screen.height/2)-(h/2);
 			return "javascript:window.open('" + url + "', '" + title + "', 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width="+w+", height="+h+", top="+top+", left="+left+"'); return false;";
		},
		text: "Your share text", showCount: false, showLabel: true, shares: []
	};
	this.id = '';
	this.widgetCounter = 1;
	this.shareText = 'Your share text';
	this.fbLikeDataLayout = 'button';
	this.fbLikeDataAction = 'like';
	this.twitterFollowShowCount = 'false';
	this.twitterFollowSize = 'defualt';
	this.followUserName = 'Sygnoos';
	var that = this;
}

SGMBWidget.prototype.setShareUrl = function(shareUrl, postUrl)
{
	if (shareUrl != '') {
		this.options.url = shareUrl;
	}
	else if (postUrl != '') {
		this.options.url = postUrl;
	}
}


SGMBWidget.prototype.show = function(data, widgetCounter, hide, postImage, postUrl)
{
	if(data.options.shareText != '') {
		this.shareText = data.options.shareText;
	}
	this.media = postImage;
	var showButtonsOnMobileDirect = true ;
	if(data != '') {
		if(data.options.showButtonsOnMobileDirect != 'on') {
			showButtonsOnMobileDirect = false ;
		}
	}

	var obj =  data.buttonOptions;
	var that = this;
	if(data != '') {
		this.id = data.id;
		this.widgetCounter = widgetCounter;
	}
	if (data.button != '') {
		for (var buttonName in obj) {
			var labelName = obj[buttonName].label;
			var icon =  SGMB_URL+"/img/"+ obj[buttonName].icon +".png";
			var via = obj[buttonName].via;
			var hashtags = obj[buttonName].hashtags;
			this.setSocialOptions(buttonName, labelName, icon, via, hashtags);
		}
		if(obj) {
			this.changeButtonSize(data.options.fontSize);
			this.setShareUrl(data.options.url, postUrl);
			this.changePanelEffect(data.options.buttonsPanelEffect);
		}
	}
	this.jsSocial();
	if(data.options) {
		if(data.options.showCounts == 'on') {
			this.showCounts(true);
		}
		if(data.options.showLabels != 'on') {
			this.showLabels(false);
		}
		if(data.options.roundButton == 'on') {
			this.changeToRoundButtons(true);
		}
		if(data.options.showButtonsAsList == 'on') {
			this.changeColorDropdown(data.options.sgmbDropdownColor);
			this.changeColorDropdownLabel(data.options.sgmbDropdownLabelColor);
			this.changeDropdownLabelSize(data.options.sgmbDropdownLabelFontSize);
			this.showButtonsAsList(true);
		}
		else {
			this.showButtonsAsList(false);
		}
		if(data.options.setButtonsPosition == 'on') {
			this.showButtonsPositionChecked(true);
		}
		else {
			this.showButtonsPositionChecked(false);
		}
		if(data.options.showButtonsOnEveryPost == 'on') {
			this.showButtonsOnEveryPostChecked(true);
		}
		else {
			this.showButtonsOnEveryPostChecked(false);
		}
		if(data.options.showOnAllPost == 'on') {
			this.disabledSelectPostsOption(true);
		}
		else {
			this.disabledSelectPostsOption(false);
		}
		this.changeBetweenButtonsSize(data.options.betweenButtons);
		jQuery('.sgmbWidget'+this.id+'-'+this.widgetCounter+' .jssocials-share').unbind('mouseenter mouseleave').hover(function() {
			that.changeButtonsEffect(data.options.buttonsEffect);
		});
		jQuery('.sgmbWidget'+this.id+'-'+this.widgetCounter+' .jssocials-share-logo').unbind('mouseenter mouseleave').hover(function() {
			that.changeIconsEffect(data.options.iconsEffect);
		});
		jQuery('.sgmbWidget'+this.id+'-'+this.widgetCounter+' .jssocials-share-logo').addClass('sgmb-social-img');
		jQuery('.sgmb-dropdown-color .wp-color-result').css({'background-color' : data.options.sgmbDropdownColor});
		jQuery('.sgmb-dropdown-label-color .wp-color-result').css({'background-color' : data.options.sgmbDropdownLabelColor});
	}
	if(obj) {
		if(data.buttonOptions.fbLike) {
			this.setFbLikeLayout(data.buttonOptions.fbLike.fbLikeLayout);
			this.setFbLikeActionType(data.buttonOptions.fbLike.fbLikeActionType);
			this.setFbLikeUrl(data.options.url);
		}
		if(data.buttonOptions.twitterFollow) {
			if(data.buttonOptions.twitterFollow.twitterFollowShowCounts == 'on') {
				this.showCountsForTwitterFollow(true);
			}
			else {
				this.showCountsForTwitterFollow(false);
			}

			if(data.buttonOptions.twitterFollow.setLargeSizeForTwitterFollow == 'on') {
				this.setLargeSizeForTwitterFollow(true);
			}
			else {
				this.setLargeSizeForTwitterFollow(false);
			}
			this.setTwitterFollowUserName(data.buttonOptions.twitterFollow.followUserName);
		}
	}
	if(hide == '') {
		if(data.options.setButtonsPosition == 'on') {
			this.setPositionForButtonsPanel(data.options.buttonsPosition);
			jQuery(".jssocials-share-logo").one("load", function() {
				var width = jQuery(".sgmbWidget"+that.id+'-'+that.widgetCounter).width()/2;
				var height = jQuery(".sgmbWidget"+that.id+'-'+that.widgetCounter).height()/2;
				if(data.options.buttonsPosition == 'topCenter' || data.options.buttonsPosition == 'bottomCenter') {
					jQuery(".sgmbWidget"+that.id+'-'+that.widgetCounter).css({'margin-left' : -(width)+'px'});
				}
				if(data.options.buttonsPosition == 'left' || data.options.buttonsPosition == 'right') {
					jQuery(".sgmbWidget"+that.id+'-'+that.widgetCounter).css({'margin-top' : -(height)+'px'});
				}
			});
		}
		if(data.options.showCenter == 'on') {
			this.showCenter(true);
		}
	}
	this.changeAttrOfButton();
}

SGMBWidget.prototype.changeAttrOfButton = function()
{
	var that = this;
	jQuery('#sgmbShare'+this.id +'-'+this.widgetCounter+' a').each(function() {
		if(jQuery(this).attr('class') != 'twitter-follow-button') {
			var t = jQuery(this);
			t.attr({
				onclick : t.attr('href'),
			});
			t.attr('href','#');
			t.removeAttr('target');
		}
	});
}

SGMBWidget.prototype.twitterFollowLoad = function()
{
	if(typeof twttr !== 'undefined') {
		if(typeof twttr.widgets !== 'undefined') {
			twttr.widgets.load();
		}
	}
}

SGMBWidget.prototype.setSocialOptions = function(socialButtonName, labelName, logo, via, hashtags)
{
	if(socialButtonName == 'fbLike') {
		this.options.shares.push(this.fbLike);
	}
	else {
		if(socialButtonName == 'twitterFollow') {
			this.options.shares.push(this.twitterFollow);
		}
		else {
			if(socialButtonName == 'twitter') {
				this.options.shares.push({'share': socialButtonName, 'label':labelName, 'logo':logo, 'via':via, 'hashtags':hashtags, 'text': this.shareText});
			}
			else {
				this.options.shares.push({'share': socialButtonName,'label': labelName, 'logo': logo, 'media':this.media, 'text': this.shareText});
			}
		}
	}
	this.jsSocial();
}
SGMBWidget.prototype.showCountsForTwitterFollow = function(inputValue)
{
	var that = this;
	this.twitterFollowShowCount = inputValue;
	jQuery('.sgmbFollow').empty();
	jQuery("<a>").addClass("twitter-follow-button")
		.text("Tweet")
		.attr("href", 'https://twitter.com/'+that.followUserName)
		.attr("data-show-screen-name", 'false')
		.attr("data-show-count",  that.twitterFollowShowCount)
		.attr("data-size", that.twitterFollowSize)
		.appendTo(".sgmbFollow");
	this.twitterFollowLoad();
}

SGMBWidget.prototype.setLargeSizeForTwitterFollow = function(inputValue)
{
	var that = this;
	if(inputValue == true) {
		this.twitterFollowSize = 'large';
	}
	else {
		this.twitterFollowSize = 'default';
	}
	jQuery('.sgmbFollow').empty();
	jQuery("<a>").addClass("twitter-follow-button")
		.text("Tweet")
		.attr("href", 'https://twitter.com/'+that.followUserName)
		.attr("data-show-screen-name", 'false')
		.attr("data-show-count",  that.twitterFollowShowCount)
		.attr("data-size", that.twitterFollowSize)
		.appendTo(".sgmbFollow");
	this.twitterFollowLoad();
}

SGMBWidget.prototype.setTwitterFollowUserName = function(userName)
{
	var that = this;
	that.followUserName = userName;
	jQuery('.sgmbFollow').empty();
	jQuery("<a>").addClass("twitter-follow-button")
		.text("Tweet")
		.attr("href", 'https://twitter.com/'+that.followUserName)
		.attr("data-show-screen-name", 'false')
		.attr("data-show-count",  that.twitterFollowShowCount)
		.attr("data-size", that.twitterFollowSize)
		.appendTo(".sgmbFollow");
	this.twitterFollowLoad();
}

SGMBWidget.prototype.setPositionForButtonsPanel = function(position)
{
	switch (position) {
	    case 'left':
	        jQuery(".sgmbWidget"+this.id+'-'+this.widgetCounter).addClass('sgmb-floating-left');
	        break;
	    case 'topRight':
	        jQuery(".sgmbWidget"+this.id+'-'+this.widgetCounter).addClass('sgmb-floating-top-right');
	        break;
	    case 'topCenter':
	        jQuery(".sgmbWidget"+this.id+'-'+this.widgetCounter).addClass('sgmb-floating-top-center');
			break;
	    case 'topLeft':
	        jQuery(".sgmbWidget"+this.id+'-'+this.widgetCounter).addClass('sgmb-floating-top-left');
	        break;
	    case 'right':
	        jQuery(".sgmbWidget"+this.id+'-'+this.widgetCounter).addClass('sgmb-floating-right');
	        break;
	    case 'bottomLeft':
	        jQuery(".sgmbWidget"+this.id+'-'+this.widgetCounter).addClass('sgmb-floating-bottom-left');
	        break;
	    case 'bottomCenter':
	        jQuery(".sgmbWidget"+this.id+'-'+this.widgetCounter).addClass('sgmb-floating-bottom-center');
	        break;
	    case 'bottomRight':
		    jQuery(".sgmbWidget"+this.id+'-'+this.widgetCounter).addClass('sgmb-floating-bottom-right');
		    break;
	}
}

SGMBWidget.prototype.showButtonsPositionChecked = function(inputValue)
{
	if(inputValue == true) {
		jQuery('.options-for-buttons-fixed-position').show();
	}
	else {
		jQuery('.options-for-buttons-fixed-position').hide();
	}
}

SGMBWidget.prototype.disabledSelectPostsOption = function(inputValue)
{
	if(inputValue == true) {
		jQuery('.sgmb-select-posts select').attr('disabled', 'disabled');
	}
	else {
		jQuery('.sgmb-select-posts select').removeAttr('disabled');
	}
}

SGMBWidget.prototype.showButtonsOnEveryPostChecked = function(inputValue)
{
	if(inputValue == true) {
		jQuery('.showEveryPostOptions').show();
	}
	else {
		jQuery('.showEveryPostOptions').hide();
	}
}

SGMBWidget.prototype.changeColorDropdown = function(element)
{
	jQuery('.dropdownWrapper'+this.id).css({'background-color' : element});
}

SGMBWidget.prototype.changeColorDropdownLabel = function(element)
{
	jQuery('.sgmbButtonListLabel'+this.id).css({'color' : element});
}

SGMBWidget.prototype.changePanelEffect = function(newEffect)
{
	jQuery('.sgmbWidget'+this.id+'-'+this.widgetCounter).addClass('sgmb-animated '+'sgmb-'+newEffect).one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend',function(){
		jQuery(this).removeClass('sgmb-animated '+'sgmb-'+newEffect);
	});
}

SGMBWidget.prototype.changeButtonsEffect = function(newEffect)
{
	jQuery( '.sgmbWidget'+this.id+'-'+this.widgetCounter+' .jssocials-share' ).unbind('mouseenter mouseleave').hover(function() {
		jQuery(this).addClass('sgmb-animated '+'sgmb-'+newEffect).one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend',function(){
			jQuery(this).removeClass('sgmb-animated '+'sgmb-'+newEffect);
		});
	});
}

SGMBWidget.prototype.changeIconsEffect = function(newEffect)
{
	jQuery( '.sgmbWidget'+this.id+'-'+this.widgetCounter+' .jssocials-share-logo' ).unbind('mouseenter mouseleave').hover(function() {
		jQuery( this ).addClass('sgmb-animated '+'sgmb-'+newEffect).one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend',function(){
			jQuery(this).removeClass('sgmb-animated '+'sgmb-'+newEffect);
		});
	});
}

SGMBWidget.prototype.setFbLikeLayout = function(fbLikeLayout)
{
	this.fbLikeDataLayout = fbLikeLayout;
	jQuery(".fb-like").attr("data-layout", this.fbLikeDataLayout);
	this.fbLikeParse();
}

SGMBWidget.prototype.setFbLikeActionType = function(fbLikeActionType)
{
	this.fbLikeDataAction = fbLikeActionType;
	jQuery(".fb-like").attr("data-action", this.fbLikeDataAction);
	this.fbLikeParse();
}

SGMBWidget.prototype.setFbLikeUrl = function(url)
{
	jQuery(".fb-like").attr("data-href", url);
	this.fbLikeParse();
}

SGMBWidget.prototype.fbLikeParse = function()
{
	if(typeof FB !== 'undefined') {
		FB.XFBML.parse();
	}
}

SGMBWidget.prototype.jsSocial = function()
{
	if(!this.id) {
		this.id = '';
	}
	return jQuery('#sgmbShare'+this.id +'-'+this.widgetCounter).jsSocials(this.options);
}

SGMBWidget.prototype.showCenter = function(inputValue)
{
	if(inputValue == true) {
		jQuery('#sgmbShare'+this.id+'-'+this.widgetCounter).addClass('widgetShowCenter');
	}
}

SGMBWidget.prototype.changeButtonSize = function(fontSize)
{
	jQuery('#sgmbShare'+this.id +'-'+this.widgetCounter).css({'font-size' : fontSize+"px"});
}

SGMBWidget.prototype.changeBetweenButtonsSize = function(betweenButtonsSize)
{
	if (betweenButtonsSize) {
		if (!betweenButtonsSize.match('px$')) {
			betweenButtonsSize+='px';
		}
	}
	jQuery('.jssocials-share').css({'margin-right' : betweenButtonsSize});
}

SGMBWidget.prototype.changeDropdownLabelSize = function(fontSize)
{
	jQuery('.sgmbButtonListLabel'+this.id).css({'font-size' : fontSize+"px"});

}

SGMBWidget.prototype.changeToRoundButtons = function(inputValue)
{
	if(inputValue == true) {
		jQuery('#sgmbShare'+this.id+'-'+this.widgetCounter+' a').css({'border-radius': "50px",'-webkit-border-radius': '50px','-moz-border-radius': '50px','-o-border-radius': '50px'});
	}
	else {
		jQuery('#sgmbShare'+this.id+'-'+this.widgetCounter+' a').css({'border-radius': "0.3em"});
	}
}

SGMBWidget.prototype.showLabels = function(inputValue)
{
	if(inputValue == false) {
		this.options.showLabel = false;
	}
	else {
		this.options.showLabel = true;
	}
	this.jsSocial();
}



SGMBWidget.prototype.showButtonsAsList = function(inputValue)
{
	if(inputValue == true) {
		jQuery(".sgmbWidget"+this.id+'-'+this.widgetCounter).appendTo( ".dropdownPanel"+this.id+'-'+this.widgetCounter );
		jQuery('.dropdownWrapper'+this.id).show();
		jQuery('.sgmb-dropdown-advance-options').show();
	}
	else {
		jQuery(".sgmbWidget"+this.id+'-'+this.widgetCounter).appendTo( ".sgmbLivePreview" );
		jQuery('.dropdownWrapper'+this.id).hide();
		jQuery('.sgmb-dropdown-advance-options').hide();
	}
}

SGMBWidget.prototype.showCounts = function(inputValue)
{
	if(inputValue == false) {
		this.options.showCount = false;
	}
	else {
		this.options.showCount = true;
	}
	this.jsSocial();
}

SGMBWidget.prototype.changeButtonText = function(buttonText, buttonName)
{
	var socialArray = this.options.shares;
	var nameIndex = '';
	for(index in socialArray) {
		if(socialArray[index] == buttonName && typeof(socialArray[index]) == 'string') {
			nameIndex = index;
		}
		else if(socialArray[index]['share'] == buttonName) {
			nameIndex = index;
		}
	}
	this.options.shares[nameIndex]['label'] =  buttonText;
	this.jsSocial();
}

SGMBWidget.prototype.changeLogo = function(logo)
{
	for(index in this.options.shares) {
		this.options.shares[index]["logo"] = SGMB_URL+"/img/"+ logo+"-"+this.options.shares[index]['share'] +".png" ;
	}
	this.jsSocial();
}

SGMBWidget.prototype.socialButtonsHide = function(socialButtonName)
{
	var sharesLength = this.options.shares.length;
	var that = this;
	var elementIndex = this.options.shares.indexOf(socialButtonName);
	if(elementIndex == -1) {
		for(var i=0; i< sharesLength; i++) {
			if(typeof that.options.shares[i] !== 'string' && that.options.shares[i].share == socialButtonName) {
				elementIndex = i;
			}
		}
	}
	this.options.shares.splice(elementIndex,1);
	this.jsSocial();
}
