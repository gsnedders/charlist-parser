<?php
// Call parserTest::main() if this source file is executed directly.
if (!defined('PHPUnit_MAIN_METHOD')) {
	define('PHPUnit_MAIN_METHOD', 'parserTest::main');
}

require_once 'PHPUnit/Framework.php';

require_once 'parser.php';

/**
 * Test class for parser.
 */
class parserTest extends PHPUnit_Framework_TestCase
{
	/**
	 * Runs the test methods of this class.
	 */
	public static function main()
	{
		require_once 'PHPUnit/TextUI/TestRunner.php';

		$suite	= new PHPUnit_Framework_TestSuite('parserTest');
		$result = PHPUnit_TextUI_TestRunner::run($suite);
	}
	
	/**
	 * Throw an exception for every error.
	 */
	public static function error_to_exception($errno, $errstr)
	{
		throw new Exception($errstr, $errno);
	}
	
	/**
	 * Setup the error -> exception error handler.
	 */
	protected function setUp()
	{
		if (!set_error_handler(array(get_class($this), 'error_to_exception')))
		{
			restore_error_handler();
		}
	}
	
	/**
	 * Restore the prior error handling.
	 */
	protected function tearDown()
	{
		restore_error_handler();
	}
	
	/**
	 * Valid, error-free, test data.
	 */
	public static function basicProvider()
	{
		return array(
			array('a', array('a')),
			array('a..b', array('a', 'b')),
			array('a..c', array('a', 'b', 'c')),
			array('a..a', array('a')),
			array('a..bc', array('a', 'b', 'c')),
			array('ab..c', array('a', 'b', 'c')),
			array('a..bc..d', array('a', 'b', 'c', 'd')),
			array('a..bcd..e', array('a', 'b', 'c', 'd', 'e')),
			array('-...', array('-', '.')),
			array('.../', array('.', '/')),
			array('....', array('.'))
			);
	}
 
	/**
	 * @dataProvider basicProvider
	 */
	public function testBasic($charlist, $expected)
	{
		$this->assertEquals(get_characters_in_charlist($charlist), $expected);
	}
	
	/**
	 * Invalid, error-full, test data.
	 */
	public static function errorProvider()
	{
		return array(
			array('..a', "Invalid '..'-range, no character to the left of '..'"),
			array('a..', "Invalid '..'-range, no character to the right of '..'"),
			array('b..a', "Invalid '..'-range, '..'-range needs to be incrementing"),
			array('a..b..c', "Invalid '..'-range"),
			);
	}
 
	/**
	 * @dataProvider errorProvider
	 */
	public function testError($charlist, $expected)
	{
		try
		{
			get_characters_in_charlist($charlist);
		}
		catch (Exception $e)
		{
			if ($e->getMessage() === $expected)
			{
				return;
			}
		}
		
		$this->fail('Expected error not thrown.');
	}
}

// Call parserTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == 'parserTest::main') {
	parserTest::main();
}
?>
