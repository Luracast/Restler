<?php
namespace Luracast\Restler\UI;

use Luracast\Restler\UI\Tags as T;

class Emmet
{
    const DELIMITERS = '.#*>+^[=" ]{}$';

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
        $parseAttributes = function (Callable $self) use (& $tokens, & $tag) {
            $a = array_shift($tokens);
            if ('=' == ($v = array_shift($tokens))) {
                //value
                if ('"' == ($v = array_shift($tokens))) {
                    $va = '';
                    while ('"' != ($v = array_shift($tokens))) {
                        $va .= $v;
                    }
                    $tag->$a($va);
                } else {
                    $va = $v;
                    while (' ' != ($v = array_shift($tokens)) && ']' != $v) {
                        $va .= $v;
                    }
                    $tag->$a($va);
                }
                if (']' == $v) {
                    //end
                    return;
                } elseif (' ' == $v) {
                    $self($self);
                    return;
                }
            } elseif (']' == $v) {
                //end
                $tag->$a('');
                return;
            } elseif (' ' == $v) {
                $tag->$a('');
                $self($self);
                return;
            }
        };
        while ($tokens) {
            switch (array_shift($tokens)) {
                //attributes
                case '.':
                    $e = $tag->class;
                    $tag->class(
                        empty($e)
                            ? array_shift($tokens)
                            : $e . ' ' . array_shift($tokens)
                    );
                    break;
                case '#':
                    $tag->id(array_shift($tokens));
                    break;
                case '[':
                    $parseAttributes($parseAttributes);
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
        $r = array();
        $f = strtok($string, static::DELIMITERS);
        $pos = 0;
        do {
            $start = $pos;
            $pos = strpos($string, $f, $start);
            $tokens = array();
            for ($i = $start; $i < $pos; $i++) {
                $token = $string{$i};
                if ('.' == $token && (!empty($tokens) || $i == 0)) {
                    $r[] = 'div';
                }
                $r[] = $tokens[] = $token;
            }
            $pos += strlen($f);
            $r[] = $f;
        } while (false != ($f = strtok(static::DELIMITERS)));
        for ($i = $pos; $i < strlen($string); $i++) {
            $token = $string{$i};
            $r[] = $tokens[] = $token;
        }
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