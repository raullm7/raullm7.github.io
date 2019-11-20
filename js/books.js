$(function () {
  // Load all the books
  // for f in *; do printf '"%s", ' "${f%}"; done
  var folder = './img/books'
  var books = [
    "1984", "a-brief-history-of-time", "a-dolls-house.jpg", "a-tale-of-two-cities.jpg", "algorithms-to-live-by.jpg", "animal-farm", "attack-of-the-50-foot-blockchain.jpg", "basic-laws", "brave-new-world.jpg", "cabo-trafalgar", "campos-de-castilla.jpg", "crime-and-punishment", "cronica-de-una-muerte-anunciada.jpg", "don-quijote.jpg", "dorian-grey", "economics.jpg", "einstein", "el-amor-las-mujeres-y-la-vida", "el-arbol-de-la-ciencia", "el-asedio.jpg", "el-coronel-no-tiene-quien-le-escriba.jpg", "el-parnaso-espanol.jpg", "espana.jpg", "eva.jpg", "factfulness.jpg", "falco", "ficciones.png", "for-the-love-of-physics", "for-whom-the-bell-tolls.jpg", "heart-of-darkness", "historia-de-la-filosofia.jpg", "i-robot", "la-casa-verde.jpg", "la-ciudad-y-los-perros.jpg", "la-emboscadura.jpg", "la-rebelion-de-las-masas.jpg", "la-tabla-de-flandes.jpg", "la-voluntad.jpg", "lolita.jpg", "lord-of-the-rings", "los-enemigos-del-comercio", "los-enemigos-del-comercio-1", "love-in-the-time-of-cholera", "methamorphosis.jpg", "moby-dick", "one-hundred-years-of-solitude", "orient-express", "prisoners-of-geography.jpg", "rimas-y-leyendas", "roger-ackroyd", "sapiens.jpg", "sherlock-holmes", "steppenwolf.jpg", "steve-jobs", "striped-pyjamas", "the-art-of-war.jpg", "the-catcher-at-the-rye.jpg", "the-club-dumas", "the-count-of-monte-cristo", "the-death-of-ivan-ilych", "the-divine-comedy", "the-fear-of-freedom", "the-gambler.jpg", "the-gulag-archipielago.jpg", "the-hobbit", "the-martian", "the-name-of-the-rose", "the-physician", "the-pillars-of-earth", "the-prince.jpg", "the-republic", "the-selfish-gene.jpg", "the-sirens-of-titan.jpg", "the-story-of-art.jpg", "the-tale-of-the-unknown-island.jpg", "una-revolucion-liberal-para-españa.jpg", "veinte-poemas-de-amor", "war-and-peace"
  ];
  for (var i = 0; i < books.length - 3; i += 3) {
    $('#books').append(
      `<div class='row'>
        <img src='${folder}/${books[i]}'>
        <img src='${folder}/${books[i + 1]}'>
        <img src='${folder}/${books[i + 2]}'>
      </div>
    `);
  }

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
