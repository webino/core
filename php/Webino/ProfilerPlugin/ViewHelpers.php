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

use Zend_View                          as View;
use Webino_Resource_Profiler_Interface as Profiler;

/**
 * ViewHelper plugin for profiler
 *
 * Add list of view helpers to profiler log.
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
class Webino_ProfilerPlugin_ViewHelpers
    implements Webino_ProfilerPlugin_Interface
{
    /**
     * View object
     *
     * @var View
     */
    private $_view;

    /**
     * Inject view object
     *
     * @param View $view
     *
     * @return Webino_ProfilerPlugin_ViewHelpers
     */
    public function setView(View $view)
    {
        $this->_view = $view;

        return $this;
    }

    /**
     * Initialize profiler plugin
     *
     * @param Profiler $profiler
     * @param array    $options
     */
    public function init(Profiler $profiler, array $options)
    {
        $helpers = $this->_view->getHelpers();

        $profiler->write(
            'group', sprintf('View Helpers (%s):', count($helpers))
        );
        
        foreach ($helpers as $object) {
            $profiler->write('send', get_class($object));
        }
        
        $profiler->write('groupEnd');
    }
}
