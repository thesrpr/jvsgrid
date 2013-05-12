jQuery(document).ready(function($) {

function noHover() {
    return this.is(':hover') ? this.wait('mouseleave') : this
}

$.fn.random = function(){ return this.eq(
  Math.floor(Math.random()*this.length)) };

var griditem = $("#animated-grid li");

griditem.find("a").on("click", function(e){
	e.preventDefault();
});

function gridautomate() {

	griditem.repeat().random().each($).addClass('active').wait(5000).removeClass('active').until();

};

gridautomate();


});