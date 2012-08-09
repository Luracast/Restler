<?php
class Validate {
    
    /*
     *
     */
    /**
     * String to be tested
     * two
     * three
     *
     * Long Description comes
     * here
     * 
     * but can go beyond
     * three lines
     *
     * @param string $str
     *   ``` choice=one,two,three&message=str+should+be+one+or+two+or+three```
     * @return string
     * 
     * {@inheritDoc}
     ************************/
    function string($str = 'none')
    {
        return "You have selected '$str'";
    }

    /**
     *
     * @param string $email
     *            ``` type=email&response=exception```
     * @return string
     */
    function email($email)
    {
        //return $this->restler->serviceMethodInfo;
        return "'$email' is a valid email id";
    }

    /**
     * Check the password strength
     * using regex <pre>/^.*(?=.{8,})(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).*$/</pre>
     *
     * @param string|number $password
     * ``` fix=true&min=15&message=Given+password+is+very+week&
     * &pattern=%2F%5E.*(%3F%3D.%7B8%2C%7D)(%3F%3D.*%5Cd)(%3F%3D.*%5Ba-z%5D)(%3F%3D.*%5BA-Z%5D).*%24%2F```
     * @return string
     */
    function password($password)
    {
        return "'$password' is a valid strong password";
    }

    /**
     * @param int $num
     * @return string
     */
    function number($num)
    {
        return "Number : $num";
    }
    
    /**
     * Custom instance for our functions
     * @param Custom $instance instance of Custom class {validate=true}
     * @return unknown
     */
    function custom ($instance=null){
        return $instance;
    }

    /**
     *
     * @param int $num
     *            ``` {"min":10,"max":20.2,"fix":true}```
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