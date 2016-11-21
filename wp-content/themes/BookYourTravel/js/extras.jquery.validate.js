(function($) { // Hide the namespace
	/* Add validation methods if validation plugin available. */
	if ($.fn.validate) {
		jQuery.validator.addMethod("dateFormatDate", function(value, element, params){
			lastElement = element;
			var inst = $.datepicker._getInst(element);
			
			try {
				var date = $.datepicker.parseDate(window.datepickerDateFormat, value, $.datepicker._getFormatConfig(inst));
				var minDate = $.datepicker._determineDate(inst, $.datepicker._get(inst, 'minDate'), null);
				var maxDate = $.datepicker._determineDate(inst, $.datepicker._get(inst, 'maxDate'), null);
				var beforeShowDay = $.datepicker._get(inst, 'beforeShowDay');
				return this.optional(element) || !date || 
					((!minDate || date >= minDate) && (!maxDate || date <= maxDate) &&
					(!beforeShowDay || beforeShowDay.apply(element, [date])[0]));
			}
			catch (e) {
				return false;
			}
			
		}, function(params) {
			var inst = $.datepicker._getInst(lastElement);
			var minDate = $.datepicker._determineDate(inst, $.datepicker._get(inst, 'minDate'), null);
			var maxDate = $.datepicker._determineDate(inst, $.datepicker._get(inst, 'maxDate'), null);
			var messages = $.datepicker._defaults;
			return (minDate && maxDate ?
				$.datepicker.errorFormat(inst, messages.validateDateMinMax, [minDate, maxDate]) :
				(minDate ? $.datepicker.errorFormat(inst, messages.validateDateMin, [minDate]) :
				(maxDate ? $.datepicker.errorFormat(inst, messages.validateDateMax, [maxDate]) :
				messages.validateDate)));
		});
		
		/* And allow as a class rule. */
		$.validator.addClassRules('dateFormatDate', {dateFormatDate: true});
		
	}

})(jQuery);