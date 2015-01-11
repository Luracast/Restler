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

    function postSignUp($firstName, $lastName, $email, $password, Address $address)
    {
        return func_get_args();
    }
}