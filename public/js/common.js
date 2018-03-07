$(document).ready(function() {

  var $languageEl = $('.language__el'),
      $language = $('.language'),
      $tableDllHash = $('.table-dll__hash');

  $language.click(function(){
    $(this).toggleClass('is-open');
  });

  $languageEl.click(function(){
    $languageEl.removeClass('is-active');
    $(this).toggleClass('is-active');
  });

  $tableDllHash.map(function () {
    var toggledClass = 'is-visible';

    $(this).on('click', function () {
      var $el = $(this).find('.hash');
      $('.table-dll__hash').not(this).find('.hash').removeClass(toggledClass);

      if (($el).hasClass(toggledClass)) {
        $($el).removeClass(toggledClass)
      } else {
        $($el).addClass(toggledClass)
      }
    })
  });

  $('table.table').DataTable({
    paging: false,
    searching: false,
    info: false,
    autoWidth: false
  });

  $('#how').on("click","a", function (event) {
    var margin,
      id  = $(this).attr('href'),
      top = $(id).offset().top;

      margin = 10;

    event.preventDefault();

    $('body,html').animate({scrollTop: top - margin}, 1000);
  });
});
