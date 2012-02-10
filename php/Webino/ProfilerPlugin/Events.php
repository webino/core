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
 * @subpackage ProfilerPlugin
 * @author     Peter Bačinský <peter@bacinsky.sk>
 * @copyright  2012 Peter Bačinský (http://www.bacinsky.sk/)
 * @license    http://www.webino.org/license/ New BSD License
 * @version    GIT: $Id$
 * @link       http://pear.webino.org/core/
 */

use Webino_Resource_Events_Profiler as EventsProfiler;

/**
 * Events plugin for profiler
 *
 * Add events info to profile log.
 *
 * @category   Webino
 * @package    Core
 * @subpackage ProfilerPlugin
 * @author     Peter Bačinský <peter@bacinsky.sk>
 * @copyright  2012 Peter Bačinský (http://www.bacinsky.sk/)
 * @license    http://www.webino.org/license/ New BSD License
 * @version    Release: @@PACKAGE_VERSION@@
 * @link       http://pear.webino.org/core/
 */
class Webino_ProfilerPlugin_Events
    extends Webino_ProfilerPlugin_Abstract
{
    /**
     * Placeholder for time value in debug messages
     */
    const TIME_PLACEHOLDER = '{$time}';

    /**
     * Time unit
     */
    const TIME_UNIT = 'ms';

    /**
     * Unit multiplier to scale time info
     */
    const TIME_UNITMULTIPLIER = 1000;
    
    /**
     * Inject profiler
     *
     * @param DrawProfiler $profiler
     *
     * @return Webino_ProfilerPlugin_Draw
     */
    public function setProfiler(EventsProfiler $profiler)
    {
        parent::_setProfiler($profiler);

        return $this;
    }

    /**
     * Returns group title
     *
     * @param string $message
     *
     * @return string
     */
    protected function _groupTitle($message)
    {
        return strtr(
            $message, array(
                self::TIME_PLACEHOLDER => round(
                    $this->_profiler->getTime() * self::TIME_UNITMULTIPLIER, 3
                ) . self::TIME_UNIT
            )
        );
    }
}
