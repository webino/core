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

use Zend_Controller_Front               as FrontController;
use Zend_Controller_Action_HelperBroker as ActionHelperBroker;
use Webino_Resource_Dependency          as DependencyResource;
use Webino_Resource_Dependency_Injector as DependencyInjector;

/**
 * Webino resource provides extra settings ability via application config.
 *
 * Performs better controller plugin and action helper registration via config
 * with options.
 *
 * example of options:
 *
 * - plugins.errorHandler.class = Zend_Controller_Plugin_ErrorHandler
 * - plugins.errorHandler.stackIndex = 100
 * - plugins.errorHandler.options.module = webino
 * - actionHelpers.dependencyInjection.class = Webino_ActionHelper_DependencyInjection
 * - actionHelpers.dependencyInjection.inject.bootstrap.resource.dependency = dependency
 * - actionHelpers.dependencyInjection.inject.bootstrap.pluginResource.dependencyResource = dependency
 * - actionHelpers.dependencyInjection.options.true = true
 * 
 * If "setBootstrap" method is founded application bootstrap is injected.
 * Booth plugins and helpers supports dependency injection. 
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
class Webino_Resource_Webino
    extends Zend_Application_Resource_ResourceAbstract
{
    /**
     * Name of controler plugins options key
     */
    const PLUGINS_KEYNAME = 'plugins';

    /**
     * Name of action helpers options key
     */
    const HELPERS_KEYNAME = 'actionHelpers';

    /**
     * Name of options key
     */
    const OPTIONS_KEYNAME = 'options';
    
    /**
     * Name of stackIndex value key
     */
    const STACKINDEX_KEYNAME = 'stackindex';

    /**
     * Name of class name value key
     */
    const CLASS_KEYNAME = 'class';

    /**
     * Name of inject options key
     */
    const INJECT_KEYNAME = 'inject';

    /**
     * Name of method to inject bootstrap
     */
    const BOOTSTRAP_SETTER = 'setBootstrap';

    /**
     * Depenency injection injector
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
     * Zend front controller
     *
     * @var FrontController
     */
    private $_frontController;

    /**
     * Controller plugin and action helper registration
     */
    public function init()
    {
        $options = $this->getOptions();

        if (isset($options[self::PLUGINS_KEYNAME])) {
            $this->_plugins($options[self::PLUGINS_KEYNAME]);
        }

        if (isset($options[self::HELPERS_KEYNAME])) {
            $this->_actionHelpers($options[self::HELPERS_KEYNAME]);
        }

        return $this;
    }

    /**
     * Inject dependency injector
     *
     * @param DependencyInjector $dependency
     *
     * @return Webino_Resource_Webino
     */
    public function setDependency(DependencyInjector $dependency)
    {
        $this->_dependency = $dependency;

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
     * Inject front controller
     *
     * @param FrontController $frontController
     * 
     * @return Webino_Resource_Webino
     */
    public function setFrontController(FrontController $frontController)
    {
        $this->_frontController = $frontController;

        return $this;
    }

    /**
     * Register plugins with options
     *
     * Method is similar as in Frontcontroller except that if plugin options
     * setting exists these options are passed as the constructor argument
     * for that plugin. And if plugin class has method to set bootstrap
     * bootstrap will be injected.
     *
     * example of options:
     *
     * - stackIndex           = 11
     * - class                = Custom_Plugin
     * - options.customOption = optionValue
     *
     * @param array $plugins
     */
    private function _plugins(array $plugins)
    {
        foreach ($plugins as $settings) {

            $pluginClass = null;
            $options     = null;
            $stackIndex  = null;
            
            if (is_array($settings)) {
                
                $settings = array_change_key_case($settings, CASE_LOWER);

                if (isset($settings[self::OPTIONS_KEYNAME])
                    && is_array($settings[self::OPTIONS_KEYNAME])
                ) {
                    $options = $settings[self::OPTIONS_KEYNAME];
                }

                if ( isset($settings[self::STACKINDEX_KEYNAME]) ) {
                    $stackIndex = $settings[self::STACKINDEX_KEYNAME];
                }
                
            } else {
                $settings = array(
                    self::CLASS_KEYNAME => $settings,
                );
            }

            $pluginClass = $settings[self::CLASS_KEYNAME];

            if ($options) {
                $plugin = new $pluginClass($options);
            } else {
                $plugin = new $pluginClass;
            }
            
            if (isset($settings[self::INJECT_KEYNAME])) {
                $this->_dependency->inject(
                    $plugin, $this->_dependencyResource,
                    $settings[self::INJECT_KEYNAME]
                );
            }

            if (method_exists($plugin, self::BOOTSTRAP_SETTER)) {
                $plugin->setBootstrap($this->getBootstrap());
            }

            $this->_frontController->registerPlugin($plugin, $stackIndex);
        }
    }

    /**
     * Register action helpers with options and injections
     *
     * example of options:
     *
     * - class                = Custom_ActionHelper
     * - options.customOption = optionValue
     *
     * @param array $actionHelpers
     */
    private function _actionHelpers(array $actionHelpers)
    {
        foreach ($actionHelpers as $settings) {

            $helperClass = null;
            $options     = null;

            if (is_array($settings)) {

                $settings = array_change_key_case($settings, CASE_LOWER);

                if (isset($settings[self::OPTIONS_KEYNAME])
                    && is_array($settings[self::OPTIONS_KEYNAME])
                ) {
                    $options = $settings[self::OPTIONS_KEYNAME];
                }
                
            } else {
                $settings = array(
                    self::CLASS_KEYNAME => $settings,
                );
            }

            $helperClass = $settings[self::CLASS_KEYNAME];

            if ($options) {
                $helper = new $helperClass($options);
            } else {
                $helper = new $helperClass;
            }

            if (isset($settings[self::INJECT_KEYNAME])) {
                $this->_dependency->inject(
                    $helper, $this->_dependencyResource,
                    $settings[self::INJECT_KEYNAME]
                );
            }

            if (method_exists($helper, self::BOOTSTRAP_SETTER)) {
                $helper->setBootstrap($this->getBootstrap());
            }

            ActionHelperBroker::addHelper(
                $helper
            );
        }
    }
}
