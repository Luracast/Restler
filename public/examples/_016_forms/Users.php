<?php

class Users
{
    function index()
    {
        return array();
    }

    /**
     * @return array {@label <span class="glyphicon glyphicon-user"></span> Sign In}
     */
    function postSignIn($email, $password)
    {
        return func_get_args();
    }

    /**
     * @param string  $firstName
     * @param string  $lastName
     * @param string  $email
     * @param string  $password
     * @param Address $address
     *
     * @return array
     *
     * @view users
     */
    function postSignUp($firstName, $lastName, $email, $password, $address)
    {
        return func_get_args();
    }
}