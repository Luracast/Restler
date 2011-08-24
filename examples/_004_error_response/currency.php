<?php
class Currency {
	function format($number=NULL) {
		if(is_null($number))throw new RestException(400);
		if(!is_numeric($number))throw new RestException(412,'not a valid number');
		
		// let's print the international format for the en_US locale
		setlocale(LC_MONETARY, 'en_US');
		return money_format('%i', $number);
	}
}