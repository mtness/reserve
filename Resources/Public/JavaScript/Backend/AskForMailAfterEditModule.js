import Modal from '@typo3/backend/modal.js';

const showModalConfiguration = TYPO3.settings.reserve?.showModal;

function showMailModal(uri) {
  Modal.advanced({
    type: Modal.types.iframe,
    content: uri,
    size: Modal.sizes.large,
    callback: modal => {
      modal.querySelectorAll('.modal-iframe').forEach(iFrameWindow => {
        iFrameWindow.addEventListener('load', event => {
          if (event.target.contentWindow.location.href.includes('#txReserveCloseModal')) {
            modal.hideModal();
          }
        });
      });
    }
  });
}

if (typeof showModalConfiguration !== 'undefined') {
  Modal.advanced({
    title: showModalConfiguration.title,
    content: showModalConfiguration.message,
    buttons: [
      {
        text: TYPO3.lang['reserve.modal.button.writeMail'],
        name: 'write-mail',
        icon: 'content-elements-mailform',
        active: true,
        btnClass: 'btn-primary',
        trigger: (event, modal) => {
          modal.hideModal();
          showMailModal(showModalConfiguration.uri);
        }
      }
    ]
  });
}
