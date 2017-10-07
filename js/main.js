
// Script for the stickers effect.
window.onload = function() {
    Sticker.init('.sticker-github');
    Sticker.init('.sticker-cv');
}

window.onscroll = function() {
  el = document.getElementById('scroll-top');
  if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
      el.style.display = "block";
  } else {
      el.style.display = "none";
  }
};


// When the user clicks on the button, scroll to the top of the document
function topFunction() {
  window.scroll({
    top: 0,
    behavior: 'smooth'
  });
}

function goToSection(number) {
  $('html', 'body').animate({
    scrollTop: 200,
    behavior: 'smooth'
  })
}
