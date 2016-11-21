var $ = jQuery;

function SGMBButtonPanel()
{
	this.selButtons = [];
}

SGMBButtonPanel.prototype.getSelButtons = function()
{
	return this.selButtons;
}

SGMBButtonPanel.prototype.setButtonAsSelected  = function(id) 
{	
	if(id) {
		var $ = jQuery, that = this; 
		var $item = $("#"+id),
			$trash = $( "#trash" );
		var $list = $( "ul", $trash ).length ?
			$( "ul", $trash ) :
			$( "<ul class='gallery ui-helper-reset'/>" ).appendTo( $trash );

		$item.find( "a.ui-icon-trash" ).remove();
		$item.appendTo( $list );
		
		that.addButtonInSelectList(id);
		sgmb.showAdvancedOption(id);
	}
}

SGMBButtonPanel.prototype.addButtonInSelectList = function(id) 
{
	this.selButtons.push(id);
	$('.select-button').val(this.selButtons.join(','));
}

SGMBButtonPanel.prototype.deleteButtonInSelectList = function(id) 
{
	var key = this.selButtons.indexOf(id);
	this.selButtons.splice(key,1);
	$('.select-button').val(this.selButtons.join(','));
}

SGMBButtonPanel.prototype.dragAndDrop = function(sgmbIsPro) 
{

	var $gallery = $( "#gallery" ),
		$trash = $( "#trash" );
	var that = this;
	$($( "li", $gallery )).each(function(index, element) {
		if(sgmbIsPro != 1) {
			if(element.id != 'fbLike' && element.id != 'twitterFollow' && element.id != 'whatsapp' && element.id != 'tumblr' && element.id != 'reddit' && element.id != 'line' && element.id != 'vk' && element.id != 'stumbleupon') {
				$( element ).draggable({
					cancel: "a.ui-icon", // clicking an icon won't initiate dragging
					revert: "invalid", // when not dropped, the item will revert back to its initial position
					containment: "document",
					helper: "clone",
					cursor: "move"
				});
			}
		}
		else {
			$( element ).draggable({
				cancel: "a.ui-icon", 
				revert: "invalid", 
				containment: "document",
				helper: "clone",
				cursor: "move"
			});
		}
	});
	// let the trash be droppable, accepting the gallery items
	$trash.droppable({
	  accept: "#gallery > li",
	  activeClass: "ui-state-highlight",
	  drop: function( event, ui ) {

		deleteImage( ui.draggable ); 
		that.addButtonInSelectList(ui.draggable.attr('id'));
		ui.draggable.trigger('optionsFadeIn');
		ui.draggable.trigger('socialButtonShow');
		ui.draggable.trigger('dragComplete');
		}
	});
 
	// let the gallery be droppable as well, accepting items from the trash
	$gallery.droppable({
		accept: "#trash li",
		activeClass: "custom-state-active",
	  drop: function( event, ui ) {
		recycleImage( ui.draggable );
		that.deleteButtonInSelectList(ui.draggable.attr('id'));
		ui.draggable.trigger('optionsFadeOut'); 
		ui.draggable.trigger('socialButtonHide');
		ui.draggable.trigger('dragComplete');
	  }
	});
 
	// image deletion function
	//var recycle_icon = "<a href='link/to/recycle/script/when/we/have/js/off' title='Recycle this image' class='ui-icon ui-icon-refresh'>Recycle image</a>";
	function deleteImage( $item ) {

	  $item.fadeOut(function() {
		var $list = $( "ul", $trash ).length ?
		  $( "ul", $trash ) :
		  $( "<ul class='gallery ui-helper-reset'/>" ).appendTo( $trash );
 
		$item.find( "a.ui-icon-trash" ).remove();
		$item/*.append( recycle_icon )*/.appendTo( $list ).fadeIn(function() {
		  $item.find( "img" )
			  
		});
	  });
	}
 
	// image recycle function
	var trash_icon = "<a href='link/to/trash/script/when/we/have/js/off' title='Delete this image' class='ui-icon ui-icon-trash'>Delete image</a>";
	function recycleImage( $item ) {
	  $item.fadeOut(function() {
		$item
		  .find( "a.ui-icon-refresh" )
			.remove()
		  .end()
		  .find( "img" )
		  .end()
		  .appendTo( $gallery )
		  .fadeIn();
	  });
	}
	
	// resolve the icons behavior with event delegation
	$( "ul.gallery > li" ).click(function( event ) {
	  var $item = $( this ),
		$target = $( event.target );
		if ( $target.is( "a.ui-icon-trash" ) ) {
			deleteImage( $item );
		} 
		else if ( $target.is( "a.ui-icon-zoomin" ) ) {
			viewLargerImage( $target );
		} 
		else if ( $target.is( "a.ui-icon-refresh" ) ) {
			recycleImage( $item );
		}
		return false;
	});
}
