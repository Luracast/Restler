<?php

 /**
  * CFTypeDetector
  * Interface for converting native PHP data structures to CFPropertyList objects.
  * @author Rodney Rehm <rodney.rehm@medialize.de>
  * @author Christian Kruse <cjk@wwwtech.de>
  * @package plist
  * @subpackage plist.types
  * @example example-create-02.php Using {@link CFTypeDetector}
  * @example example-create-03.php Using {@link CFTypeDetector} with {@link CFDate} and {@link CFData}
  * @example example-create-04.php Using and extended {@link CFTypeDetector}
  */
class CFTypeDetector {
  
  /**
   * flag stating if all arrays should automatically be converted to {@link CFDictionary}
   * @var boolean
   */
  protected $autoDictionary = false;
  
  /**
   * flag stating if exceptions should be suppressed or thrown
   * @var boolean
   */
  protected $suppressExceptions = false;

  /**
   * name of a method that will be used for array to object conversations
   * @var boolean
   */
  protected $objectToArrayMethod = false;


  /**
   * Create new CFTypeDetector
   * @param boolean $autoDicitionary if set to true all arrays will be converted to {@link CFDictionary}
   * @param boolean $suppressExceptions if set to true toCFType() will not throw any exceptions
   * @param boolean $objectToArrayMethod if non-null, this method will be called on objects (if possible) to convert the object to an array
   */
  public function __construct($autoDicitionary=false,$suppressExceptions=false,$objectToArrayMethod=null) {
    $this->autoDicitionary = $autoDicitionary;
    $this->suppressExceptions = $suppressExceptions;
    $this->objectToArrayMethod = $objectToArrayMethod;
  }
  
  /**
   * Determine if an array is associative or numerical.
   * Numerical Arrays have incrementing index-numbers that don't contain gaps.
   * @param array $value Array to check indexes of
   * @return boolean true if array is associative, false if array has numeric indexes
   */
  protected function isAssociativeArray($value) {
    $numericKeys = true;
    $previousKey = null;
    foreach($value as $key => $v) {
      if(!is_numeric($key) || ($previousKey !== null && $previousKey != $key-1)) {
        $numericKeys = false;
        break;
      }

      $previousKey = $key;
    }
    return !$numericKeys;
  }
  
  /**
   * Get the default value
   * @return CFType the default value to return if no suitable type could be determined
   */
  protected function defaultValue() {
    return new CFString();
  }
  
  /**
   * Create CFType-structure by guessing the data-types.
   * {@link CFArray}, {@link CFDictionary}, {@link CFBoolean}, {@link CFNumber} and {@link CFString} can be created, {@link CFDate} and {@link CFData} cannot.
   * <br /><b>Note:</b>Distinguishing between {@link CFArray} and {@link CFDictionary} is done by examining the keys. 
   * Keys must be strictly incrementing integers to evaluate to a {@link CFArray}. 
   * Since PHP does not offer a function to test for associative arrays, 
   * this test causes the input array to be walked twice and thus work rather slow on large collections. 
   * If you work with large arrays and can live with all arrays evaluating to {@link CFDictionary}, 
   * feel free to set the appropriate flag.
   * <br /><b>Note:</b> If $value is an instance of CFType it is simply returned.
   * <br /><b>Note:</b> If $value is neither a CFType, array, numeric, boolean nor string, it is omitted.
   * @param mixed $value Value to convert to CFType
   * @param boolean $autoDictionary if true {@link CFArray}-detection is bypassed and arrays will be returned as {@link CFDictionary}.
   * @return CFType CFType based on guessed type
   * @uses isAssociativeArray() to check if an array only has numeric indexes
   */
  public function toCFType($value) {
    switch(true) {
      case $value instanceof CFType:
        return $value;
      break;

      case is_object($value):
        // DateTime should be CFDate
        if(class_exists( 'DateTime' ) && $value instanceof DateTime){
          return new CFDate($value->getTimestamp());
        }
        
        // convert possible objects to arrays, arrays will be arrays
        if($this->objectToArrayMethod && is_callable(array($value, $this->objectToArrayMethod))){
          $value = call_user_func( array( $value, $this->objectToArrayMethod ) );
        }
        
        if(!is_array($value)){
          if($this->suppressExceptions)
            return $this->defaultValue();

          throw new PListException('Could not determine CFType for object of type '. get_class($value));
        }
      /* break; omitted */

      case $value instanceof Iterator:
      case is_array($value):
        // test if $value is simple or associative array
        if(!$this->autoDictionary) {
          if(!$this->isAssociativeArray($value)) {
            $t = new CFArray();
            foreach($value as $v) $t->add($this->toCFType($v));
            return $t;
          }
        }

        $t = new CFDictionary();
        foreach($value as $k => $v) $t->add($k, $this->toCFType($v));

        return $t;
      break;

      case is_numeric($value):
        return new CFNumber($value);
      break;

      case is_bool($value):
        return new CFBoolean($value);
      break;

      case is_string($value):
        return new CFString($value);
      break;

      case is_null($value):
        return new CFString();
      break;

      case is_resource($value):
        if( $this->suppressExceptions )
          return $this->defaultValue();

        throw new PListException('Could not determine CFType for resource of type '. get_resource_type($value));
      break;
      
      default:
        if( $this->suppressExceptions )
          return $this->defaultValue();

        throw new PListException('Could not determine CFType for '. gettype($value));
      break;
    }
  }

}

?>