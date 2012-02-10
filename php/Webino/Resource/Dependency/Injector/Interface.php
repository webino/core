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
 * @subpackage Dependency
 * @author     Peter Bačinský <peter@bacinsky.sk>
 * @copyright  2012 Peter Bačinský (http://www.bacinsky.sk/)
 * @license    http://www.webino.org/license/ New BSD License
 * @version    GIT: $Id$
 * @link       http://pear.webino.org/core/
 */

use Webino_Resource_Dependency_Injection_Interface as Injection;

/**
 * Interface of dependency injector
 *
 * @category   Webino
 * @package    Core
 * @subpackage Dependency
 * @author     Peter Bačinský <peter@bacinsky.sk>
 * @copyright  2012 Peter Bačinský (http://www.bacinsky.sk/)
 * @license    http://www.webino.org/license/ New BSD License
 * @version    Release: @@PACKAGE_VERSION@@
 * @link       http://pear.webino.org/core/
 */
interface Webino_Resource_Dependency_Injector_Interface
{
    /**
     * Add injection object
     *
     * @param string    $key       Injection ID
     * @param Injection $injection
     *
     * @return Webino_Resource_Dependency_Injector_Interface
     */
    public function addInjection($key, Injection $injection);

    /**
     * Return params from giver by injection and options
     *
     * @param object $giver   Object to get params
     * @param array  $options Instructions
     *
     * @return array Params to inject
     */
    public function injection($giver, $options);
}
