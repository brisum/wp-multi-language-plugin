(function ($) {
  $('#wp-admin-bar-wp-ml-admin-switcher li[data-href]').on('click', function () {
    location.href = $(this).attr('data-href');
    return false;
  })
})(jQuery);
