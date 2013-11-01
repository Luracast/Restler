<?php
namespace Luracast\Restler\UI;

use Luracast\Restler\UI\Tags as T;

class Emmet
{
    const DELIMITERS = '.#*>+^[]{}$';

    /**
     * @param $string
     *
     * @return array|T
     */
    public static function make($string)
    {
        if (!strlen($string))
            return array();

        $tokens = static::tokenize($string);

        $tag = new T(array_shift($tokens));
        $root = new T;
        $root[] = $tag;
        while ($tokens) {
            switch (array_shift($tokens)) {
                //attributes
                case '.':
                    $tag->class(array_shift($tokens));
                    break;
                case '#':
                    $tag->id(array_shift($tokens));
                    break;
                //child
                case '{':
                    $tag[] = array_shift($tokens);
                    $t = array_shift($tokens);
                    //TODO: see if $t is not `}`
                    break;
                case '>':
                    $child = new T();
                    $tag[] = $child;
                    $tag = $child;
                    break;
                //sibling
                case '+':
                    if ('{' == ($t = array_shift($tokens))) {
                        $tag = $tag->parent;
                        $tag[] = array_shift($tokens);
                        array_shift($tokens);
                    } else {
                        $child = new T($t);
                        $tag = $tag->parent;
                        $tag[] = $child;
                        $tag = $child;
                    }
                    break;
                //sibling of parent
                case '^':
                    $tag = $tag->parent->parent;
                    while ('^' == ($t = array_shift($tokens))) {
                        if ($tag->parent)
                            $tag = $tag->parent;
                    }
                    $child = new T($t);
                    $tag[] = $child;
                    $tag = $child;
                    break;
                //clone
                case '*':
                    $times = array_shift($tokens);
                    $parent = $tag->parent;
                    for ($i = 1; $i < $times; $i++) {
                        $parent[] = $tag;
                    }
                    break;
            }
        }
        return $root;
    }

    public static function tokenize($string)
    {
        $token = '.#*>^[]{}$';
        $r = array();
        $f = strtok($string, static::DELIMITERS);
        $pos = 0;
        do {
            $start = $pos;
            $pos = strpos($string, $f, $start);
            $tokens = array();
            for ($i = $start; $i < $pos; $i++) {
                $token = $string{$i};
                if (!empty($token) && '.' == $token) {
                    $r[] = 'div';
                }
                $r[] = $tokens[] = $token;
            }
            $pos += strlen($f);
            $r[] = $f;
        } while (false != ($f = strtok(static::DELIMITERS)));
        return $r;
        /* sample output produced by ".row*3>.col*3"
        [0] => div
        [1] => .
        [2] => row
        [3] => *
        [4] => 3
        [5] => >
        [6] => div
        [7] => .
        [8] => col
        [9] => *
        [10] => 4
         */
    }
}