<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/reserve.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Reserve\Service;

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\View\ViewFactoryData;
use TYPO3\CMS\Core\View\ViewFactoryInterface;
use TYPO3\CMS\Core\View\ViewInterface;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

/**
 * Utility methods to create and render the Fluid views for mailing
 */
class FluidService
{
    /**
     * Mail templates are located in Templates/Mail/
     */
    private const MAIL_TEMPLATE_FOLDER = 'Mail';

    public function __construct(
        private readonly ConfigurationManagerInterface $configurationManager,
        private readonly ViewFactoryInterface $viewFactory,
    ) {}

    /**
     * Create a view which uses the template, layout and partial root paths of EXT:reserve. As the paths are read
     * from plugin.tx_reserve.view they can be overridden the standard Extbase way.
     */
    public function createViewForMailing(): ViewInterface
    {
        $extbaseFrameworkConfiguration = $this->configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK,
            'reserve',
            'Reservation',
        );

        // $extbaseFrameworkConfiguration is filled only if the default TypoScript setup is included, otherwise
        // $extbaseFrameworkConfiguration['view']['templateRootPaths'] would be null, so let's set some default values!
        return $this->viewFactory->create(
            new ViewFactoryData(
                templateRootPaths: $extbaseFrameworkConfiguration['view']['templateRootPaths'] ?? ['EXT:reserve/Resources/Private/Templates/'],
                partialRootPaths: $extbaseFrameworkConfiguration['view']['partialRootPaths'] ?? ['EXT:reserve/Resources/Private/Partials/'],
                layoutRootPaths: $extbaseFrameworkConfiguration['view']['layoutRootPaths'] ?? ['EXT:reserve/Resources/Private/Layouts/'],
                request: $this->getRequest(),
            ),
        );
    }

    /**
     * Render a template of Templates/Mail/
     *
     * @param string $template Name of the template without path and file extension, e.g. "Cancellation"
     * @param array<string, mixed> $vars Variables for the Fluid template
     */
    public function renderMailTemplate(string $template, array $vars = []): string
    {
        $view = $this->createViewForMailing();
        $view->assignMultiple($vars);

        return $view->render(self::MAIL_TEMPLATE_FOLDER . '/' . $template);
    }

    /**
     * @param string $marker content to replace e.g. ###MY_MARKER###
     * @param string $template fluid template name of Templates/Mail/ without file extension, e.g. "Reservation"
     * @param string $content string which may contain $marker
     * @param array $vars additional vars for the fluid template
     */
    public function replaceMarkerByRenderedTemplate(
        string $marker,
        string $template,
        string $content,
        array $vars = [],
    ): string {
        return str_replace($marker, $this->renderMailTemplate($template, $vars), $content);
    }

    public function getRequest(): ServerRequestInterface
    {
        return $GLOBALS['TYPO3_REQUEST'];
    }
}
