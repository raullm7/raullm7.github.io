$(function () {
  // Load all the books
  var folder = '/img/books';
  $.ajax({
    url : folder,
    success: function (data) {
      var listOfLinks = $(data).find('a');
      for (var i = 0; i < listOfLinks.length - 3; i += 3) {
        $('body').append(
          `<div class="row">
            <img src="${folder}${listOfLinks[i].pathname}">
            <img src="${folder}${listOfLinks[i + 1].pathname}">
            <img src="${folder}${listOfLinks[i + 2].pathname}">
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

    }
  });

});
