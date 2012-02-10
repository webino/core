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

use Zend_Controller_Request_Abstract as Request;
use Webino_Resource_Events           as Events;

/**
 * Controller plugin for events resource
 *
 * @category   Webino
 * @package    Core
 * @subpackage Events
 * @author     Peter Bačinský <peter@bacinsky.sk>
 * @copyright  2012 Peter Bačinský (http://www.bacinsky.sk/)
 * @license    http://www.webino.org/license/ New BSD License
 * @version    Release: @@PACKAGE_VERSION@@
 * @link       http://pear.webino.org/core/
 */
class Webino_ControllerPlugin_Events
    extends Zend_Controller_Plugin_Abstract
{
    /**
     * Name of start up event option key
     */
    const STARTUP_KEYNAME = 'startUp';
    
    /**
     * Name of dispatch start up event option key
     */
    const DISPATCHSTARTUP_KEYNAME = 'dispatchStartUp';
    
    /**
     * Name of pre dispatch event option key
     */
    const PREDISPATCH_KEYNAME = 'preDispatch';

    /**
     * Name of post dispatch event option key
     */
    const POSTDISPATCH_KEYNAME = 'postDispatch';

    /**
     * Name of shut down event option key
     */
    const SHUTDOWN_KEYNAME = 'shutDown';

    /**
     * Resource for event handling
     *
     * @var Events
     */
    private $_events;

    /**
     * Inject events
     *
     * @param Events $events
     *
     * @return Webino_ControllerPlugin_Events
     */
    public function setEvents(Events $events)
    {
        $this->_events = $events;

        return $this;
    }
    
    /**
     * Called before Zend_Controller_Front begins evaluating the
     * request against its routes.
     *
     * @param Request $request
     * 
     * @return void
     */
    public function routeStartup(Request $request)
    {
        $this->_events->fire(
            self::STARTUP_KEYNAME, array()
        );

        $this->getResponse()->setHeader('X-Engine', 'Webino.org', true);
    }

    /**
     * Called before Zend_Controller_Front enters its dispatch loop.
     *
     * @param  Request $request
     * 
     * @return void
     */
    public function dispatchLoopStartup(Request $request)
    {
        $this->_events->fire(
            self::DISPATCHSTARTUP_KEYNAME, array()
        );
    }

    /**
     * Called before an action is dispatched by Zend_Controller_Dispatcher.
     *
     * This callback allows for proxy or filter behavior.  By altering the
     * request and resetting its dispatched flag (via
     * {@link Request::setDispatched() setDispatched(false)}),
     * the current action may be skipped.
     *
     * @param  Request $request
     *
     * @return void
     */
    public function preDispatch(Request $request)
    {
        $this->_events->fire(
            self::PREDISPATCH_KEYNAME, array()
        );
    }

    /**
     * Called after an action is dispatched by Zend_Controller_Dispatcher.
     *
     * This callback allows for proxy or filter behavior. By altering the
     * request and resetting its dispatched flag (via
     * {@link Request::setDispatched() setDispatched(false)}),
     * a new action may be specified for dispatching.
     *
     * @param  Request $request
     *
     * @return void
     */
    public function postDispatch(Request $request)
    {
        $this->_events->fire(
            self::POSTDISPATCH_KEYNAME, array()
        );
    }

    /**
     * Called before Zend_Controller_Front exits its dispatch loop.
     *
     * @return void
     */
    public function dispatchLoopShutdown()
    {
        $this->_events->fire(
            self::SHUTDOWN_KEYNAME, array()
        );
    }
}
