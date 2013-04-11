$(function() {
	$('#loadMoreCourses').click(function() {
		var link = $(this);
		var facultyId = $(this).data('faculty-id');
		var currentPage = $(this).data('current-page');
		var spinner = $(this).find('span.spin');
		spinner.spin('tiny-black');
		spinner.show();
		link.attr('disabled', 'disabled');
		$.get(Routing.generate('faculty_courses', {
			facultyId: facultyId, 
			page: currentPage+1,
			render: 'true'
		}))
		.done(function(data) {
			for (var i=0, n=data.html.length ; i<n ; ++i) {
				$('ul.course-list').append(data.html[i]);
			}
			link.data('current-page', currentPage+1);
			spinner.hide();
			link.removeAttr('disabled');
			if (!data.hasMoreCourses) {
				link.hide();
			}
		});
		return false;
	});
});
