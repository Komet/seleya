$(function() {
	$('form.form-search').submit(function() {
		var query = $(this).find('input[type="text"]').val();
		search(query);
		return false;
	});
	
	function search(query) {
		var searchVisible = $('form.form-search').data('search-visible');
		var resultsContainer = $('#searchResults');
		var recordContainer = $('#record-list');
		var spinContainer = resultsContainer.find('div.spin-container');
		var spinner = spinContainer.find('span.spin');
		var resultsList = resultsContainer.find('ul');
		var moreResults = resultsContainer.find('div.alert-warning');
		
		recordContainer.hide();
		resultsContainer.show();
		spinContainer.show();
		moreResults.hide();
		spinner.spin('medium');
		resultsList.find('li').each(function() {
			$(this).remove();
		});
		
    	$.post(Routing.generate('admin_record_search'), {
	    	query  : query,
	    	visible: searchVisible
	    })
	    .done(function(data) {
	    	window.location.hash = encodeURIComponent(query);
	        for (var i=0, n=data.results.length ; i<n ; ++i) {
	        	resultsList.append(data.results[i]);
	        }
	        moreResults.toggle(data.moreResults);
	    }) 
		.always(function() {
	        spinContainer.hide();
		});
	}
	
	if (window.location.hash !== '') {
		var query = window.location.hash.substring(1);
		query = decodeURIComponent(query);
		$('form.form-search').find('input[type="text"]').val(query);
		search(query);
	}
});
