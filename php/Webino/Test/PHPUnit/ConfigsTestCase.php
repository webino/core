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
 * @subpackage TestCase
 * @author     Peter Bačinský <peter@bacinsky.sk>
 * @copyright  2012 Peter Bačinský (http://www.bacinsky.sk/)
 * @license    http://www.webino.org/license/ New BSD License
 * @version    GIT: $Id$
 * @link       http://pear.webino.org/core/
 */

use Zend_Config_Ini as Config;

/**
 * Test case for configs testing
 *
 * @category   Webino
 * @package    Core
 * @subpackage TestCase
 * @author     Peter Bačinský <peter@bacinsky.sk>
 * @copyright  2012 Peter Bačinský (http://www.bacinsky.sk/)
 * @license    http://www.webino.org/license/ New BSD License
 * @version    Release: @@PACKAGE_VERSION@@
 * @link       http://pear.webino.org/core/
 */
abstract class Webino_Test_PHPUnit_ConfigsTestCase
    extends PHPUnit_Framework_TestCase
{
    /**
     * Config sections to test
     *
     * @var array
     */
    protected $_configSections;

    /**
     * List of configuration files to test
     *
     * @var array
     */
    protected $_configFiles;

    /**
     * Path to configs directory
     *
     * @var string
     */
    protected $_dir;

    /**
     * Key of config to test assertions
     *
     * @var string
     */
    private $_configToTest;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->_configSections = array(
            'common',
            'development',
            'testing',
            'production',
            'staging',
        );
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {

    }

    /**
     * Inject array of filepaths to configs
     *
     * @param array $configFiles
     *
     * @return Webino_Test_PHPUnit_ConfigsTestCase
     */
    public function setConfigFiles(array $configFiles)
    {
        $this->_configFiles = $configFiles;

        return $this;
    }

    /**
     * Inject array of config sections to check
     *
     * @param array $configSections
     *
     * @return Webino_Test_PHPUnit_ConfigsTestCase
     */
    public function setConfigSections(array $configSections)
    {
        $this->_configSections = $configSections;

        return $this;
    }

    /**
     * Extract directives from configs with sections
     *
     * @param bool $configKey
     *
     * @return array
     */
    private function _directives($configKey)
    {
        $directives = array();
        $section    = null;

        foreach ($this->_configFiles as $key=>$filepath) {

            if ($key != $configKey) {
                continue;
            }

            foreach (file($filepath) as $line) {

                $line = trim($line);

                if (!$line) {
                    continue;
                }

                if ('[' == $line[0]) {
                    $section = substr($line, 1, strlen($line)-2);
                    $directives[$section] = array();
                } else {
                    $directives[$section][] = preg_replace('~ +~', ' ', $line);
                }
            }
        }

        return $directives;
    }

    /**
     * Set config key to test
     *
     * @param string $configKey
     */
    public function setConfigToTest($configKey)
    {
        $this->_configToTest = $configKey;

        return $this;
    }

    /**
     * Asserting config directive
     *
     * @param string $section
     * @param string $directive
     *
     * @throws LogicException
     *
     * @return bool
     */
    public function assertConfigDirective($section, $directive)
    {
        if (!$this->_configToTest) {
            throw new LogicException('You should call setConfigToTest() first');
        }

        $directives = $this->_directives($this->_configToTest);

        return $this->assertContains(
            $directive, $directives[$section], 'assertConfigDirective failed'
        );
    }

    /**
     * Generate assertions from config
     *
     * @param string $configKey Key of config to generate from
     * @param string $filepath  Path to save generated code
     *
     * @throws RuntimeException
     */
    public function generateAssertions($configKey, $filepath)
    {
        $directives = $this->_directives($configKey);

        $assertions = array();

        foreach ($directives as $section=>$sectionDirectives) {
            foreach ($sectionDirectives as $directive) {

                $directive = trim($directive);

                if (';' == $directive[0]) {
                    
                    continue;
                }

                $assertions[] = sprintf(
                    '$this->assertConfigDirective(%s\'%s\', \'%s\'%s);%s',
                    PHP_EOL . str_repeat(' ', 4), $section,
                    str_replace("'", "\'", $directive), PHP_EOL, PHP_EOL
                );

            }
        }

        file_put_contents($filepath, join(PHP_EOL, $assertions));

        throw new RuntimeException(
            sprintf(
                'Assertions for config "%s" was saved to "%s"',
                $configKey, $filepath
            )
        );
    }

    /**
     * Test if config could be loaded and have all required sections
     */
    public function testConfigsSections()
    {
        foreach ($this->_configFiles as $config) {

            foreach ($this->_configSections as $section) {
                new Config($config, $section);
            }
        }
    }
}
