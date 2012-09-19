<?php
class Access
{
    public function all()
    {
        return "public api, all are welcome";
    }

    /**
     * @access protected
     * @class  AccessControl {@requires user}
     */
    public function user()
    {
        return "protected api, only user and admin can access";
    }

    /**
     * @access protected
     * @class  AccessControl {@requires admin}
     */
    public function admin()
    {
        return "protected api, only admin can access";
    }

}
