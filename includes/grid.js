jQuery(document).ready(function($) {

function noHover() {
    return this.is(':hover') ? this.wait('mouseleave') : this
}

$.fn.random = function(){ return this.eq(
  Math.floor(Math.random()*this.length)) };

var griditem = $("#animated-grid li");

griditem.eq(0).addClass('active');

griditem.find("a").on("click", function(e){
	e.preventDefault();
	$(this).parent().toggleClass('active');
	$(this).parent().siblings().removeClass('active');
});

function gridautomate() {

	griditem.repeat().random().each($).addClass('active').wait(5000).wait(noHover).removeClass('active').until();

};


});