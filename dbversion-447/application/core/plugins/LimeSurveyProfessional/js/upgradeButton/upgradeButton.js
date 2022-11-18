$(document).ready(function () {
// if user is not siteadmin add modal functionality
    if ($('.no-siteadmin').length) {
        let upgradeLink = $('#upgrade-button');
        upgradeLink.attr('href', '#');
        upgradeLink.click(function (e) {
            e.preventDefault();
            // show modal
            $(document.body).append(getModalHtml());
            $('#upgrade-notification').modal('show');
        });
    }
});


