<?php
/**
 * Unit test class for VariableCommentSniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   CVS: $Id: VariableCommentUnitTest.php 302088 2010-08-11 01:52:10Z squiz $
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Unit test class for VariableCommentSniff.
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
class Squiz_Tests_Commenting_VariableCommentUnitTest extends AbstractSniffUnitTest
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
                6   => 1,
                8   => 1,
                21  => 1,
                24  => 1,
                28  => 1,
                38  => 1,
                41  => 1,
                53  => 1,
                56  => 1,
                63  => 1,
                64  => 1,
                69  => 2,
                73  => 1,
                81  => 1,
                82  => 1,
                84  => 1,
                90  => 1,
                92  => 1,
                128 => 1,
                137 => 1,
                145 => 1,
                153 => 1,
                158 => 1,
                159 => 1,
                163 => 1,
                178 => 1,
                180 => 1,
                184 => 1,
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
                93 => 1,
               );

    }//end getWarningList()


}//end class

?>