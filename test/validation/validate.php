<?php
class Validate {

    /**
     *
     * @param string $str {choice=one,two,three&message=str+should+be+one+or+two+or+three}
     * @return string
     */
    function string($str = 'none')
    {
        return "You have selected '$str'";
    }

    /**
     *
     * @param string $email {type=email&response=exception}
     * @return string
     */
    function email($email)
    {
        return "'$email' is a valid email id";
    }
    
    /**
     * Check the password strength
     * using regex /^.*(?=.{8,})(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).*$/
     * @param string $password {fix=true&min=15&message=Given+password+is+very+week&pattern=%2F%5E.*(%3F%3D.%7B8%2C%7D)(%3F%3D.*%5Cd)(%3F%3D.*%5Ba-z%5D)(%3F%3D.*%5BA-Z%5D).*%24%2F}
     * @return string
     */
    function password($password){
        return "'$password' is a valid strong password";
    }

    function number($num)
    {
        return "Number : $num";
    }

    /**
     *
     * @param int $num {min=10&max=20.2&fix=true}
     * @return string
     */
    function int($num)
    {
        return "Integer : $num";
    }

    function ipaddress($ip)
    {
        return "IP Address : $ip";
    }
}