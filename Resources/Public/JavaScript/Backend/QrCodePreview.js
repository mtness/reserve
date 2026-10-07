import AjaxRequest from '@typo3/core/ajax/ajax-request.js';

document.addEventListener('click', event => {
  const button = event.target.closest('.generate-qr-code');
  if (button === null) {
    return;
  }

  event.preventDefault();

  const facilityUid = button.dataset.facility;
  const previewDiv = document.querySelector('.qr-code-preview[data-facility="' + facilityUid + '"]');
  if (previewDiv === null) {
    return;
  }

  previewDiv.textContent = '…';

  new AjaxRequest(TYPO3.settings.ajaxUrls['tx_reserve_qr_code_preview'])
    .withQueryArguments({ facility: facilityUid })
    .get()
    .then(async response => {
      const responseObject = await response.resolve();
      const image = document.createElement('img');
      image.src = responseObject.qrCode;
      previewDiv.replaceChildren(image);
    })
    .catch(() => {
      previewDiv.replaceChildren();
    });
});
