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

/**
 * Profiler for events resource
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
class Webino_Resource_Events_Profiler 
    extends    Webino_Resource_Profiler_Abstract
    implements Webino_Resource_Events_Profiler_Interface
{
    /**
     * Event time
     *
     * @var float
     */
    private $_eventTime = 0;

    /**
     * Events list
     *
     * @var array
     */
    private $_events;

    /**
     * Name of event
     *
     * @var string
     */
    private $_eventName;

    /**
     * Event params
     *
     * @var array
     */
    private $_eventParams;

    /**
     * Array of event profile log
     *
     * @var array
     */
    private $_eventProfile = array();

    /**
     * Start profile
     *
     * @param array  $events
     * @param string $eventName
     * @param array  $params
     */
    public function start(
        array $events = array(), $eventName = null, array $params = array()
    )
    {
        $this->_events      = $events;
        $this->_eventName   = $eventName;
        $this->_eventParams = $params;

        if ($this->_eventName) {
            $this->_eventProfile[$this->_eventName] = $this->_eventParams;
        }

        $this->_start();
    }

    /**
     * Add event profile log
     *
     * @param string $observerName
     *
     * @return Webino_Resource_Events_Profiler
     */
    public function add($observerName)
    {
        $passedParams = array();
        $options      = $this->_events[$observerName];

        foreach ($this->_eventParams as $param) {

            if (is_object($param)) {
                $param = get_class($param);
            }

            $passedParams[] = $param;
        }

        if (!isset($options[$this->_eventName])) {

            $this->_eventProfile[$this->_eventName] = array(
                null, 'params' => $passedParams
            );

            return null;
        }

        $this->_eventProfile[$this->_eventName] = array(
            $options[$this->_eventName], 'params' => $passedParams
        );
    }

    /**
     * Add profile log to events group
     *
     * @return Webino_Resource_Events_Profiler
     */
    public function debugGroup()
    {
        $this->_eventTime+= $this->_debugGroup(
            'Fired events (%s in {$time}):', '{$time}', $this->_eventProfile
        );

        $this->_eventProfile = array();
    }

    /**
     * Return events overall time
     *
     * @return float
     */
    public function getTime()
    {
        return $this->_eventTime;
    }
}
