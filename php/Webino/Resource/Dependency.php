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
 * @subpackage Resource
 * @author     Peter Bačinský <peter@bacinsky.sk>
 * @copyright  2012 Peter Bačinský (http://www.bacinsky.sk/)
 * @license    http://www.webino.org/license/ New BSD License
 * @version    GIT: $Id$
 * @link       http://pear.webino.org/core/
 */

use Webino_Resource_Dependency_Injector_Interface as DependencyInjector;

/**
 * Resource for dependency injection
 *
 * example of config:
 *
 * - injector = Webino_Resource_Dependency_Injector
 * - bootstrapper = Webino_Resource_Dependency_Bootstrap
 * - injections.controller = Webino_Resource_Dependency_Injection_Controller
 * - inject.pluginResource.webino.bootstrap.resource.frontController = frontController
 * - inject.pluginResource.webino.bootstrap.pluginResource.dependencyResource = dependency
 * - inject.resource.errors.bootstrap.pluginResource.dependencyResource = dependency
 * - inject.resource.errors.bootstrap.resource.events = events
 *
 * Injector must implement specific interface, see class constants.
 *
 * @category   Webino
 * @package    Core
 * @subpackage Resource
 * @author     Peter Bačinský <peter@bacinsky.sk>
 * @copyright  2012 Peter Bačinský (http://www.bacinsky.sk/)
 * @license    http://www.webino.org/license/ New BSD License
 * @version    Release: @@PACKAGE_VERSION@@
 * @link       http://pear.webino.org/core/
 */
class Webino_Resource_Dependency
    extends Zend_Application_Resource_ResourceAbstract
    implements Webino_Resource_Dependency_Interface
{
    /**
     * Name of resource
     */
    const RESOURCE_NAME = 'dependency';

    /**
     * Interface to implement by injector
     */
    const INJECTOR_INTERFACE = 'Webino_Resource_Dependency_Injector_Interface';

    /**
     * Interface to implement by bootstrapper
     */
    const BOOTSTRAPPER_INTERFACE = 'Webino_Resource_Dependency_Bootstrap_Interface';

    /**
     * Dependency injector object
     *
     * @var DependencyInjector
     */
    private $_injector;

    /**
     * Return injector
     */
    public function init()
    {
        return $this->getInjector();
    }

    /**
     * Inject injector
     *
     * @param mixed $injector (string/object)
     *
     * @return DependencyInjector
     */
    public function setInjector($injector)
    {
        if (is_string($injector)) {
            $injector = new $injector;
        }

        if (
            !in_array(
                self::INJECTOR_INTERFACE, class_implements(get_class($injector))
            )
        ) {
            throw new InvalidArgumentException(
                sprintf(
                    'Injector "%s" must implements "%s", '
                    . 'but it implements "%s"',
                    get_class($injector), self::INJECTOR_INTERFACE,
                    join(', ', class_implements($injector))                    
                )
            );
        }

        $this->_bootstrap->getContainer()->dependency
            = $this->_injector = $injector;

        return $this;
    }

    /**
     * Return injector
     *
     * @return DependencyInjector
     */
    public function getInjector()
    {
        return $this->_injector;
    }

    /**
     * Add injections into injector
     *
     * @param array $injections
     *
     * @return Webino_Resource_Dependency
     */
    public function setInjections(array $injections)
    {
        foreach ($injections as $key=>$class) {
            $this->getInjector()->addInjection($key, new $class);
        }

        return $this;
    }

    /**
     * Inject dependency bootstrapper
     *
     * @param mixed $bootstrapper (string/object)
     *
     * @return Webino_Resource_Dependency
     */
    public function setBootstrapper($bootstrapper)
    {
        if (is_string($bootstrapper)) {

            $bootstrapper = new $bootstrapper(
                $this->getBootstrap()
            );
        }

        if (
            !in_array(
                self::BOOTSTRAPPER_INTERFACE,
                class_implements(get_class($bootstrapper))
            )
        ) {
            throw new InvalidArgumentException(
                sprintf(
                    'Bootstrapper "%s" must implements "%s", '
                    . 'but it implements "%s"',
                    get_class($bootstrapper), self::BOOTSTRAPPER_INTERFACE,
                    join(', ', class_implements($bootstrapper))
                )
            );
        }

        $this->setBootstrap(
            $bootstrapper->setDependencyResource($this)
            ->setDependencyInjector($this->getInjector())
        );

        return $this;
    }

    /**
     * Perform resource injections
     *
     * @param array $options
     *
     * @return Webino_Resource_Dependency
     */
    public function setInject(array $options)
    {
        foreach ($options as $type => $typeOptions) {

            $consumerFc = '_' . $type;
            foreach ($typeOptions as $consumer => $injectionSettings) {
                $this->getInjector()->inject(
                    $this->$consumerFc($consumer), $this, $injectionSettings
                );
            };
        }
        
        return $this;
    }

    /**
     * Return plugin resource object from bootstrapper
     *
     * @param string $name
     *
     * @return object
     */
    protected function _resource($name)
    {
        if (self::RESOURCE_NAME == $name) {
            
            return $this->getInjector();
        }

        return $this->_bootstrap->bootstrap($name)->getResource($name);
    }

    /**
     * Return plugin resource from bootstrapper
     *
     * @param string $name
     *
     * @return Webino_Resource_Dependency 
     */
    protected function _pluginResource($name)
    {
        if (self::RESOURCE_NAME == $name) {

            return $this;
        }

        return $this->_bootstrap->getPluginResource($name);
    }

    /**
     * Return application
     *
     * @return Zend_Application
     */
    public function getApplication()
    {
        return $this->_bootstrap->getApplication();
    }
}
