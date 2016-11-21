(function($) {

	'use strict';

	$(document).ready(function () {
		bookYourTravelOptionsFramework.init();
	});
	
	var bookYourTravelOptionsFramework = {
	
		init: function () {	
		
			$('.input-label-for-dynamic-id').each(function( index, element ){
				bookYourTravelOptionsFramework.bindLabelForDynamicIdField($(this));
			});
					
			bookYourTravelOptionsFramework.bindDynamicIdField($('.input-dynamic-id'));
			bookYourTravelOptionsFramework.bindModifyDynamicIdCheckbox($('.modify-dynamic-element-id'));
			bookYourTravelOptionsFramework.bindRemoveIcons();
			
			bookYourTravelOptionsFramework.initializeOptionsTab('accommodations', 'enable_accommodations');
			bookYourTravelOptionsFramework.initializeOptionsTab('tours', 'enable_tours');
			bookYourTravelOptionsFramework.initializeOptionsTab('carrentals', 'enable_car_rentals');
			bookYourTravelOptionsFramework.initializeOptionsTab('cruises', 'enable_cruises');
			bookYourTravelOptionsFramework.initializeOptionsTab('reviews', 'enable_reviews');

			$('.synchronise_reviews').on('click', function(e) {
				
				var $parentDiv = $(this).parent(),
					$loadingDiv = $parentDiv.find('.loading');
					
				$loadingDiv.show();
					
				var dataObj = {
					'action':'sync_reviews_ajax_request',
					'nonce' : $('#_wpnonce').val() 
				};

				$.ajax({
					url: window.adminAjaxUrl,
					data: dataObj,
					success:function(json) {
						$loadingDiv.hide();
					},
					error: function(errorThrown) {
						
					}
				}); 
				
				e.preventDefault();
			});
			
			$('.upgrade_bookyourtravel_db').on('click', function(e) {
				
				var $parentDiv = $(this).parent(),
					$loadingDiv = $parentDiv.find('.loading');
					
				$loadingDiv.show();
					
				var dataObj = {
					'action':'upgrade_bookyourtravel_db_ajax_request',
					'nonce' : $('#_wpnonce').val() 
				};				  

				$.ajax({
					url: window.adminAjaxUrl,
					data: dataObj,
					success:function(json) {
						// This outputs the result of the ajax request
						$loadingDiv.hide();
						window.location = window.adminSiteUrl;
					},
					error: function(errorThrown) {
						console.log(errorThrown);
					}
				}); 
				
				e.preventDefault();
			});

			$('.of-repeat-review-fields').sortable({
			
				update: function(event, ui) {
					
					var $fieldLoop = $(this).closest('.section').find('.of-repeat-review-fields');	

					$fieldLoop.find('.of-repeat-group').each(function (index, element) {

						var $inputFieldId = $(this).find('input.input-field-id'),
							$inputFieldLabel = $(this).find('input.input-field-label'),
							$labelFieldTab = $(this).find('label.label-field-tab'),
							$inputFieldPostType = $(this).find('input.input-field-post-type'),
							$labelFieldHide = $(this).find('label.label-field-hide'),
							$labelFieldModify = $(this).find('label.label-field-modify'),
							$checkboxFieldHide = $(this).find('input.checkbox-field-hide'),
							$checkboxFieldModify = $(this).find('input.checkbox-field-modify'),
							$inputFieldIndex = $(this).find('input.input-index');
						
						$inputFieldId.attr('name', $inputFieldId.attr('data-rel') + '[' + index + '][id]');
						$inputFieldLabel.attr('name', $inputFieldLabel.attr('data-rel') + '[' + index + '][label]');
						$inputFieldPostType.attr('name', $inputFieldPostType.attr('data-rel') + '[' + index + '][post_type]'); 
						$checkboxFieldHide.attr('name', $checkboxFieldHide.attr('data-rel') + '[' + index + '][hide]'); 
						$labelFieldHide.attr('for', $checkboxFieldHide.attr('data-rel') + '[' + index + '][hide]');
						$checkboxFieldModify.attr('name', $checkboxFieldModify.attr('data-rel') + '[' + index + '][modify]'); 
						$labelFieldModify.attr('for', $checkboxFieldModify.attr('data-rel') + '[' + index + '][modify]');				
						$inputFieldIndex.attr('name', $inputFieldIndex.attr('data-rel') + '[' + index + '][index]');
						$inputFieldIndex.val(index);
					});				
				}
			});
	 
			$('.docopy_review_field').on('click', function(e) {
	 
				var $section = $(this).closest('.section'),
					$loop = $section.find('.of-repeat-review-fields'),
					$toCopy = $loop.find('.of-repeat-group:last'),
					$newGroup = $toCopy.clone(),
					maxFieldIndex = parseInt($section.find('.max_field_index').val(), 10) + 1;

				$newGroup.insertAfter($toCopy);
				
				$section.find('.max_field_index').val(maxFieldIndex);
				$newGroup.find('input.input-index').val(maxFieldIndex);

				bookYourTravelOptionsFramework.initializeCustomField('.input-field-label',  'label', 	$newGroup, maxFieldIndex, '');
				bookYourTravelOptionsFramework.initializeCustomField('.input-field-id', 	  'id', 	$newGroup, maxFieldIndex, '');
				bookYourTravelOptionsFramework.initializeCustomField('.input-index',  'index', 	$newGroup, maxFieldIndex, '');
				bookYourTravelOptionsFramework.initializeCustomField('.input-field-post-type',  'post_type', 	$newGroup, maxFieldIndex, '');
				bookYourTravelOptionsFramework.initializeCustomField('.checkbox-field-hide','hide', 	$newGroup, maxFieldIndex, 'label.label-field-hide');
				bookYourTravelOptionsFramework.initializeCustomField('.checkbox-field-modify','modify', 	$newGroup, maxFieldIndex, 'label.label-field-modify');
				
				$newGroup.append($('<span class="ui-icon ui-icon-close"></span>'));
				bookYourTravelOptionsFramework.bindRemoveIcons();
				bookYourTravelOptionsFramework.bindLabelForDynamicIdField($newGroup.find('input.input-field-label'));
				bookYourTravelOptionsFramework.bindDynamicIdField($newGroup.find('input.input-field-id'));
				bookYourTravelOptionsFramework.bindModifyDynamicIdCheckbox($newGroup.find('input.modify-dynamic-element-id'));

				$newGroup.find('.input-field-id').val('review_');
				
				e.preventDefault();	 
			});
		
			$('.of-repeat-extra-fields').sortable({
			
				update: function(event, ui) {
					
					var $fieldLoop = $(this).closest('.section').find('.of-repeat-extra-fields');	

					$fieldLoop.find('.of-repeat-group').each(function (index, element) {

						var $inputFieldId = $(this).find('input.input-field-id'),
							$inputFieldLabel = $(this).find('input.input-field-label'),
							$labelFieldType = $(this).find('label.label-field-type'),
							$labelFieldTab = $(this).find('label.label-field-tab'),
							$selectFieldType = $(this).find('select.select-field-type'),
							$selectFieldTab = $(this).find('select.select-field-tab'),							
							$labelFieldHide = $(this).find('label.label-field-hide'),
							$labelFieldModify = $(this).find('label.label-field-modify'),
							$checkboxFieldHide = $(this).find('input.checkbox-field-hide'),
							$checkboxFieldModify = $(this).find('input.checkbox-field-modify'),
							$inputFieldIndex = $(this).find('input.input-index');
						
						$inputFieldId.attr('name', $inputFieldId.attr('data-rel') + '[' + index + '][id]');
						$inputFieldLabel.attr('name', $inputFieldLabel.attr('data-rel') + '[' + index + '][label]');
						$selectFieldType.attr('name', $selectFieldType.attr('data-rel') + '[' + index + '][type]'); 
						$labelFieldType.attr('for', $selectFieldType.attr('data-rel') + '[' + index + '][type]');
						$selectFieldTab.attr('name', $selectFieldTab.attr('data-rel') + '[' + index + '][tab_id]'); 
						$labelFieldTab.attr('for', $selectFieldTab.attr('data-rel') + '[' + index + '][tab_id]');
						$checkboxFieldHide.attr('name', $checkboxFieldHide.attr('data-rel') + '[' + index + '][hide]'); 
						$labelFieldHide.attr('for', $checkboxFieldHide.attr('data-rel') + '[' + index + '][hide]');
						$checkboxFieldModify.attr('name', $checkboxFieldModify.attr('data-rel') + '[' + index + '][modify]'); 
						$labelFieldModify.attr('for', $checkboxFieldModify.attr('data-rel') + '[' + index + '][modify]');				
						$inputFieldIndex.attr('name', $inputFieldIndex.attr('data-rel') + '[' + index + '][index]');
						$inputFieldIndex.val(index);
					});					
				}
			});
	 
			$('.docopy_field').on('click', function(e) {
	 
				var $section = $(this).closest('.section'),
					$loop = $section.find('.of-repeat-extra-fields'),
					$toCopy = $loop.find('.of-repeat-group:last'),
					$newGroup = $toCopy.clone(),
					maxFieldIndex = parseInt($section.find('.max_field_index').val(), 10) + 1;

				$newGroup.insertAfter($toCopy);
				
				$section.find('.max_field_index').val(maxFieldIndex);
				$newGroup.find('input.input-index').val(maxFieldIndex);

				bookYourTravelOptionsFramework.initializeCustomField('.input-field-label',  'label', 	$newGroup, maxFieldIndex, '');
				bookYourTravelOptionsFramework.initializeCustomField('.input-field-id', 	  'id', 	$newGroup, maxFieldIndex, '');
				bookYourTravelOptionsFramework.initializeCustomField('.input-index',  'index', 	$newGroup, maxFieldIndex, '');
				bookYourTravelOptionsFramework.initializeCustomField('.select-field-type',  'type', 	$newGroup, maxFieldIndex, '');
				bookYourTravelOptionsFramework.initializeCustomField('.select-field-tab',  'tab_id', 	$newGroup, maxFieldIndex, '');
				bookYourTravelOptionsFramework.initializeCustomField('.checkbox-field-hide','hide', 	$newGroup, maxFieldIndex, 'label.label-field-hide');
				bookYourTravelOptionsFramework.initializeCustomField('.checkbox-field-modify','modify', 	$newGroup, maxFieldIndex, 'label.label-field-modify');
				
				$newGroup.append($('<span class="ui-icon ui-icon-close"></span>'));
				bookYourTravelOptionsFramework.bindRemoveIcons();
				bookYourTravelOptionsFramework.bindLabelForDynamicIdField($newGroup.find('input.input-field-label'));
				bookYourTravelOptionsFramework.bindDynamicIdField($newGroup.find('input.input-field-id'));
				bookYourTravelOptionsFramework.bindModifyDynamicIdCheckbox($newGroup.find('input.modify-dynamic-element-id'));
				
				e.preventDefault();	 
			});
				 
			$('.docopy_form_field').on('click', function(e) {
	 
				var $section = $(this).closest('.section'),
					$loop = $section.find('.of-repeat-form-fields'),
					$toCopy = $loop.find('.of-repeat-group:last'),
					$newGroup = $toCopy.clone(),
					maxFieldIndex = parseInt($section.find('.max_field_index').val(), 10) + 1;

				$newGroup.insertAfter($toCopy);
				
				$section.find('.max_field_index').val(maxFieldIndex);
				$newGroup.find('input.input-index').val(maxFieldIndex);

				bookYourTravelOptionsFramework.initializeCustomField('.input-field-label',  'label', 	$newGroup, maxFieldIndex, '');
				bookYourTravelOptionsFramework.initializeCustomField('.input-field-id', 	  'id', 	$newGroup, maxFieldIndex, '');
				bookYourTravelOptionsFramework.initializeCustomField('.input-index',  'index', 	$newGroup, maxFieldIndex, '');
				bookYourTravelOptionsFramework.initializeCustomField('.select-field-type',  'type', 	$newGroup, maxFieldIndex, '');
				bookYourTravelOptionsFramework.initializeCustomField('.checkbox-field-hide','hide', 	$newGroup, maxFieldIndex, 'label.label-field-hide');
				bookYourTravelOptionsFramework.initializeCustomField('.checkbox-field-modify','modify', 	$newGroup, maxFieldIndex, 'label.label-field-modify');
				bookYourTravelOptionsFramework.initializeCustomField('.checkbox-field-required','required', 	$newGroup, maxFieldIndex, 'label.label-field-required');
				
				$newGroup.append($('<span class="ui-icon ui-icon-close"></span>'));
				bookYourTravelOptionsFramework.bindRemoveIcons();
				bookYourTravelOptionsFramework.bindLabelForDynamicIdField($newGroup.find('input.input-field-label'));
				bookYourTravelOptionsFramework.bindDynamicIdField($newGroup.find('input.input-field-id'));
				bookYourTravelOptionsFramework.bindModifyDynamicIdCheckbox($newGroup.find('input.modify-dynamic-element-id'));
				
				e.preventDefault();	 
			});
			
			$('.of-repeat-tabs').sortable({
			
				update: function(event, ui) {
					
					var $tabLoop = $(this).closest('.section').find('.of-repeat-tabs');	
					
					$tabLoop.find('.of-repeat-group').each(function (index, element) {

						var $inputTabId = $(this).find('input.input-tab-id'),
							$inputTabLabel = $(this).find('input.input-tab-label'),
							$checkboxTabHide = $(this).find('input.checkbox-tab-hide'),
							$labelTabHide = $(this).find('label.label-tab-hide'),
							$checkboxTabModify = $(this).find('input.checkbox-tab-modify'),
							$labelTabModify = $(this).find('label.label-tab-modify'),
							$inputTabIndex = $(this).find('input.input-index');

						$inputTabId.attr('name', $inputTabId.attr('data-rel') + '[' + ( index ) + '][id]');
						$inputTabLabel.attr('name', $inputTabLabel.attr('data-rel') + '[' + index + '][label]'); 
						$checkboxTabHide.attr('name', $checkboxTabHide.attr('data-rel') + '[' + index + '][hide]'); 
						$labelTabHide.attr('for', $checkboxTabHide.attr('data-rel') + '[' + index + '][hide]');
						$checkboxTabModify.attr('name', $checkboxTabModify.attr('data-rel') + '[' + index + '][modify]'); 
						$labelTabModify.attr('for', $checkboxTabModify.attr('data-rel') + '[' + index + '][modify]');
						$inputTabIndex.attr('name', $inputTabIndex.attr('data-rel') + '[' + index + '][index]');
						$inputTabIndex.val(index);
					});
				}
			});
	 
			$('.docopy_tab').on('click', function(e) {
	 
				var $section = $(this).closest('.section'),
					$loop = $section.find('.of-repeat-tabs'),
					$toCopy = $loop.find('.of-repeat-group:last'),
					$newGroup = $toCopy.clone();

				$newGroup.insertAfter($toCopy);

				var maxTabIndex = parseInt($section.find('.max_tab_index').val(), 10) + 1;
				$section.find('.max_tab_index').val(maxTabIndex);
				$newGroup.find('input.input-index').val(maxTabIndex);

				bookYourTravelOptionsFramework.initializeCustomField('.input-tab-label',  'label', 	$newGroup, maxTabIndex, '');
				bookYourTravelOptionsFramework.initializeCustomField('.input-tab-id', 	  'id', 	$newGroup, maxTabIndex, '');
				bookYourTravelOptionsFramework.initializeCustomField('.input-index',  'index', 	$newGroup, maxTabIndex, '');
				bookYourTravelOptionsFramework.initializeCustomField('.checkbox-tab-hide','hide', 	$newGroup, maxTabIndex, 'label.label-tab-hide');
				
				$newGroup.append($('<span class="ui-icon ui-icon-close"></span>'));
				bookYourTravelOptionsFramework.bindRemoveIcons();
				bookYourTravelOptionsFramework.bindLabelForDynamicIdField($newGroup.find('input.input-tab-label'));
				bookYourTravelOptionsFramework.bindDynamicIdField($newGroup.find('input.input-tab-id'));
				bookYourTravelOptionsFramework.bindModifyDynamicIdCheckbox($newGroup.find('input.modify-dynamic-element-id'));
				
				e.preventDefault();
			});
		
		},
		bindModifyDynamicIdCheckbox : function ($checkboxInput) {
			
			$checkboxInput.on('click', function(e) {
			
				var $idInput = $(this).parent().prev('.of-input-wrap').find("input[type=text].input-dynamic-id");

				if ($idInput.is('[readonly]')) {
					$idInput.prop('readonly', false);
				} else {
					$idInput.prop('readonly', true);
				}
			});
			
		},
		bindDynamicIdField : function($inputDynamicId) {
		
			$inputDynamicId.on('blur', function(e) {
			
				if (!$(this).is('[readonly]')) {
				
					var $this = $(this),					
						$parentDiv = $(this).parent(),
						$loadingDiv = $parentDiv.find('.loading'),
						elementType= '',
						elementNewId = $(this).val(),
						elementId = $(this).data('id'),
						elementOriginalId = $(this).data('original-id'),
						elementIsDefault = $(this).data('is-default');
					
					if (elementNewId !== elementOriginalId && elementNewId != elementId && !elementIsDefault) {
					
						if ($this.hasClass('input-tab-id'))
							elementType = 'tab';
						else if ($this.hasClass('input-review-field-id'))
							elementType = 'review_field';
						else if ($this.hasClass('input-inquiry-form-field-id'))
							elementType = 'inquiry_form_field';
						else if ($this.hasClass('input-booking-form-field-id'))
							elementType = 'booking_form_field';
						else if ($this.hasClass('input-field-id'))
							elementType = 'field';
		
						$loadingDiv.show();
			
						var newId = bookYourTravelOptionsFramework.getUniqueDynamicElementId(elementNewId, elementType, $this.data('parent'));
			
						$this.val(newId);
						$this.data('id', newId);
			
						$loadingDiv.hide();			
					}
				}
			});
		},
		bindLabelForDynamicIdField : function($inputElement) {

			var elementOriginalId = $inputElement.data('original-id');
			
			$inputElement.on('blur', function(e) {

				var val = $inputElement.val(),
					$parentDiv = $inputElement.parent(),
					$loadingDiv = $parentDiv.find('.loading'),
					$idInput = $parentDiv.find('.input-dynamic-id'),
					elementType = '',
					elementNewId = bookYourTravelOptionsFramework.cleanUpId(val),
					elementIsDefault = $(this).data('is-default');
					
				if ( !elementIsDefault && (elementOriginalId === null || typeof(elementOriginalId) === 'undefined' || elementOriginalId != elementNewId )) {
				
					$loadingDiv.show();

					if ($idInput.hasClass('input-tab-id'))
						elementType = 'tab';
					else if ($idInput.hasClass('input-review-field-id'))
						elementType = 'review_field';
					else if ($idInput.hasClass('input-inquiry-form-field-id'))
						elementType = 'inquiry_form_field';
					else if ($idInput.hasClass('input-booking-form-field-id'))
						elementType = 'booking_form_field';
					else if ($idInput.hasClass('input-field-id'))
						elementType = 'field';
					
					var newId = bookYourTravelOptionsFramework.getUniqueDynamicElementId(elementNewId, elementType, $idInput.data('parent'));
				
					$idInput.val(newId);
					$idInput.data('id', newId);
					$loadingDiv.hide();		
				}
			});
			
			if (elementOriginalId === null || typeof(elementOriginalId) === 'undefined') {
		
				$inputElement.on('keyup', function(e) {

					if ( e.which == 13 ) {
						// Enter key pressed
						e.preventDefault();
					} else {
						
						var val = $inputElement.val(),
							$parentDiv = $inputElement.parent(),
							$idInput = $parentDiv.find('.input-dynamic-id');
						
						var slug = bookYourTravelOptionsFramework.cleanUpId(val);
						
						$idInput.val(slug);
					}
				});

			}		
		},
		getUniqueDynamicElementId : function(elementNewId, elementType, parent) {

			var newId = '';
		
			var dataObj = {
				'action' 		: 'generate_unique_dynamic_element_id',
				'element_id'	: elementNewId,
				'nonce' 		: $('#_wpnonce').val(),
				'element_type' 	: elementType,
				'parent'		: parent
			};

			$.ajax({
				url: window.adminAjaxUrl,
				data: dataObj,
				async: false,
				success:function(data) {
					newId = JSON.parse(data);
				},
				error: function(errorThrown) {
					
				}
			});

			return newId;
		},
		cleanUpId : function(str) {
			return str.replace(/-/g, '_')
					.replace(/ /g, '_')
					.replace(/:/g, '_')
					.replace(/\\/g, '_')
					.replace(/\//g, '_')
					.replace(/[^a-zA-Z0-9\_]+/g, '')
					.replace(/-{2,}/g, '_')
					.toLowerCase();
		},
		bindRemoveIcons : function() {
		
			$('.ui-icon-close').unbind( "click" );
			$('.ui-icon-close').on('click', function() {
				$(this).parent().remove();
				return false;
			});
		},
		initializeOptionsTab : function(groupClass, checkboxId) {
		
			bookYourTravelOptionsFramework.toggleTabVisibility($('#' + checkboxId).is(':checked'), groupClass, checkboxId);
			
			$('#' + checkboxId).change(function() {
				bookYourTravelOptionsFramework.toggleTabVisibility(this.checked, groupClass, checkboxId);
			});
		},
		toggleTabVisibility : function(show, groupClass, checkboxId) {

			var formFieldsFilter = '';
			if (groupClass == 'accommodations' || groupClass == 'tours' || groupClass == 'cruises') {
				formFieldsFilter = groupClass.slice(0, -1);
			} else if (groupClass == 'carrentals') {
				formFieldsFilter = 'car_rental';
			}
		
			if (show) {
				$('.' + groupClass + '-tab').show();
				$('.' + groupClass + '_controls').show();
			} else {
				$('.' + groupClass + '-tab').hide();
				$('.' + groupClass + '_controls').hide();
			}		
		},
		initializeCustomField : function(fieldSelector, fieldKey, $groupObj, fieldIndex, labelSelector) {
		
			var $fieldControl = $groupObj.find(fieldSelector);

			$fieldControl.attr('name', $fieldControl.attr('data-rel') + '[' + fieldIndex + '][' + fieldKey + ']');
			$fieldControl.attr('id', $fieldControl.attr('data-rel') + '[' + fieldIndex + '][' + fieldKey + ']');
			
			var fieldType = $fieldControl[0].type || $fieldControl[0].tagName.toLowerCase();
			if (fieldType == 'text') {
				$fieldControl.val('');
			}
			
			if ($fieldControl.attr('data-original-id')) {
				$fieldControl.removeAttr('data-original-id');
			}
		  
			if (labelSelector.length > 0) {
				$groupObj.find(labelSelector).attr('for', $fieldControl.attr('data-rel') + '[' + fieldIndex + '][' + fieldKey + ']');
			}
		},
	};
	
})(jQuery);