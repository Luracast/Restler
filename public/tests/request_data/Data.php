<?php
class Data
{

    /**
     * @url POST request_data
     **/
    function request_data($request_data)
    {
        return $request_data;
    }

    /**
     * @url POST
     * @url GET
     * @url GET {name}
     * @url PUT
     */
    function name_email($name, $email)
    {
        return compact('name', 'email');
    }

}