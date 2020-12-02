$('.collapse').on('show.bs.collapse', function () {
    $('.collapse').collapse('hide')
})

$('.card.clickable').on('click touch', function () {
    $(this).find('.collapse').collapse('show')
})