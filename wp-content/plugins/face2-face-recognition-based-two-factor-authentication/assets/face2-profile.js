(function($){
	var container = $('#face2_user');

	container.html( '<tr><th><label>' + window.face2.th_text + '</label></th><td><a class="button thickbox" href="' + window.face2.ajax + '&KeepThis=true&TB_iframe=true&height=580&width=450">' + window.face2.button_text + '</a></td></tr>' );

	$( '.button', container ).on( 'click', function( ev ) {
		ev.preventDefault();
	} );
})(jQuery);