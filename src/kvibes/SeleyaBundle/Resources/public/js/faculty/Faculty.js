$(function() {
	$('a.load-courses').click(function() {
		var link = $(this);
		var facultyId = link.data('faculty-id');
		var container = $('div.course-list-container[data-faculty-id="' + facultyId + '"]');
		if (container.is(':visible')) {
			container.slideUp();
			return false;
		}
		if (link.data('is-loading') == '1') {
			return false;
		}
		container.slideDown();
		if (link.data('loaded') == '1') {
			return false;
		}
		var spinner = container.find('span.spin');
		spinner.spin('small');
		link.data('is-loading', '1');
		$.get(Routing.generate('faculty_courses_lastRecord', {
			facultyId: facultyId
		}))
		.done(function(data) {
			for (var i=0, n=data.html.length ; i<n ; ++i) {
				$('div.course-list-container[data-faculty-id="' + facultyId + '"] ul').append(data.html[i]);
			}
			link.data('loaded', '1');
			container.find('.spin-container').hide();
		})
		.always(function() {
			link.data('is-loading', '0');
		});
		return false;
	});
});
