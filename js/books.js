$(function () {
  // Load all the books
  var folder = './img/books'
  var books = ["/1984","/a-brief-history-of-time","/a-tale-of-two-cities.jpg","/animal-farm","/basic-laws","/cabo-trafalgar","/crime-and-punishment","/don-quijote.jpg","/dorian-grey","/einstein","/el-amor-las-mujeres-y-la-vida","/el-arbol-de-la-ciencia","/el-asedio.jpg","/falco","/for-the-love-of-physics","/heart-of-darkness","/historia-de-la-filosofia.jpg","/i-robot","/la-ciudad-y-los-perros.jpg","/lord-of-the-rings","/los-enemigos-del-comercio","/los-enemigos-del-comercio-1","/love-in-the-time-of-cholera","/methamorphosis.jpg","/moby-dick","/one-hundred-years-of-solitude","/orient-express","/rimas-y-leyendas","/roger-ackroyd","/sherlock-holmes","/steve-jobs","/striped-pyjamas","/the-catcher-at-the-rye.jpg","/the-club-dumas","/the-count-of-monte-cristo","/the-death-of-ivan-ilych","/the-divine-comedy","/the-fear-of-freedom","/the-gambler.jpg","/the-hobbit","/the-martian","/the-name-of-the-rose","/the-physician","/the-pillars-of-earth","/the-prince.jpg","/the-republic","/veinte-poemas-de-amor","/war-and-peace"];
  for (var i = 0; i < books.length - 3; i += 3) {
    $('body').append(
      `<div class="row">
        <img src="${folder}${books[i]}">
        <img src="${folder}${books[i + 1]}">
        <img src="${folder}${books[i + 2]}">
      </div>
    `);
  }
  $('body').append('<div id="show-more"/>');

  // Show first three books
  $('.row').slice(0, 3).css('display', 'flex');

  // Add click handler to show more books
  $('#show-more').on('click', function (e) {
    e.preventDefault();
    var booksToShow = $('.row:hidden').slice(0, 3);
    booksToShow.css('display', 'flex');
    booksToShow.slideDown();
    $('html, body').animate({
      scrollTop: $(document).scrollTop() + 500
    }, 'slow');
    if ($('.row:hidden').length == 0) {
      $('#show-more').fadeOut('slow');
    }
  });

});
