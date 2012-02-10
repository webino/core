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
 * Abstract class for profilers
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
abstract class Webino_Resource_Profiler_Abstract
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
     * Array of time
     *
     * @var array
     */
    protected $_time = array();

    /**
     * Is true if profiler is in group
     *
     * @var bool
     */
    protected $_inGroup;

    /**
     * Array of profile logs
     *
     * @var array
     */
    protected $_profile = array();

    /**
     * Return start time
     *
     * @return int
     */
    private function _timeStart()
    {
        $time = microtime(true) - end($this->_time);
        array_pop($this->_time);

        return $time;
    }

    /**
     * Add log to profile array
     *
     * @param string $key
     * @param array $options
     *
     * @return Webino_Resource_Profiler_Abstract 
     */
    private function _profile($key, array $options)
    {
        if ( !isset($this->_profile[$key]) ) {
            $this->_profile[$key] = array();
        }

        $this->_profile[$key][] = $options;

        return $this;
    }

    /**
     * Return microtime
     *
     * @return float
     */
    protected function _start()
    {
        return $this->_time[] = microtime(true);
    }

    /**
     * Stop profile
     *
     * @param string  $message Message with time variable
     *
     * @return string Message with replaced time
     */
    protected function _stop($message = null)
    {
        if ($this->_inGroup) {
            $this->_profile('default', array('groupEnd' => array()));
            $this->_inGroup = false;
        }

        return strtr(
            $message, array(
                self::TIME_PLACEHOLDER => round(
                    $this->_timeStart() * self::TIME_UNITMULTIPLIER, 3
                ) . self::TIME_UNIT,
            )
        );
    }

    /**
     * Profile debug log
     *
     * @param string $message
     * @param mixed  $var
     */
    protected function _debug($message, $var)
    {
        $this->_profile(
            'default', array(
                'send' => array(
                    $var,
                    strtr(
                        $message, array(
                            self::TIME_PLACEHOLDER => round(
                                $this->_timeStart() * self::TIME_UNITMULTIPLIER,
                                3
                            ) . self::TIME_UNIT,
                        )
                    ), null
                ),
            )
        );
    }
    
    /**
     * Add start profile group
     *
     * @param string $title
     */
    protected function _startGroup($title)
    {
        $this->_inGroup = true;
        $this->_time[]  = microtime(true);

        $this->_profile(
            'default', array(
                'group' => array($title),
            )
        );
    }

    /**
     * Add group profile log
     *
     * @param string $key      Group key
     * @param string $message
     * @param mixed  $var
     *
     * @return float Time
     */
    protected function _debugGroup($key, $message, $var)
    {
        $time = $this->_timeStart();

        $translation = array(
            self::TIME_PLACEHOLDER => round(
                $time * self::TIME_UNITMULTIPLIER, 3
            ) . self::TIME_UNIT,
        );

        $this->_profile(
            $key, array(
                'send' => array($var, strtr($message, $translation), null),
            )
        );

        return $time;
    }

    /**
     * Return profile data
     *
     * @return array
     */
    public function getProfile()
    {
        return $this->_profile;
    }    
}
