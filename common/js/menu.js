window.addEventListener( "DOMContentLoaded", function(){
    var $nav = document.getElementById( 'navArea' );
    var $btn = document.getElementsByClassName( 'toggle_btn' );
    var open = 'open';
    Array.prototype.forEach.call( $btn, function( $ ){
        $.addEventListener( 'click', function(){
            if( ! $nav.classList.contains( open ) ){
                $nav.classList.add( open );
            } else {
                $nav.classList.remove( open );
            }
        }, false );
    } );
}, false );