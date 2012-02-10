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
use Webino_Resource_Events_Profiler_Interface     as Profiler;

/**
 * Resource for events support
 *
 * example of config:
 *
 * - attach.example.object                        = Webino_EventHandler_Example
 * - attach.example.inject.bootstrap.resource.log = log
 * - handle.startUp.example                       = exampleHandlerMethod
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
class Webino_Resource_Events
    extends    Zend_Application_Resource_ResourceAbstract
    implements Webino_Resource_Events_Interface
{
    /**
     * Option name of attach
     */
    const ATTACH_KEYNAME = 'attach';

    /**
     * Option name of handle
     */
    const HANDLE_KEYNAME = 'handle';

    /**
     * Option name of options
     */
    const OPTIONS_KEYNAME = 'options';

    /**
     * Option name of class
     */
    const CLASS_KEYNAME = 'class';

    /**
     * Option name of inject
     */
    const INJECT_KEYNAME = 'inject';

    /**
     * List of event observers, handlerName = EventHandler handler
     *
     * @var  array
     */
    private $_observers = array();

    /**
     * List of events, handler => eventName = array methods
     *
     * @var array
     */
    private $_events = array();

    /**
     * Dependency resource
     *
     * @var Dependency
     */
    private $_dependency;

    /**
     * Profiler resource
     *
     * @var Profiler
     */
    private $_profiler;

    /**
     * Check options and initialize error handler
     */
    public function init()
    {
        if (isset($this->_options[self::ATTACH_KEYNAME])) {
            $this->_attach($this->_options[self::ATTACH_KEYNAME]);
        }

        if (isset($this->_options[self::HANDLE_KEYNAME])) {
            $this->_handle($this->_options[self::HANDLE_KEYNAME]);
        }

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
     * Inject profiler resource
     *
     * @param Profiler $profiler
     *
     * @return Webino_Resource_Events
     */
    public function setProfiler(Profiler $profiler = null)
    {
        $this->_profiler = $profiler;

        return $this;
    }

    /**
     * Attach observer object to events resource
     *
     * @param array $classNames
     *
     * @return Webino_Resource_Events
     */
    private function _attach(array $classNames)
    {
        foreach ($classNames as $name => $class) {
            if (is_array($class)) {

                if (isset($class[self::OPTIONS_KEYNAME])) {
                    $this->_observers[$name]= new $class[self::CLASS_KEYNAME]
                        ($class[self::OPTIONS_KEYNAME]);
                } else {
                    $this->_observers[$name] = new $class[self::CLASS_KEYNAME];
                }

                if (isset($class[self::INJECT_KEYNAME])) {
                    $this->_dependency->inject(
                        $this->_observers[$name], $this,
                        $class[self::INJECT_KEYNAME]
                    );
                }
            } elseif (is_object($class)) {
                $this->_observers[$name] = $class;

            } else {
                $this->_observers[$name] = new $class;
            }
        }

        return $this;
    }

    /**
     * Attach callback to event
     *
     * @param array $events
     *
     * @return Webino_Resource_Events
     */
    private function _handle(array $events)
    {
        foreach ($events as $eventName => $options) {

            foreach ($options as $key => $callback) {

                if (is_array($callback)) {
                    foreach ($callback as $index => $method) {
                            $this->_events[$key][$eventName][$index] = $method;
                    }

                    continue;
                }

                $this->_events[$key][$eventName][] = $callback;
            }
        }

        return $this;
    }

    /**
     * Fire an event and notify the observer
     *
     * @param string $eventName
     * @param array  $params
     *
     * @return Webino_Resource_Events
     */
    public function fire($eventName, array $params = array())
    {
        !$this->_profiler or
            $this->_profiler->start($this->_events, $eventName, $params);

        foreach ($this->_observers as $observerName => $observer) {

            !$this->_profiler or
                $this->_profiler->add($observerName);

            if (!isset($this->_events[$observerName][$eventName])) {

                continue;
            }

            foreach (
                $this->_events[$observerName][$eventName] as $method
            ) {
                call_user_func_array(array($observer, $method), $params);
            }
        }

        !$this->_profiler or
            $this->_profiler->debugGroup();

        return $this;
    }
}
