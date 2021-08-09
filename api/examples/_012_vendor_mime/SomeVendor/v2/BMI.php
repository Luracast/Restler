<?php

namespace SomeVendor\v2;

use Luracast\Restler\Exceptions\HttpException;
use stdClass;

class BMI
{
    /**
     * Body Mass Index v2
     *
     * Calculates BMI of a person. Version 2 is improved to support different
     * units for height and weight given as the suffix.
     *
     * @param string $height height along with unit
     * @param string $weight weight along with unit
     *
     * @throws HttpException 400
     * @return object
     */
    function index($height = "162.6cm", $weight = "84.0kg")
    {
        $result = new stdClass();

        //	1 pound = 0.45359237 kilograms
        //	1 meter = 3.2808399  feet
        //	1 meter = 39.3700787 inches
        //	1 meter = 100		 cms

        preg_match('/(\d*\.?\d*)(\w*)/', $height, $matches);
        $height = floatval($matches[1]);
        $height_unit = strtolower($matches[2]);

        preg_match('/(\d*\.?\d*)(\w*)/', $weight, $matches);
        $weight = floatval($matches[1]);
        $weight_unit = strtolower($matches[2]);

        switch ($height_unit) {
            case 'cm':
            case 'centimeter':
            case 'centimeters':
                $meter = $height / 100;
                break;
            case 'm':
            case 'meter':
            case 'meters':
                $meter = $height;
                break;
            case 'in':
            case 'inches':
            case '"':
                $meter = 39.3700787 * $height;
                break;
            case 'ft':
            case 'feet':
            case '′':
                $meter = 3.2808399 * $height;
                break;
            default:
                throw new HttpException(400, 'invalid height unit');

        }
        $cm = $meter * 100;
        $inches = $meter * 39.3700787;
        $feet = round($inches / 12);
        $inches = $inches % 12;

        switch ($weight_unit) {
            case 'kg':
            case 'kilogram':
            case 'kilograms':
                $kg = $weight;
                break;
            case 'lbs':
            case 'pound':
            case 'pounds':
            case '£':
            case '₤':
                $kg = 0.45359237 * $weight;
                break;
            default:
                throw new HttpException(400, 'invalid weight unit');
        }

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