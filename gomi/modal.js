```
$(document).on('click', '.booking_window', function() {
    //背景をスクロールできないように　&　スクロール場所を維持
    scroll_position = $(window).scrollTop();
    $('body').addClass('fixed').css({ 'top': -scroll_position });
    // モーダルウィンドウを開く
    $('.post_process').fadeIn();
    $('.modal').fadeIn();
});
```