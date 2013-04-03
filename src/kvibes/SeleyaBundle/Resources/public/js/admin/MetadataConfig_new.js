jQuery(document).ready(function() {
    collectionHolder.find('li').each(function() { addTagFormDeleteLink($(this)); });
	collectionHolder.append($newLinkLi);
    collectionHolder.data('index', collectionHolder.find(':input').length);
    $addTagLink.on('click', function(e) {
        e.preventDefault();
        addTagForm(collectionHolder, $newLinkLi);
    });

    function addTagForm(collectionHolder, $newLinkLi) {
        var prototype = collectionHolder.data('prototype');
        var index = collectionHolder.data('index');
        var newForm = prototype.replace(/__name__/g, index);
        collectionHolder.data('index', index+1);
        var $newFormLi = $('<li></li>').append(newForm);
        $newLinkLi.before($newFormLi);
        addTagFormDeleteLink($newFormLi);
    }

    function addTagFormDeleteLink($tagFormLi) {
        var $removeFormA = $('<a href="#" class="btn btn-mini btn-danger align-middle left-space-small"><i class="icon-minus-sign icon-white"></i></a>');
        $tagFormLi.find('div.controls').append($removeFormA);
        $removeFormA.on('click', function(e) {
        	e.preventDefault();
            $tagFormLi.remove();
        });
    }
    
    
    $('#metadataConfig_definition').change(function() {
    	collectionDiv.toggle($(this).val() == 'select');
    });
    collectionDiv.toggle($('#metadataConfig_definition').val() == 'select');
    
});
