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
 * @category   Webino
 * @package    Core
 * @subpackage bin
 * @author     Peter Bačinský <peter@bacinsky.sk>
 * @copyright  2012 Peter Bačinský (http://www.bacinsky.sk/)
 * @license    http://www.webino.org/license/ New BSD License
 * @version    GIT: $Id$
 * @link       http://pear.webino.org/core/
 */

ini_set('html_errors', 'off');

/**
 * CLI bootstrap
 */
require_once 'Webino/cli/bootstrap.php';

/**
 * Client Console
 */
require_once 'Webino/Tool/Framework/Client/Console.php';

/**
 * Command line handler
 */
require_once 'Webino/Cli.php';

/**
 * Start CLI
 */
$webinoCli = new Webino_Cli(new Webino_Tool_Framework_Client_Console);

$webinoCli
    ->loadTool(glob(APPLICATION_LIB . '/Webino/Tool/*.php'))
    ->loadTool(glob(PEAR_PHP_DIR    . '/Webino/Tool/*.php'));

try {
    $webinoCli->dispatch();
} catch(Exception $e) {
    echo sprintf("\033[41m%s\033[m" . PHP_EOL, $e->getMessage());
}

echo $webinoCli;
