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

use Webino_Resource_Dependency_Injector_Interface as Dependency;
use Webino_Resource_Events_Interface              as Events;

/**
 * Action helper for firing events
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

class Webino_ActionHelper_Events
    extends Zend_Controller_Action_Helper_Abstract
{
    /**
     * Dependency injector
     *
     * @var Dependency
     */
    private $_dependency;

    /**
     * Events resource
     *
     * @var Events
     */
    private $_events;

    /**
     * Dependency injection name for event handlera
     */
    const INJECTION_NAME = 'controller';

    /**
     * Dependency injector
     *
     * @param Dependency $dependency
     *
     * @return Webino_PageController
     */
    public function setDependency(Dependency $dependency)
    {
        $this->_dependency = $dependency;

        return $this;
    }

    /**
     * Events resource injector
     *
     * @param Events $events
     *
     * @return Webino_PageController
     */
    public function setEvents(Events $events)
    {
        $this->_events = $events;

        return $this;
    }

    /**
     * Fire an event
     *
     * @param string $eventName
     * @param array $params
     *
     * @return Webino_ActionHelper_Events
     */
    public function fire($eventName, array $params)
    {
        $this->_events->fire($eventName, $params);

        return $this;
    }

    /**
     * Fire events from options
     *
     * @param array $options
     *
     * @return Webino_ActionHelper_Events
     */
    public function trigger(array $options)
    {
        foreach ( $options as $eventName => $options ) {
            $params = array();

            if (!empty($options)) {
                $params = $this->_dependency->injection(
                    $this->getActionController(), array(
                        self::INJECTION_NAME => $options,
                    )
                );
            }

            $this->_events->fire($eventName, $params);
        }

        return $this;
    }
}
