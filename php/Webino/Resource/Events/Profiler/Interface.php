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
 * Interface of events profiler
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
interface Webino_Resource_Events_Profiler_Interface
{
    /**
     * Start profile
     *
     * @param array  $events
     * @param string $eventName
     * @param array  $params
     */
    public function start(
        array $events = array(), $eventName = null, array $params = array()
    );

    /**
     * Add event profile log
     *
     * @param string $observerName
     *
     * @return Webino_Resource_Events_Profiler
     */
    public function add($observerName);

    /**
     * Add profile log to events group
     *
     * @return Webino_Resource_Events_Profiler
     */
    public function debugGroup();

    /**
     * Return events overall time
     *
     * @return float
     */
    public function getTime();
}
