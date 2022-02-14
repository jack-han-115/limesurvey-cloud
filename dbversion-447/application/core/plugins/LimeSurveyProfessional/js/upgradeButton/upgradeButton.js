$(document).ready(function () {
// change link to button
    let upgradeLink = $('.upgrade-icon').parent();
    upgradeLink.addClass('btn btn-success upgrade-button');
    upgradeLink.attr('target', '_blank');

// Change position of button from last in list to first
// take it out of li element and put it before ul starts
    let parentLi = upgradeLink.parent('li');
    let parentNavbar = parentLi.parents('.navbar-collapse');
    upgradeLink.prependTo(parentNavbar);
    parentLi.remove();

// if user is not siteadmin add modal functionality
    if ($('.no-siteadmin').length) {
        upgradeLink.attr('href', '#');
        upgradeLink.click(function (e) {
            e.preventDefault();
            // show modal
            $(document.body).append(getModalHtml());
            $('#upgrade-notification').modal('show');
        });
    }
});


