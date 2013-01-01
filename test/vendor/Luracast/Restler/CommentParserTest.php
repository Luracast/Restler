<?php
namespace Luracast\Restler;

/**
 * @covers Luracast\Restler\CommentParser
 */
class CommentParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CommentParser local instance
     */
    protected $object;

    /**
     * Setting the stage for our unit tests.
     */
    protected function setUp()
    {
        $this->object = new CommentParser();
    }

    /**
     * This is where we clean up after testing, if necessary.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Luracast\Restler\CommentParser::parse
     */
    public function test_class_CommentParser_method_parse()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
        $comment = $isPhpDoc = null;
        $this->object->parse($comment, $isPhpDoc);
    }

    /**
     * @covers Luracast\Restler\CommentParser::removeCommentTags
     */
    public function test_class_CommentParser_method_removeCommentTags()
    {
        $comment = null;
        $this->object->removeCommentTags($comment);
    }
}

