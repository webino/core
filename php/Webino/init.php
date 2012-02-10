<?php
/**
 * Webino
 *
 * PHP version 5.3
 *
 * LICENSE: This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt. It is also available through the
 * world-wide-web at this URL: http://www.webino.org/license/
 * If you did not receive a copy of the license and are unable to obtain it
 * through the world-wide-web, please send an email to license@webino.org
 * so we can send you a copy immediately.
 *
 * This file must be required before public/index.php. It defines base
 * constants and creates the application.
 *
 * @category   Webino
 * @package    Core
 * @author     Peter Bačinský <peter@bacinsky.sk>
 * @copyright  2012 Peter Bačinský (http://www.bacinsky.sk/)
 * @license    http://www.webino.org/license/ New BSD License
 * @version    GIT: $Id$
 * @version    Release: @@PACKAGE_VERSION@@
 * @link       http://pear.webino.org/core/
 */

/**
 * Zend_Application
 */
require_once 'Zend/Application.php';

/**
 * Defined constants bellow are intended to use in INI configs and this file.
 * Any class shouldn't depend on global constants.
 */

/**
 * Application start time
 */
defined('APPLICATION_TIME')
    || define(
        'APPLICATION_TIME', microtime(true)
    );

/**
 * Application enviroment name
 */
defined('APPLICATION_ENV')
    || define(
        'APPLICATION_ENV', (
            getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'
        )
    );

/**
 * This is important constant, because PEAR_INSTALL_DIR could be different.
 *
 * On a shared host you have PEAR install dir for your account in your home
 * directory, so Webino is located there after install.
 */
defined('PEAR_PHP_DIR')
    || define(
        'PEAR_PHP_DIR', realpath(
            __DIR__ . '/..'
        )
    );

/**
 * Get the protocol to make URLs
 */
if (isset($_SERVER['SERVER_PROTOCOL'])) {

    list($serverProtocol) = explode('/', $_SERVER['SERVER_PROTOCOL']);
    $serverProtocol       = strtolower($serverProtocol);

    /**
     * URL base
     */
    defined('URL_BASE')
        || define(
            'URL_BASE', $serverProtocol . '://'. $_SERVER['HTTP_HOST']
            . preg_replace(
                '~/[a-zA-Z0-9_-]+.php~', "/", $_SERVER['PHP_SELF']
            )
        );

    /**
     * Current URL
     */
    defined('URL')
        || define(
            'URL', $serverProtocol . '://'. $_SERVER['HTTP_HOST']
            . htmlentities($_SERVER['REQUEST_URI'])
        );
}

/**
 * Zend_Application
 */
$application = new Zend_Application(APPLICATION_ENV);
