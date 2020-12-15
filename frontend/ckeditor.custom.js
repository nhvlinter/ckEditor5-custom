(function( $ ) {
$.fn.customCkEditor= function( options ) {    
    // Extend our default options with those provided.    
    // Note that the first argument to extend is an empty    
    // object – this is to keep from overriding our "defaults" object.
     var _settings = $.extend( {}, $.fn.customCkEditor.defaults.option, options );  
    // Our plugin implementation code goes here. 
    ClassicEditor
        .create( this[0] )
        .catch( error => {
            console.error( error );
        } );
}; 
$.fn.customCkEditor.defaults = {
    option:{}
}
}( jQuery ));