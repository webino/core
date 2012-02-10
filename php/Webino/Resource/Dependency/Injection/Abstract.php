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

/**
 * Dependency injection abstract class
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
abstract class Webino_Resource_Dependency_Injection_Abstract
    implements Webino_Resource_Dependency_Injection_Interface
{
    /**
     * Provides parameters for injection
     *
     * @var object
     */
    protected $_giver;

    /**
     * Inject giver for injection
     *
     * @param object $giver
     *
     * @return Webino_Resource_Dependency_Injection_Abstract 
     */
    protected function _setGiver($giver)
    {
        $this->_giver = $giver;

        return $this;
    }

    /**
     * Return given param
     *
     * @param string $param
     *
     * @return string
     */
    public function param($param)
    {
        return $param;
    }

    /**
     * Call getter on giver and return value
     *
     * @param string $name Name of getter
     *
     * @return mixed
     */
    public function getter($name)
    {
        $getter = 'get' . ucfirst($name);

        return $this->_giver->$getter();
    }
}
