var banner = $('#promotional-banner');

if (!banner.length) {
    $(getBannerHtml()).insertBefore('nav.navbar');
    banner = $('#promotional-banner');
}

banner.on('closed.bs.alert', function () {
    let url = $(this).data('href');
    let data = {bid: $(this).data('bid')};
    $.post(url, data);
});

