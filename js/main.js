
// Script for the stickers effect.
window.onload = function() {
    Sticker.init('.sticker-github');
    Sticker.init('.sticker-cv');
}

$(window).scroll(function() {
    el = $('#scroll-top');
    if ($(window).scrollTop() > 200) {
      el.show();
    }
    else {
      el.hide();
    }
});

function goToDiv(number) {
  divTop = $('#' + number).offset().top;
  console.log(divTop);
  window.scroll({
    top: divTop,
    behavior: 'smooth'
  });
};


// When the user clicks on the button, scroll to the top of the document
function topFunction() {
  window.scroll({
    top: 0,
    behavior: 'smooth'
  });
};

jQuery(document).ready(function(){
	if( $('.cd-stretchy-nav').length > 0 ) {
		var stretchyNavs = $('.cd-stretchy-nav');

		stretchyNavs.each(function(){
			var stretchyNav = $(this),
				stretchyNavTrigger = stretchyNav.find('.cd-nav-trigger');

			stretchyNavTrigger.on('click', function(event){
				event.preventDefault();
				stretchyNav.toggleClass('nav-is-visible');
			});
		});

		$(document).on('click', function(event){
			( !$(event.target).is('.cd-nav-trigger') && !$(event.target).is('.cd-nav-trigger span') ) && stretchyNavs.removeClass('nav-is-visible');
		});
	}
});
