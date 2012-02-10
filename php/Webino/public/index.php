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
 * File init.php must be included before this file,
 * because it defines base constants and creates the application object.
 *
 * This file defines application constants, load and cache options,
 * and bootstraps the application, but not run.
 *
 * @category   Webino
 * @package    Core
 * @subpackage public
 * @author     Peter Bačinský <peter@bacinsky.sk>
 * @copyright  2012 Peter Bačinský (http://www.bacinsky.sk/)
 * @license    http://www.webino.org/license/ New BSD License
 * @version    GIT: $Id$
 * @version    Release: @@PACKAGE_VERSION@@
 * @link       http://pear.webino.org/core/
 */

use Zend_Cache      as Cache;
use Zend_Config_Ini as Config;

/**
 * Defined constants bellow are intended to use in INI configs and this file.
 * Any class shouldn't depend on global constants.
 */

/**
 * Application dir
 */
defined('APPLICATION_PATH')
    || define(
        'APPLICATION_PATH', realpath(
            dirname($_SERVER['SCRIPT_FILENAME']) . '/../application'
        )
    );

/**
 * Application library path
 */
defined('APPLICATION_LIB')
    || define(
        'APPLICATION_LIB', realpath(
            APPLICATION_PATH . '/../library'
        )
    );

/**
 * Application configs dir
 */
defined('APPLICATION_CONFIGS')
    || define(
        'APPLICATION_CONFIGS', APPLICATION_PATH . '/configs'
    );

/**
 * Application cache dir
 */
defined('APPLICATION_CACHE')
    || define(
        'APPLICATION_CACHE', APPLICATION_PATH . '/../data/cache'
    );

/**
 * Define constants from config
 */
if (is_file(APPLICATION_CONFIGS . '/constants.ini')) {

    $cfg = new Config(
        APPLICATION_CONFIGS . '/constants.ini', APPLICATION_ENV
    );

    foreach ($cfg->constants as $name=>$value) {
        defined($name)
            or define($name, $value);
    }

    unset($cfg);
}

/**
 * Caching configs
 */
$autoloadConfigs = array_merge(
    glob(APPLICATION_CONFIGS . '/*.ini'),
    glob(APPLICATION_CONFIGS . '/*/*.ini')
);

if (!empty($autoloadConfigs)) {

    $cache = Cache::factory(
        'File', 'File',
        array(
           'master_files'            => $autoloadConfigs,
           'automatic_serialization' => true,
        ),
        array('cache_dir' => APPLICATION_CACHE)
    );

    unset($autoloadConfigs);

    if (!$options = $cache->load('app_options')) {

        $cfg = new Config(
            APPLICATION_CONFIGS . '/application.ini', APPLICATION_ENV
        );

        $options = $application->setOptions($cfg->toArray())->getOptions();
        unset($options['config']);

        $cache->save($options);

        unset($cfg);
    }

    unset($cache);

    $application->setOptions($options);

    unset($options);
}

/**
 * Set options and bootstrap the application
 */
$application->bootstrap();
