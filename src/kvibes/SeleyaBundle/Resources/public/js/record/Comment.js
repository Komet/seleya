$(function() {
	var addedComments = new Array();
	
	if (isLoggedIn) {
		$('#comment-form').submit(function() {
			var textarea = $('#textarea-comment');
			var submitButton = $(this).find('button[type="submit"]');
			var comment = textarea.val();
			var spinner = $(this).find('span.spin');
			var recordId = $(this).data('record-id');
			
			if (comment == '') {
				textarea.parent().parent().addClass('error');
				return false;
			}
			textarea.parent().parent().removeClass('error');
			textarea.attr('readonly', 'readonly');
			submitButton.attr('disabled', 'disabled');
			spinner.show();
			spinner.spin('tiny');
			
	    	$.post(Routing.generate('comment_add', {recordId: recordId}), {
	    		comment: comment
	    	})
		    .done(function(data) {
		    	textarea.val('');
		    	addedComments.push(data.commentId);
		    	$('ul.comments').append(data.html);
		    	$('ul.comments li:last').addClass('highlight')
		    						    .delay(1000)
		    						    .removeClass('highlight', 1000);
		    })
			.always(function() {
				spinner.hide();
				submitButton.removeAttr('disabled');
				textarea.removeAttr('readonly');
			});			
			
			return false;
		});	
	}
	
	var nextCommentsPage = 1;
	$('#loadOlderComments').click(function() {
		var button = $(this);
		var recordId = $(this).data('record-id');
		var spinner = $(this).find('span.spin');
		spinner.spin('tiny-black');
		spinner.show();
		$(this).attr('disabled', 'disabled');
		$.post(Routing.generate('comments', {recordId: recordId, page: nextCommentsPage}), {
			commentsToExclude: addedComments
		})
		.done(function(data) {
			for (var i=0, n=data.html.length ; i<n ; ++i) {
				$('ul.comments').prepend(data.html[i]);
			}
			nextCommentsPage++;
			spinner.hide();
			button.removeAttr('disabled');
			if (!data.hasMoreComments) {
				$('#loadOlderComments').hide();
			}
		});
		return false;
	});
});