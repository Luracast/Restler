<?php
/**
 * Unit test class for FileCommentSniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   CVS: $Id: FileCommentUnitTest.php 302756 2010-08-25 05:12:37Z squiz $
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Unit test class for FileCommentSniff.
 *
 * Verifies that :
 * <ul>
 *  <li>A doc comment exists.</li>
 *  <li>Short description must start with a capital letter and end with a period.</li>
 *  <li>There must be one blank newline after the short description.</li>
 *  <li>A PHP version is specified.</li>
 *  <li>Check the order of the tags.</li>
 *  <li>Check the indentation of each tag.</li>
 *  <li>Check required and optional tags and the format of their content.</li>
 * </ul>
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   Release: 1.3.0
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Squiz_Tests_Commenting_FileCommentUnitTest extends AbstractSniffUnitTest
{

    /**
     * Returns the lines where errors should occur.
     *
     * The key of the array should represent the line number and the value
     * should represent the number of errors that should occur on that line.
     *
     * @return array(int => int)
     */
    public function getErrorList()
    {
        return array(
                1  => 1,
                6  => 3,
                8  => 1,
                10 => 1,
                21 => 1,
                22 => 2,
                23 => 1,
                24 => 2,
                26 => 1,
                27 => 2,
                30 => 2,
               );

    }//end getErrorList()

    /**
     * Returns the lines where warnings should occur.
     *
     * The key of the array should represent the line number and the value
     * should represent the number of warnings that should occur on that line.
     *
     * @return array(int => int)
     */
    public function getWarningList()
    {
        return array(
                28 => 1,
               );

    }//end getWarningList()

}//end class

?>

