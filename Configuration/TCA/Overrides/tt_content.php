<?php

/*
 * This file is part of the package jweiland/reserve.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use JWeiland\Reserve\Backend\Preview\ReservePluginPreview;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

if (!defined('TYPO3')) {
    die('Access denied.');
}

// TYPO3 v14 registers the FlexForm together with the plugin (7th argument). TYPO3 v13 ignores that argument
// and still needs addPiFlexFormValue(), which is deprecated since v14.
$isTypo3V14OrHigher = (new Typo3Version())->getMajorVersion() >= 14;

ExtensionUtility::registerPlugin(
    'Reserve',
    'Reservation',
    'LLL:EXT:reserve/Resources/Private/Language/locallang_db.xlf:plugin.reserve_reservation.title',
    'tx_reserve_domain_model_reservation',
    'plugins',
    'LLL:EXT:reserve/Resources/Private/Language/locallang_db.xlf:plugin.reserve_reservation.description',
    'FILE:EXT:reserve/Configuration/FlexForms/Reservation.xml',
);

ExtensionUtility::registerPlugin(
    'Reserve',
    'Management',
    'LLL:EXT:reserve/Resources/Private/Language/locallang_db.xlf:plugin.reserve_management.title',
    'ext-reserve-wizard-icon',
    'plugins',
    'LLL:EXT:reserve/Resources/Private/Language/locallang_db.xlf:plugin.reserve_management.description',
    'FILE:EXT:reserve/Configuration/FlexForms/Management.xml',
);

if (!$isTypo3V14OrHigher) {
    foreach (['reserve_reservation' => 'Reservation', 'reserve_management' => 'Management'] as $cType => $flexForm) {
        ExtensionManagementUtility::addPiFlexFormValue(
            '*',
            'FILE:EXT:reserve/Configuration/FlexForms/' . $flexForm . '.xml',
            $cType,
        );

        ExtensionManagementUtility::addToAllTCAtypes(
            'tt_content',
            '--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:plugin,pi_flexform',
            $cType,
            'after:subheader',
        );
    }
}

$GLOBALS['TCA']['tt_content']['types']['reserve_reservation']['previewRenderer'] = ReservePluginPreview::class;
$GLOBALS['TCA']['tt_content']['types']['reserve_management']['previewRenderer'] = ReservePluginPreview::class;
