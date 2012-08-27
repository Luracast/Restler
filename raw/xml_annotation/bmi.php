<?php
/**
 * @throttle 100
 */
class BMI {
	/**
	* @class Luracast\Restler\Format\XmlFormat
     *  {@rootName self}
     *
     *  ```
     *  {
     *      "attributeNames":
     *          [
     *             "height",
     *             "weight"
     *          ]
     *  }
     *  ```
     * @cache max-age=149;
     * @expires 50
	*/
	function index($height=162.6, $weight=84) {
		$result = new stdClass();
		$cm = $height;
		$kg = $weight;
		
		$meter = $cm / 100;
		$inches = $meter * 39.3700787;
		$feet = round($inches/12);
		$inches = $inches % 12;

		$result->bmi = round($kg/($meter*$meter),2);
		$lb = round($kg/0.45359237,2);
		
		if($result->bmi<18.5){
			$result->message = 'Underweight';
		}elseif ($result->bmi<=24.9){
			$result->message = 'Normal weight';
		}elseif ($result->bmi<=29.9){
			$result->message = 'Overweight';
		}else{
			$result->message = 'Obesity';
		}
		$result->metric = array('height'=>"$cm centimeter", 'weight'=>"$weight kilograms");
		$result->imperial = array('height'=>"$feet feet $inches inches", 'weight'=>"$lb pounds");
		return $result;
	}
}