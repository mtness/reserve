<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/reserve.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Reserve\Form\Element;

use TYPO3\CMS\Backend\Form\Element\AbstractFormElement;
use TYPO3\CMS\Core\Page\JavaScriptModuleInstruction;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Form element to render a QR code preview in TCA using
 * type=none, renderType=reserveQrCodePreview
 */
class QrCodePreviewElement extends AbstractFormElement
{
    public function render(): array
    {
        $resultArray = $this->initializeResultArray();
        $resultArray['html'] = sprintf(
            '<div class="alert alert-info">%s</div><p><button type="button" class="btn btn-default generate-qr-code" data-facility="%d">%s</button></p><p class="qr-code-preview" data-facility="%d"></p>',
            LocalizationUtility::translate('LLL:EXT:reserve/Resources/Private/Language/locallang_db.xlf:tx_reserve_domain_model_facility.qr_code_preview.notice'),
            $this->data['vanillaUid'],
            LocalizationUtility::translate('LLL:EXT:reserve/Resources/Private/Language/locallang_db.xlf:tx_reserve_domain_model_facility.qr_code_preview.button'),
            $this->data['vanillaUid'],
        );
        $resultArray['javaScriptModules'][] = JavaScriptModuleInstruction::create(
            '@jweiland/reserve/backend/QrCodePreview.js',
        );

        return $resultArray;
    }
}
