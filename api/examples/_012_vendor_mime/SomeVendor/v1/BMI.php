<?php

namespace SomeVendor\v1;

use stdClass;

class BMI
{
    /**
     * Body Mass Index v1
     *
     * Calculates BMI of a person. Assumes the given weight is in kilo grams and
     * given height is in centimeters
     *
     * @param float $height height in centimeters
     * @param float $weight weight in kilograms
     *
     * @return object
     */
    function index($height = 162.6, $weight = 84.0)
    {
        $result = new stdClass();

        //	1 pound = 0.45359237 kilograms
        //	1 meter = 3.2808399  feet
        //	1 meter = 39.3700787 inches
        //	1 meter = 100		 cms

        // assume height is given in centimeters
        $cm = $height;
        // assume weight is given in kilograms
        $kg = $weight;

        $meter = $cm / 100;
        $inches = $meter * 39.3700787;
        $feet = round($inches / 12);
        $inches = $inches % 12;

        $result->bmi = round($kg / ($meter * $meter), 2);
        $lb = round($kg / 0.45359237, 2);

        if ($result->bmi < 18.5) {
            $result->message = 'Underweight';
        } elseif ($result->bmi <= 24.9) {
            $result->message = 'Normal weight';
        } elseif ($result->bmi <= 29.9) {
            $result->message = 'Overweight';
        } else {
            $result->message = 'Obesity';
        }
        $result->metric = array(
            'height' => "$cm centimeters",
            'weight' => "$weight kilograms"
        );
        $result->imperial = array(
            'height' => "$feet feet $inches inches",
            'weight' => "$lb pounds"
        );
        return $result;
    }
}