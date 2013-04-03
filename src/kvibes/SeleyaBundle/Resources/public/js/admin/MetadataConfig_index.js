$(function() {
	$('#metadata-sortable').sortable({
		handle: '.sort-handle',
	    update: function(event, ui) {
	    	$('#metadata-sortable-success').stop().hide();
	    	$('#metadata-sortable-error').stop().hide();
	        $('#metadata-sortable-loading').show();
	    	$('#metadata-sortable-loading-spin').spin('tiny');
	    	var sorted = JSON.stringify($(this).sortable('toArray', {attribute: 'data-id'}));
	    	$.post(Routing.generate('admin_metadataConfig_order'), {
		    	order: sorted
		    })
		    .done(function(data) {
		        $('#metadata-sortable-success').fadeIn().delay(3000).fadeOut();
		    }) 
		    .fail(function() {
		        $('#metadata-sortable-error').fadeIn().delay(3000).fadeOut();
			})
			.always(function() {
		        $('#metadata-sortable-loading').hide();
			});
	    }
	});
  	$('#metadata-sortable').disableSelection();
});
