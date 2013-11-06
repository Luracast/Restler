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
        use (& $tokens, & $parent, & $tag, $parseAttributes) {
            $offsetTokens = null;
            $parent[] = $tag;
            while ($tokens) {
                switch (array_shift($tokens)) {
                    //attributes
                    case '.':
                        $offsetTokens = array_values($tokens);
                        array_unshift($offsetTokens, '.');
                        $e = $tag->class;
                        $digits = 0;
                        $text = empty($e) ? '' : "$e ";
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
                        $tag->class($text);
                        array_unshift($tokens, $t);
                        break;
                    case '#':
                        $tag->id(array_shift($tokens));
                        break;
                    case '[':
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
                        if ('{' == ($t = array_shift($tokens))) {
                            array_unshift($tokens, $t);
                            $child = new T();
                            $tag[] = $child;
                            $parent = $tag;
                            $tag = $child;
                        } else {
                            $child = new T($t);
                            $tag[] = $child;
                            $parent = $tag;
                            $tag = $child;
                        }
                        break;
                    //sibling
                    case '+':
                        if ('{' == ($t = array_shift($tokens))) {
                            $tag = $tag->parent;
                            $tag[] = array_shift($tokens);
                            array_shift($tokens);
                        } elseif ($round == 1) {
                            $child = new T($t);
                            $tag = $tag->parent;
                            $tag[] = $child;
                            $tag = $child;
                        } else {
                            $delimiter = array(
                                '.' => true,
                                '#' => true,
                                '*' => true,
                                '>' => true,
                                '+' => true,
                                '^' => true,
                            );
                            while (!empty($tokens) && !isset($delimiter[$t = array_shift($tokens)])) {
                                //keep removing until clean
                            }
                            array_unshift($tokens, $t);
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
                        for ($i = 2; $i <= $times; $i++) {
                            $tag = clone $source;
                            $tag->parent = null;
                            $tokens = array_values($remainingTokens);
                            $self($self, $i, $times);
                        }
                        $round = 1;
                        $offsetTokens = null;
                        $tag = $source;
                        $tokens = $remainingTokens;
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