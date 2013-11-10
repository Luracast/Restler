<?php
namespace Luracast\Restler\UI;

use Luracast\Restler\UI\Tags as T;

class Emmet
{
    const DELIMITERS = '.#*>+^[=" ]{$@-}';

    /**
     * @param $string
     *
     * @return array|T
     */
    public static function make($string)
    {
        if (!strlen($string))
            return array();

        $implicitTag = function () use (& $tag) {
            if (empty($tag->tag)) {
                switch ($tag->parent->tag) {
                    case 'ul':
                    case 'ol':
                        $tag->tag = 'li';
                        break;
                    case 'em':
                        $tag->tag = 'span';
                        break;
                    case 'table':
                    case 'tbody':
                    case 'thead':
                    case 'tfoot':
                        $tag->tag = 'tr';
                        break;
                    case 'tr':
                        $tag->tag = 'td';
                        break;
                    case 'select':
                    case 'optgroup':
                        $tag->tag = 'option';
                        break;
                    default:
                        $tag->tag = 'div';
                }
            }
        };

        $parseClassOrId = function ($text, $round, $total) use (& $tokens, & $tag) {
            $digits = 0;
            $delimiter = array(
                '.' => true,
                '#' => true,
                '*' => true,
                '>' => true,
                '+' => true,
                '^' => true,
                '[' => true,
                '=' => true,
            );
            while (!empty($tokens) && !isset($delimiter[$t = array_shift($tokens)])) {
                while ('$' === $t) {
                    $digits++;
                    $t = array_shift($tokens);
                }
                if ($digits) {
                    $negative = false;
                    $offset = 0;
                    if ('@' == $t) {
                        if ('-' == ($t = array_shift($tokens))) {
                            $negative = true;
                            if (is_numeric(reset($tokens))) {
                                $offset = array_shift($tokens);
                            }
                        } elseif (is_numeric($t)) {
                            $offset = $t;
                        } else {
                            array_unshift($tokens, $t);
                        }
                    } else {
                        array_unshift($tokens, $t);
                    }
                    if ($negative) {
                        $n = $total + 1 - $round + $offset;
                    } else {
                        $n = $round + $offset;
                    }
                    $text .= sprintf("%0{$digits}d", $n);
                    $digits = 0;
                } else {
                    $text .= $t;
                }
            }
            array_unshift($tokens, $t);
            return $text;
        };

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

        $tokens = static::tokenize($string);
        $tag = new T(array_shift($tokens));
        $parent = $root = new T;
        $parse = function (Callable $self, $round = 1, $total = 1)
        use (
            & $tokens, & $parent, & $tag,
            $parseAttributes, $implicitTag, $parseClassOrId
        ) {
            $offsetTokens = null;
            $parent[] = $tag;
            while ($tokens) {
                switch (array_shift($tokens)) {
                    //attributes
                    case '.':
                        $offsetTokens = array_values($tokens);
                        array_unshift($offsetTokens, '.');
                        $implicitTag();
                        $e = $tag->class;
                        $text = empty($e) ? '' : "$e ";
                        $tag->class($parseClassOrId($text, $round, $total));
                        break;
                    case '#':
                        $offsetTokens = array_values($tokens);
                        array_unshift($offsetTokens, '#');
                        $implicitTag();
                        $tag->id($parseClassOrId(array_shift($tokens), $round, $total));
                        break;
                    case '[':
                        $implicitTag();
                        $parseAttributes($parseAttributes);
                        break;
                    //child
                    case '{':
                        $text = '';
                        $digits = 0;
                        while (!empty($tokens) && '}' !== ($t = array_shift($tokens))) {
                            while ('$' === $t) {
                                $digits++;
                                $t = array_shift($tokens);
                            }
                            if ($digits) {
                                $negative = false;
                                $offset = 0;
                                if ('@' == $t) {
                                    if ('-' == ($t = array_shift($tokens))) {
                                        $negative = true;
                                        if (is_numeric(reset($tokens))) {
                                            $offset = array_shift($tokens);
                                        }
                                    } elseif (is_numeric($t)) {
                                        $offset = $t;
                                    } else {
                                        array_unshift($tokens, $t);
                                    }
                                } else {
                                    array_unshift($tokens, $t);
                                }
                                if ($negative) {
                                    $n = $total + 1 - $round + $offset;
                                } else {
                                    $n = $round + $offset;
                                }
                                $text .= sprintf("%0{$digits}d", $n);
                                $digits = 0;
                            } else {
                                $text .= $t;
                            }
                        }
                        $tag[] = $text;
                        break;
                    case '>':
                        $offsetTokens = null;
                        if ('{' == ($t = array_shift($tokens))) {
                            array_unshift($tokens, $t);
                            $child = new T();
                            $tag[] = $child;
                            $parent = $tag;
                            $tag = $child;
                        } elseif ('[' == $t) {
                            array_unshift($tokens, $t);
                        } else {
                            $child = new T($t);
                            $tag[] = $child;
                            $parent = $tag;
                            $tag = $child;
                        }
                        break;
                    //sibling
                    case '+':
                        $offsetTokens = null;
                        if ($round != $total) {
                            $tokens = array();
                            break;
                        }
                        if ('{' == ($t = array_shift($tokens))) {
                            $tag = $tag->parent;
                            array_unshift($tokens, $t);
                            break;
                        } elseif ('[' == $t) {
                            array_unshift($tokens, $t);
                        } else {
                            $child = new T($t);
                            $tag = $tag->parent;
                            $tag[] = $child;
                            $tag = $child;
                        }
                        break;
                    //sibling of parent
                    case '^':
                        if ($round != $total) {
                            $tokens = array();
                            break;
                        }
                        $tag = $tag->parent;
                        if ($tag->parent)
                            $tag = $tag->parent;
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
                        $source = $tag;
                        if (!empty($offsetTokens)) {
                            if (false !== strpos($source->class, ' ')) {
                                $class = explode(' ', $source->class);
                                array_pop($class);
                                $class = implode(' ', $class);
                            } else {
                                $class = null;
                            }
                            $tag->class($class);
                            $star = array_search('*', $offsetTokens);
                            array_splice($offsetTokens, $star, 2);
                            $remainingTokens = $offsetTokens;
                        } else {
                            $remainingTokens = $tokens;
                        }
                        $source->parent = null;
                        $currentParent = $parent;
                        for ($i = 1; $i <= $times; $i++) {
                            $tag = clone $source;
                            $parent = $currentParent;
                            $tokens = array_values($remainingTokens);
                            $self($self, $i, $times);
                        }
                        $round = 1;
                        $offsetTokens = null;
                        $tag = $source;
                        $tokens = array(); //$remainingTokens;
                        break;
                }
            }
        };
        $parse($parse);
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
                if (('#' == $token || '.' == $token) && (!empty($tokens) || $i == 0)) {
                    $r[] = '';
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