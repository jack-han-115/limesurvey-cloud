$(document.body).append(getUnclosableModalHtml());
var modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('unclosable-notification-modal'));
modal.show();
