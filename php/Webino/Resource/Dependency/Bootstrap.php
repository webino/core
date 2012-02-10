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

use Zend_Application_Bootstrap_Bootstrapper       as Bootstrapper;
use Webino_Resource_Dependency_Injector_Interface as DependencyInjector;
use Webino_Resource_Dependency_Interface          as DependencyResource;

/**
 * The dependency resource bootstrap
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
class Webino_Resource_Dependency_Bootstrap
    implements Webino_Resource_Dependency_Bootstrap_Interface
{
    /**
     * Name of resource
     */
    const RESOURCE_NAME = 'dependency';

    /**
     * Dependency resource bootstrap
     *
     * @var Bootstrapper
     */
    private $_bootstrap;

    /**
     * Dependency resource object
     *
     * @var DependencyResource
     */
    private $_resource;

    /**
     * Dependency injector object
     *
     * @var DependencyInjector
     */
    private $_injector;

    /**
     * Inject bootstrapper
     *
     * @param Bootstrapper $bootstrap
     *
     * @return void
     */
    public function __construct($bootstrap)
    {
        $this->_bootstrap = $bootstrap;
    }

    /**
     * Dummy method
     *
     * @param array $options
     *
     * @return Webino_Resource_Dependency_Bootstrap
     */
    public function setOptions(array $options)
    {
        return $this;
    }

    /**
     * Return resource container object
     *
     * @return object
     */
    public function getContainer()
    {
        return $this->_bootstrap->getContainer();
    }

    /**
     * Retrieve application object
     *
     * @return Zend_Application|Zend_Application_Bootstrap_Bootstrapper
     */
    public function getApplication()
    {
        return $this->_bootstrap->getApplication();
    }

    /**
     * Retrieve application environment
     *
     * @return string
     */
    public function getEnvironment()
    {
        return $this->_bootstrap->getEnvironment();
    }

    /**
     * Retrieve list of class resource initializers (_init* methods). Returns
     * as resource/method pairs.
     *
     * @return array
     */
    public function getClassResources()
    {
        return $this->_bootstrap->getClassResources();
    }

    /**
     * Retrieve list of class resource initializer names (resource names only,
     * no method names)
     *
     * @return array
     */
    public function getClassResourceNames()
    {
        return $this->_bootstrap->getClassResourceNames();
    }

    /**
     * Bootstrap application or individual resource
     *
     * @param  null|string $resource
     *
     * @return mixed
     */
    public function bootstrap($resource = null)
    {
        if (self::RESOURCE_NAME == $resource) {

            return $this->_bootstrap;
        }

        return $this->_bootstrap->bootstrap($resource);
    }

    /**
     * Dummy method
     *
     * @return Webino_Resource_Dependency_Bootstrap
     */
    public function run()
    {
        return $this;
    }

    /**
     * Return resource
     *
     * @param string $name
     *
     * @return Resource
     */
    public function getResource($name)
    {
        if (self::RESOURCE_NAME == $name) {

            return $this->_injector;
        }

        return $this->_bootstrap->getResource($name);
    }

    /**
     * Inject dependency resource
     *
     * @param DependencyResource $resource
     *
     * @return Webino_Resource_Dependency_Bootstrap
     */
    public function setDependencyResource(DependencyResource $resource)
    {
        $this->_resource = $resource;

        return $this;
    }

    /**
     * Inject dependency injector
     *
     * @param DependencyInjector $injector
     *
     * @return Webino_Resource_Dependency_Bootstrap
     */
    public function setDependencyInjector(DependencyInjector $injector)
    {
        $this->_injector = $injector;

        return $this;
    }

    /**
     * Return plugin resource object
     *
     * @param string $name
     *
     * @return  object
     */
    public function getPluginResource($name)
    {
        if (self::RESOURCE_NAME == $name) {

            return $this->_resource;
        }

        return $this->_bootstrap->getPluginResource($name);
    }
}
