<?php
namespace Luracast\Restler;

/**
 * Parses the PHPDoc comments for metadata. Inspired by Documentor code base
 * @category   Framework
 * @package    restler
 * @subpackage helper
 * @author     Murray Picton <info@murraypicton.com>
 * @author     R.Arul Kumaran <arul@luracast.com>
 * @copyright  2010 Luracast
 * @license    http://www.gnu.org/licenses/ GNU General Public License
 * @link       https://github.com/murraypicton/Doqumentor
 */
class DocParser
{
    private $_params = array ();

    public static function parse($phpDocComment)
    {
        $p = new DocParser ();
        if (empty ( $phpDocComment )) {
            return $p->_params;
        }
        // Get the comment
        if (preg_match ( '#^/\*\*(.*)\*/#s',
                $phpDocComment, $comment ) === false) {
            return $p->_params;
        }
        $comment = trim ( $comment [1] );
        // Get all the lines and strip the * from the first character
        if (preg_match_all ( '#^\s*\*(.*)#m', $comment, $lines ) === false) {
            return $p->_params;
        }
        $p->parseLines ( $lines [1] );

        return $p->_params;
    }

    private function parseLines($lines)
    {
        $desc = array ();
        foreach ($lines as $line) {
            $parsedLine = $this->parseLine ( $line ); // Parse the line
//            if ($parsedLine === false &&
//                    ! isset ( $this->_params ['description'] )) {
//                if (isset ( $desc )) {
//                    // Store the first line in the short description
//                    $this->_params ['description'] = implode ( PHP_EOL, $desc );
//                }
//                $desc = array ();
//            } else
            if ($parsedLine !== false) {
                $desc [] = $parsedLine; // Store the line in the long
                                            // description
            }
        }
        $desc = trim ( implode ( ' ', $desc ) );
        if (! empty ( $desc )) {
            $this->_params ['longDescription'] = $desc;
        }
    }

    private function parseLine($line)
    {
        // trim the whitespace from the line
        $line = trim ( $line );
        if (empty ( $line )) {
            return false; // Empty line
        }
        if (strpos ( $line, '@' ) === 0) {
            if (strpos ( $line, ' ' ) > 0) {
                // Get the parameter name
                $param = substr ( $line, 1, strpos ( $line, ' ' ) - 1 );
                $value = substr ( $line, strlen ( $param ) + 2 ); // Get the value
            } else {
                $param = substr ( $line, 1 );
                $value = '';
            }
            // Parse the line and return false if the parameter is valid
            if ($this->setParam ( $param, $value )) {
                return false;
            }
        }

        return $line;
    }

    private function setParam($param, $value)
    {
        $allowMultiple = false;
        switch ($param) {
            case 'param' :
                $value = $this->formatParam ( $value );
                $allowMultiple = true;
                break;
            case 'return' :
                $value = $this->formatReturn ( $value );
                break;
            case 'class' :
                list ( $param, $value ) = $this->formatClass ( $value );
                break;
            case 'status' :
                $value = explode ( ' ', $value, 2 );
                $value [0] = intval ( $value [0] );
                break;
            case 'throws' :
                $value = $this->formatThrows ( $value );
                $allowMultiple = true;
                break;
            case 'header' :
                $allowMultiple = true;
        }
        if (empty ( $this->_params [$param] )) {
            if ($allowMultiple) {
                $this->_params [$param] = array (
                        $value
                );
            } else {
                $this->_params [$param] = $value;
            }
        } elseif ($allowMultiple) {
            $this->_params [$param] [] = $value;
        } elseif ($param == 'param') {
            $arr = array (
                    $this->_params [$param],
                    $value
            );
            $this->_params [$param] = $arr;
        } else {
            $this->_params [$param] = $value + $this->_params [$param];
        }

        return true;
    }

    private function formatThrows($value)
    {
        $value = explode ( ' ', $value, 3 );
        $r = array (
                'exception' => $value [0]
        );
        $r ['code'] = @is_numeric ( $value [1] ) ? intval ( $value [1] ) : 500;
        $r ['reason'] = @isset ( $value [2] ) ? $value [2] : '';

        return $r;
    }

    private function formatClass($value)
    {
        $r = preg_split ( "[{|}]", $value );
        if (count ( $r ) > 1) {
            $param = trim ( $r [0] );
            parse_str ( $r [1], $value );
            foreach ($value as $key => $val) {
                $val = explode ( ',', $val );
                if (count ( $val ) > 1) {
                    $value [$key] = $val;
                }
            }
        } else {
            $param = 'Unknown';
        }

        return array (
                $param,
                $value
        );
    }

    private function formatReturn($string)
    {
        $arr = explode ( ' ', $string, 2 );
        $r = array (
                'type' => $arr [0]
        );
        if (! empty ( $arr [1] )) {
            $r ['description'] = trim ( $arr [1] );
        }

        return $r;
    }

    private function formatParam($string)
    {
        $arr = explode ( ' ', $string, 2 );
        $r = array (
                'type' => $arr [0]
        );
        $arr2 = preg_split ( "[{|}]", $arr [1] );
        if (! empty ( $arr2 [0] )) {
            $arr3 = explode ( ' ', $arr2 [0], 2 );
        }
        if (isset($arr3 [0])) {
            $r ['name'] = trim ( $arr3 [0], '$  ' );
        }
        if (! empty ( $arr3 [1] )) {
            $r ['description'] = trim ( $arr3 [1] );
        }
        if (! empty ( $arr2 [1] )) {
            if (! isset ( $r ['validate'] )) {
                $r ['validate'] = array ();
            }
            parse_str ( $arr2 [1], $value );
            foreach ($value as $key => $val) {
                $val = explode ( ',', $val );
                if (count ( $val ) > 1) {
                    $value [$key] = $val;
                }
            }
            $r ['validate'] = $value;
        }
        if (! empty ( $arr2 [2] )) {
            $r ['description'] = trim ( $arr2 [2] );
        }

        return $r;
    }
}
