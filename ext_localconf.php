<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

call_user_func(static function() {
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'JWeiland.Reserve',
        'Reservation',
        [
            'Checkout' => 'list,form,create,confirm,cancel'
        ],
        [
            'Checkout' => 'form,create,confirm,cancel'
        ]
    );

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'JWeiland.Reserve',
        'Management',
        [
            'Management' => 'overview,period,periodsOnSameDay,scanner,scan'
        ],
        [
            'Management' => 'overview,period,periodsOnSameDay,scanner,scan'
        ]
    );

    $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1590659241206] = [
        'nodeName' => 'reserveQrCodePreview',
        'priority' => '70',
        'class' => \JWeiland\Reserve\Form\Element\QrCodePreviewElement::class,
    ];

    $icons = ['facility', 'order', 'order_1', 'period', 'reservation', 'email'];
    $iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
    foreach ($icons AS $model) {
        $identifier = 'tx_reserve_domain_model_' . $model;
        $iconRegistry->registerIcon(
            $identifier,
            \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
            ['source' => 'EXT:reserve/Resources/Public/Icons/' . $identifier . '.svg']
        );
    }
    unset($icons, $iconRegistry);

    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = \JWeiland\Reserve\Hooks\DataHandler::class;
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processCmdmapClass'][] = \JWeiland\Reserve\Hooks\DataHandler::class;
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_pagerenderer.php']['render-preProcess'][] = \JWeiland\Reserve\Hooks\PageRenderer::class . '->processTxReserveModalUserSetting';

    $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeResolver'][1593430553393] = [
        'nodeName' => 'reserveCheckboxToggle',
        'priority' => 50,
        'class' => \JWeiland\Reserve\Form\Resolver\CheckboxToggleElementResolver::class,
    ];

    if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('events2')) {
        // Replace with EventListeners while removing TYPO3 9 compatibility
        $signalSlotDispatcher = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
            \TYPO3\CMS\Extbase\SignalSlot\Dispatcher::class
        );
        // SqlExpectedSchemaService is a non existing class, so please keep the double backslashes
        $signalSlotDispatcher->connect(
            'TYPO3\\CMS\\Install\\Service\\SqlExpectedSchemaService',
            'tablesDefinitionIsBeingBuilt',
            \JWeiland\Reserve\Tca\CreateForeignTableColumns::class,
            'addEvents2DatabaseColumnsToTablesDefinition'
        );
    }
});
