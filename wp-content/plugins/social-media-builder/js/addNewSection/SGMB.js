var jQuery = jQuery;

function SGMB() {
	this.buttons = ['facebook', 'googleplus', 'twitter', 'email', 'linkedin', 'pinterest', 'mewe', 'twitterFollow', 'fbLike', 'whatsapp', 'tumblr', 'reddit', 'line', 'vk', 'stumbleupon'];
	this.theme = {
		"classic" : {
			"socialTheme":"classic",
			"icons":"default"
		},
		"cloud" : {
			"socialTheme":"minima",
			"icons":"cloud"
		},
		"toy" : {
			"socialTheme":"minima",
			"icons":"toy"
		},
		"wood" : {
			"socialTheme":"plain",
			"icons":"wood"
		},
		"box" : {
			"socialTheme":"plain",
			"icons":"box"
		},
		"round" : {
			"socialTheme":"minima",
			"icons":"round"
		}
	};
	this.livePreview = new SGMBLivePreview();
	this.buttonPanel = new SGMBButtonPanel();
}

SGMB.prototype.init = function(data, sgmbIsPro)
{
	this.initTabs();
	this.initDragAndDrop(sgmbIsPro);
	this.initAccordion();
	this.livePreview.init();
	this.setButtonInArray(data);
	this.initButtonOptions();
}

SGMB.prototype.getLivePreview = function()
{
	return this.livePreview;
}

SGMB.prototype.setButtonInArray = function(data)
{
	for (var buttonName in data.button) {
		var button = data.button[buttonName];
		this.buttonPanel.setButtonAsSelected(button);
	}
}

SGMB.prototype.initAccordion = function()
{
	var that = this;
	jQuery( "#accordion" ).accordion({
		heightStyle: "content"
	});
	jQuery.each( that.buttons, function( key, value ) {
		jQuery('.'+ value).hide();
	});
}

SGMB.prototype.initButtonOptions = function()
{
	var that = this;
	jQuery('.ui-widget-content').on('optionsFadeIn', function(e){
		var id = jQuery(this).attr('id');
		that.showAdvancedOption(id);
	});

	jQuery('.ui-widget-content').on('optionsFadeOut', function(e, param){
		var id = jQuery(this).attr('id');
		that.hideAdvancedOption(id);
		var selButtons = that.buttonPanel.getSelButtons();
		if(selButtons.length != 0) {
			if(selButtons[0] == id) {
				that.showAdvancedOption(selButtons[1]);
			}
			else {
				that.showAdvancedOption(selButtons[0]);
			}
		}
	});
}

SGMB.prototype.initTabs = function()
{
	jQuery( "#tabs" ).tabs().addClass( "ui-tabs-vertical ui-helper-clearfix" );
	jQuery( "#tabs li" ).removeClass( "ui-corner-top" ).addClass( "ui-corner-left" );
}

SGMB.prototype.initDragAndDrop = function(sgmbIsPro)
{
	this.buttonPanel.dragAndDrop(sgmbIsPro);
}

SGMB.prototype.showAdvancedOption = function(id)
{
	jQuery('.'+id).show();
	jQuery( "#accordion" ).accordion("refresh");
	jQuery('.'+id).click();
}

SGMB.prototype.hideAdvancedOption = function(id)
{
	jQuery('.'+id).hide();
}
