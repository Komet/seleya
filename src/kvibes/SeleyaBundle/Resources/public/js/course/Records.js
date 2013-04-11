$(function() {
	$('#loadMoreRecords').click(function() {
		var link = $(this);
		var courseId = $(this).data('course-id');
		var sortOrder = $(this).data('sort-order');
		var sortDirection = $(this).data('sort-direction');
		var currentPage = $(this).data('current-page');
		var spinner = $(this).find('span.spin');
		spinner.spin('tiny-black');
		spinner.show();
		link.attr('disabled', 'disabled');
		$.get(Routing.generate('course_records', {
			courseId: courseId, 
			page: currentPage+1,
			sortOrder: sortOrder,
			sortDirection: sortDirection,
			render: 'true'
		}))
		.done(function(data) {
			for (var i=0, n=data.html.length ; i<n ; ++i) {
				$('ul.record-list-horizontal').append(data.html[i]);
			}
			link.data('current-page', currentPage+1);
			spinner.hide();
			link.removeAttr('disabled');
			if (!data.hasMoreRecords) {
				link.hide();
			}
		});
		return false;
	});
});