<?php
class SimpleService {
	/**
	 * HelloWorld
	 * @url GET /
	 */
	function helloworld() {
		return "HelloWorld";
	}
	/**
	 * Sums the given numbers
	 * @param integer $num1
	 * @param integer $num2
	 * @return integer
	 * @url GET /sum
	 */
	function sum($num1, $num2){
		return $num1+$num2;
	}
	/**
	 * Multiplies the given numbers
	 * @param integer $num1
	 * @param integer $num2
	 * @return integer
	 * @url GET /multiply/:num1/:num2
	 */
	 function multiply($num1, $num2){
		return $num1*$num2;
	}
	/**
	 * Sums the given numbers
	 * @param integer $n1
	 * @param integer $n2
	 * @return integer
	 * @url GET /protectedsum
	 */
	protected function protectedsum($n1, $n2){
		return $n1+$n2;
	}
}