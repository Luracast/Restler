<?php declare(strict_types=1);

use Luracast\Restler\Utils\CommentParser;

include __DIR__ . "/../vendor/autoload.php";


class Book
{

    /**
     * @var integer Status: 1 - published (displayed), 0 - not published, -1 - deleted
     */
    public $status;
}

try {
    $p = new ReflectionProperty(Book::class, 'status');
    //make sure the comment text is not lost
    echo json_encode(CommentParser::parse($p->getDocComment()), JSON_PRETTY_PRINT);
} catch (Exception $e) {
    echo $e->getMessage();
}

