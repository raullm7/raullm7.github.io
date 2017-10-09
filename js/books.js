$(function () {
    $(".row").slice(0, 3).show();
    $("#show-more").on('click', function (e) {
        e.preventDefault();
        $(".row:hidden").slice(0, 3).slideDown();
        $("html, body").animate({
          scrollTop: $(document).height()
        }, "slow");
        if ($(".row:hidden").length == 0) {
            $("#show-more").fadeOut('slow');
        }
    });
});
