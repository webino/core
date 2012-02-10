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

use Zend_Application_Bootstrap_Bootstrapper as Bootstrapper;

/**
 * Bootstrap injection for dependency injector
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
class Webino_Resource_Dependency_Injection_Bootstrap
    extends Webino_Resource_Dependency_Injection_Abstract
{
    /**
     * Inject bootstrapper into injection
     *
     * @param Bootstrapper $giver
     *
     * @return Webino_Resource_Dependency_Injection_Bootstrap
     */
    public function setGiver(Bootstrapper $giver)
    {
        return parent::_setGiver($giver);
    }

    /**
     * Get resource from bootstrap
     *
     * @param string $name Resource name
     *
     * @return object
     */
    public function resource($name)
    {
        return $this->_giver->bootstrap($name)->getResource($name);
    }
    
    /**
     * Get plugin resource from bootstrap
     *
     * @param string $name Resource name
     *
     * @return Zend_Application_Resource_Resource
     */
    public function pluginResource($name)
    {
        return $this->_giver->getPluginResource($name);
    }

    /**
     * Provide method call on injection to get giver getter value
     *
     * @param string $function Injection function
     * @param string $resource Resource name
     * @param string $method   Resource method
     *
     * @return mixed
     */
    public function call($function, $resource, $method)
    {
        $params = func_get_args();

        array_shift($params);
        array_shift($params);
        array_shift($params);

        return call_user_func_array(
            array($this->$function($resource), $method), $params
        );
    }
}
