$(function() {
	$('#checkAll').click(function() {
		var checked = $(this).is(':checked');
		var prefix = $(this).data('name-prefix');
		$('input[type="checkbox"][name^="record"]').prop('checked', checked);
	});
});
