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
 * @subpackage Events
 * @author     Peter Bačinský <peter@bacinsky.sk>
 * @copyright  2012 Peter Bačinský (http://www.bacinsky.sk/)
 * @license    http://www.webino.org/license/ New BSD License
 * @version    GIT: $Id$
 * @link       http://pear.webino.org/core/
 */

use Zend_Wildfire_Channel_HttpHeaders             as Wildfire;
use Webino_Resource_Profiler_Interface            as Profiler;
use Webino_ProfilerPlugin_Interface               as Plugin;
use Webino_Resource_Dependency_Interface          as Dependency;
use Webino_Resource_Dependency_Injector_Interface as DependencyInjector;

/**
 * Controller plugin for events resource
 *
 * @category   Webino
 * @package    Core
 * @subpackage Events
 * @author     Peter Bačinský <peter@bacinsky.sk>
 * @copyright  2012 Peter Bačinský (http://www.bacinsky.sk/)
 * @license    http://www.webino.org/license/ New BSD License
 * @version    Release: 0.1.0alpha
 * @link       http://pear.webino.org/core/
 */
class Webino_ControllerPlugin_Profiler
    extends Zend_Controller_Plugin_Abstract
{
    /**
     * Name of plugin class name option key
     */
    const CLASS_KEYNAME = 'class';

    /**
     * Name of stack sequence number option key
     */
    const STACKINDEX_KEYNAME = 'stackIndex';

    /**
     * Name of inject options key
     */
    const INJECT_KEYNAME = 'inject';

    /**
     * Name of plugins options key
     */
    const PLUGINS_KEYNAME = 'plugins';

    /**
     * Plugin options
     *
     * @var array
     */
    private $_options;

    /**
     * Profiler resource
     *
     * @var Profiler
     */
    private $_profiler;

    /**
     * Dependency resource
     *
     * @var Dependency
     */
    private $_dependencyResource;

    /**
     * Dependency injector
     *
     * @var DependencyInjector
     */
    private $_dependency;

    /**
     * Constructor with options
     *
     * @param array $options
     */
    public function __construct(array $options)
    {
        $this->_options = $options;
    }

    /**
     * Inject profiler
     *
     * @param Profiler $profiler
     *
     * @return Webino_ControllerPlugin_Profiler
     */
    public function setProfiler(Profiler $profiler)
    {
        $this->_profiler = $profiler;

        return $this;
    }

    /**
     * Inject dependency resource
     *
     * @param Dependency $dependency
     *
     * @return Webino_ControllerPlugin_Profiler
     */
    public function setDependencyResource(Dependency $dependency)
    {
        $this->_dependencyResource = $dependency;

        return $this;
    }

    /**
     * Inject dependency injector
     *
     * @param DependencyInjector $dependency
     *
     * @return Webino_Resource_Events
     */
    public function setDependency(DependencyInjector $dependency)
    {
        $this->_dependency = $dependency;

        return $this;
    }

    /**
     * Called before Zend_Controller_Front exits its dispatch loop.
     *
     * @return void
     */
    public function dispatchLoopShutdown()
    {
        foreach ($this->_plugins() as $options) {

            if (is_object($options[self::CLASS_KEYNAME])) {
                $plugin = $options[self::CLASS_KEYNAME];
            } else {
                $plugin = new $options[self::CLASS_KEYNAME];
            }

            if (!($plugin instanceof Plugin)) {
                throw new InvalidArgumentException(
                    sprintf(
                        'Profiler plugin "%s" must implements interface "%s"',
                        get_class($plugin), 'Webino_ProfilerPlugin_Interface'
                    )
                );
            }

            if (!empty($options[self::INJECT_KEYNAME])) {
                $this->_dependency->inject(
                    $plugin, $this->_dependencyResource,
                    $options[self::INJECT_KEYNAME]
                );
            }

            $plugin->init($this->_profiler, $options);

        }

        Wildfire::getInstance()->flush();
    }

    /**
     * Return array of plugins
     *
     * @return array
     */
    private function _plugins()
    {
        $plugins = array();

        foreach ($this->_options[self::PLUGINS_KEYNAME] as $options) {

            if (!isset($options[self::STACKINDEX_KEYNAME])) {
                $stackIndex = count($plugins) + 100;
            } else {
                $stackIndex = $options[self::STACKINDEX_KEYNAME];
            }

            while (isset($plugins[$stackIndex])) {
                $stackIndex+= 100;
            }

            $plugins[$stackIndex] = $options;
        }

        ksort($plugins);

        return $plugins;
    }
}
