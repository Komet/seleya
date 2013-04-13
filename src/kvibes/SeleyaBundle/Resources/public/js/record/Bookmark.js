$(function() {
	if (!isLoggedIn) {
		$('#bookmark').attr('title', textBookmarkNotLoggedIn);
	} else if (hasBookmark) {
		$('#bookmark').attr('title', textBookmarkSet);
	} else {
		$('#bookmark').attr('title', textBookmarkUnset);
	}
	
	$('#bookmark').tooltip();
	
	if (isLoggedIn) {
		$('#bookmark').click(function() {
			$('#bookmark').spin('tiny-black');
			var recordId = $(this).data('record-id');
			var bookmark = $(this);
	    	$.post(Routing.generate('bookmark_toggle', {id: recordId}))
		    .done(function(data) {
		    	if (data.bookmark) {
		    		bookmark.addClass('active');
		    		$('#bookmark').attr('title', textBookmarkSet);
		    	} else {
		    		bookmark.removeClass('active');
		    		$('#bookmark').attr('title', textBookmarkUnset);
		    	}
		    	bookmark.tooltip('destroy');
		    	bookmark.tooltip();
		    }) 
			.always(function() {
				$('#bookmark').spin(false);
			});
			return false;
		});	
	}
});