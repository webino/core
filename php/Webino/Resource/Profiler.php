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

use Webino_Resource_Profiler_Writer_Interface as Writer;

/**
 * Resource for profiling
 *
 * example of options:
 *
 * - writers.firebug       = Webino_Resource_Profiler_Writer_Firebug
 * - register.events.class = Webino_Resource_Events_Profiler
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
class Webino_Resource_Profiler
    extends    Zend_Application_Resource_ResourceAbstract
    implements Webino_Resource_Profiler_Interface
{
    /**
     * Name of profiler object option key
     */
    const OBJECT_KEYNAME = 'object';
    
    /**
     * Name of profiler class option key
     */
    const CLASS_KEYNAME = 'class';

    /**
     * List of profilers
     *
     * @var array
     */
    private $_register = array();

    /**
     * Initialize resource
     *
     * @return null
     */
    public function init()
    {
        return null;
    }

    /**
     * Set profilers to register
     *
     * @param array $options
     *
     * @return Webino_Resource_Profiler
     */
    public function setRegister(array $options)
    {
        $this->_register = $options;

        return $this;
    }

    /**
     * Set writers options
     *
     * @param array $writers
     *
     * @throws
     *
     * @return Webino_Resource_Profiler 
     */
    public function setWriters(array $writers)
    {
        foreach ($writers as $key => $writer) {

            if (is_string($writer)) {
                $writer = new $writer;
            }

            if (!($writer instanceof Writer)) {
                throw new InvalidArgumentException(
                    sprintf(
                        'Invalid writer "%s" for profiler!', get_class($writer)
                    )
                );
            }

            $this->_writers[$key] = $writer;
        }

        return $this;
    }

    /**
     * Return profiler
     *
     * @param string $name
     *
     * @return object
     */
    public function getProfiler($name)
    {
        if (!empty($this->_register[$name][self::OBJECT_KEYNAME])) {

            return $this->_register[$name][self::OBJECT_KEYNAME];
        }

        $this->_register[$name][self::OBJECT_KEYNAME]
             = new $this->_register[$name][self::CLASS_KEYNAME];

        return $this->_register[$name][self::OBJECT_KEYNAME];
    }

    /**
     * Write profile log
     *
     * @return Webino_Resource_Profiler 
     */
    public function write()
    {
        foreach ($this->_writers as $writer) {
            $writer->write(func_get_args());
        }

        return $this;
    }
}
