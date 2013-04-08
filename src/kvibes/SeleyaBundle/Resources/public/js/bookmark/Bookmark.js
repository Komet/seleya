$(function() {
	$('a.bookmark-remove').click(function() {
		var bookmarkId = $(this).data('bookmark-id');
		var containingLi = $(this).parent();
		$(this).html('');
		$(this).spin('tiny-black');
    	$.post(Routing.generate('bookmark_delete', {id: bookmarkId}))
	    .done(function(data) {
	    	containingLi.slideUp('normal', function() {
	    		var containingUl = $(this).parent();
	    		$(this).remove();
	    		if (containingUl.find('li').length == 0) {
	    			$('#alert-no-bookmarks').show();
	    		}
	    	});
	    });
		return false;
	});
});