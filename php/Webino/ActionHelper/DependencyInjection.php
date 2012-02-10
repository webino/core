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
 * @subpackage ActionHelper
 * @author     Peter Bačinský <peter@bacinsky.sk>
 * @copyright  2012 Peter Bačinský (http://www.bacinsky.sk/)
 * @license    http://www.webino.org/license/ New BSD License
 * @version    GIT: $Id$
 * @link       http://pear.webino.org/core/
 */

use Webino_Resource_Dependency_Interface          as DependencyResource;
use Webino_Resource_Dependency_Injector_Interface as DependencyInjector;

/**
 * Action helper for action controller dependency injection
 *
 * example of options:
 *
 * - class = Webino_ActionHelper_DependencyInjection
 * - inject.bootstrap.resource.dependency = dependency
 * - inject.bootstrap.pluginResource.dependencyResource = dependency
 * - options.webino.error.bootstrap.resource.log = log
 * - options.webino.error.bootstrap.pluginResource.drawResource = draw
 *
 * as pattern: options.(module).(controller). ...
 *
 * @category   Webino
 * @package    Core
 * @subpackage ActionHelper
 * @author     Peter Bačinský <peter@bacinsky.sk>
 * @copyright  2012 Peter Bačinský (http://www.bacinsky.sk/)
 * @license    http://www.webino.org/license/ New BSD License
 * @version    Release: @@PACKAGE_VERSION@@
 * @link       http://pear.webino.org/core/
 */

class Webino_ActionHelper_DependencyInjection
    extends Zend_Controller_Action_Helper_Abstract
{
    /**
     * Action helper options
     * 
     * @var array
     */
    private $_options;

    /**
     * Dependency injection injector
     *
     * @var DependencyInjector
     */
    private $_dependency;

    /**
     * Dependency injection provider
     *
     * @var DependencyResource
     */
    private $_dependencyResource;

    /**
     * Set options via constructor
     *
     * @param array $options 
     */
    public function __construct(array $options)
    {
        $this->_options = $options;
    }

    /**
     * Inject dependency injector
     *
     * @param DependencyInjector $injector
     *
     * @return Webino_ActionHelper_DependencyInjection
     */
    public function setDependency(DependencyInjector $injector)
    {
        $this->_dependency = $injector;

        return $this;
    }

    /**
     * Inject dependency plugin resource
     *
     * @param DependencyResource $resource
     *
     * @return Webino_Resource_Webino
     */
    public function setDependencyResource(DependencyResource $resource)
    {
        $this->_dependencyResource = $resource;

        return $this;
    }

    /**
     * Inject properties into action controller
     */
    public function init()
    {
        if (!isset($this->_options[$this->getRequest()->getModuleName()])) {

            return null;
        }

        $options = &$this->_options[$this->getRequest()->getModuleName()];

        if (!isset($options[$this->getRequest()->getControllerName()])) {

            return null;
        }

        $this->_dependency->inject(
            $this->getActionController(), $this->_dependencyResource,
            $options[$this->getRequest()->getControllerName()]
        );
    }
}
