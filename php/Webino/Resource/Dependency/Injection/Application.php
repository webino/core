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

use Zend_Application as Application;

/**
 * Application injection for dependency resource
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
class Webino_Resource_Dependency_Injection_Application
    extends Webino_Resource_Dependency_Injection_Abstract
{
    /**
     * Inject application into injection
     *
     * @param Application $giver
     *
     * @return Webino_Resource_Dependency_Injection_Bootstrap
     */
    public function setGiver(Application $giver)
    {
        return parent::_setGiver($giver);
    }

    /**
     * Return environment name
     *
     * @return string
     */
    public function environment()
    {
        return $this->_giver->getEnvironment();
    }

    /**
     * Return bootstrapper
     *
     * @return Bootstrapper
     */
    public function bootstrap()
    {
        return $this->_giver->getBootstrap();
    }
}
