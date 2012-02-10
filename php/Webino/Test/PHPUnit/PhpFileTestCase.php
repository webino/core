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

/**
 * Test case for testing php files
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
abstract class Webino_Test_PHPUnit_PhpFileTestCase
    extends PHPUnit_Framework_TestCase
{
    /**
     * Path to tested file
     *
     * @var string
     */
    protected $_filepath;

    /**
     * File code for testing
     *
     * @var string
     */
    protected $_code;

    /**
     * PHP code lines
     *
     * @var string
     */
    private $_codeLines;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->_code = trim(php_strip_whitespace($this->_filepath));
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {

    }

    /**
     * Inject path to PHP file
     *
     * @param string $filepath Path to PHP file
     *
     * @return Webino_Test_PHPUnit_PhpFileTestCase
     */
    public function setFilepath($filepath)
    {
        $this->_filepath = $filepath;

        return $this;
    }

    /**
     * Return code lines from code
     *
     * @return array
     */
    protected function _codeLines()
    {
        if ($this->_codeLines) {
            return $this->_codeLines;
        }
        
        $lines = explode(';', $this->_code);
        foreach ($lines as $key=>&$line) {
            $line = trim($line);

            if (!$line) {
                unset($lines[$key]);
            }
        }

        return $this->_codeLines = $lines;
    }

    /**
     * Assert if code lines contains statement
     *
     * Statement in this case is everything from ; to ;
     *
     * @param string $statement PHP code statement
     */
    public function assertPhpStatement($statement)
    {
        $this->assertContains(
            $statement, $this->_codeLines(), 'assertPhpStatement failed'
        );
    }

    /**
     * Assert PHP file against pattern
     *
     * @param string $filepath Path to match file
     */
    public function assertMatchFile($filepath)
    {
        $this->assertStringEqualsFile(
            $filepath, $this->_code, 'assertMatchFile failed'
        );
    }

    /**
     * Generate assertions for PHP file
     *
     * @param string $filepath Path to save assertions code
     */
    public function generateAssertions($filepath)
    {
        $assertions = array();

        foreach ($this->_codeLines() as $line) {
            $line = str_replace(
                '<?php' . PHP_EOL, "<?php' . PHP_EOL . '",
                str_replace('\'', "\'", $line)
            );

            $assertions[] = sprintf(
                '$this->assertPhpStatement(%s\'%s\'%s);%s',
                PHP_EOL . str_repeat(' ', 4), $line, PHP_EOL, PHP_EOL
            );
        }

        file_put_contents($filepath, join(PHP_EOL, $assertions));

        throw new RuntimeException(
            sprintf(
                'Assertions for file "%s" was saved to "%s"',
                $this->_filepath, $filepath
            )
        );
    }

    /**
     * Generate match pattern for PHP file
     *
     * @param string $filepath Path to save match code
     */
    public function generateMatchFile($filepath)
    {
        file_put_contents($filepath, $this->_code);

        throw new RuntimeException(
            sprintf(
                'Match file for "%s" was saved to "%s"',
                $this->_filepath, $filepath
            )
        );
    }
}
