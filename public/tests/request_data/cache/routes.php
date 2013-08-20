<?php $o = array();

// ** THIS IS AN AUTO GENERATED FILE. DO NOT EDIT MANUALLY ** 

//==================== v1 ====================

$o['v1'] = array();

//==== v1 POST ====

$o['v1']['POST'] = array (
    'url' => 'v1',
    'className' => 'Data',
    'path' => 'v1',
    'methodName' => 'name_email',
    'arguments' => 
    array (
        'name' => 0,
        'email' => 1,
    ),
    'defaults' => 
    array (
        0 => NULL,
        1 => NULL,
    ),
    'metadata' => 
    array (
        'description' => '',
        'longDescription' => '',
        'url' => 0,
        'resourcePath' => 'v1/',
        'param' => 
        array (
            0 => 
            array (
                'name' => 'name',
                'default' => NULL,
                'required' => true,
                'children' => 
                array (
                ),
                'from' => 'body',
            ),
            1 => 
            array (
                'name' => 'email',
                'default' => NULL,
                'required' => true,
                'children' => 
                array (
                ),
                'from' => 'body',
            ),
        ),
    ),
    'accessLevel' => 0,
);

//==== v1 GET ====

$o['v1']['GET'] = array (
    'url' => 'v1',
    'className' => 'Data',
    'path' => 'v1',
    'methodName' => 'name_email',
    'arguments' => 
    array (
        'name' => 0,
        'email' => 1,
    ),
    'defaults' => 
    array (
        0 => NULL,
        1 => NULL,
    ),
    'metadata' => 
    array (
        'description' => '',
        'longDescription' => '',
        'url' => 0,
        'resourcePath' => 'v1/',
        'param' => 
        array (
            0 => 
            array (
                'name' => 'name',
                'default' => NULL,
                'required' => true,
                'children' => 
                array (
                ),
                'from' => 'query',
            ),
            1 => 
            array (
                'name' => 'email',
                'default' => NULL,
                'required' => true,
                'children' => 
                array (
                ),
                'from' => 'query',
            ),
        ),
    ),
    'accessLevel' => 0,
);

//==== v1 PUT ====

$o['v1']['PUT'] = array (
    'url' => 'v1',
    'className' => 'Data',
    'path' => 'v1',
    'methodName' => 'name_email',
    'arguments' => 
    array (
        'name' => 0,
        'email' => 1,
    ),
    'defaults' => 
    array (
        0 => NULL,
        1 => NULL,
    ),
    'metadata' => 
    array (
        'description' => '',
        'longDescription' => '',
        'url' => 0,
        'resourcePath' => 'v1/',
        'param' => 
        array (
            0 => 
            array (
                'name' => 'name',
                'default' => NULL,
                'required' => true,
                'children' => 
                array (
                ),
                'from' => 'body',
            ),
            1 => 
            array (
                'name' => 'email',
                'default' => NULL,
                'required' => true,
                'children' => 
                array (
                ),
                'from' => 'body',
            ),
        ),
    ),
    'accessLevel' => 0,
);

//==================== v1/request_data ====================

$o['v1/request_data'] = array();

//==== v1/request_data POST ====

$o['v1/request_data']['POST'] = array (
    'url' => 'v1/request_data',
    'className' => 'Data',
    'path' => 'v1',
    'methodName' => 'request_data',
    'arguments' => 
    array (
        'request_data' => 0,
    ),
    'defaults' => 
    array (
        0 => NULL,
    ),
    'metadata' => 
    array (
        'description' => '',
        'longDescription' => '',
        'url' => 'POST request_data',
        'resourcePath' => 'v1/',
        'param' => 
        array (
            0 => 
            array (
                'name' => 'request_data',
                'default' => NULL,
                'required' => true,
                'children' => 
                array (
                ),
                'from' => 'body',
            ),
        ),
    ),
    'accessLevel' => 0,
);

//==================== v1/resources ====================

$o['v1/resources'] = array();

//==== v1/resources GET ====

$o['v1/resources']['GET'] = array (
    'url' => 'v1/resources',
    'className' => 'Luracast\\Restler\\Resources',
    'path' => 'v1/resources',
    'methodName' => 'index',
    'arguments' => 
    array (
    ),
    'defaults' => 
    array (
    ),
    'metadata' => 
    array (
        'description' => '',
        'longDescription' => '',
        'access' => 'hybrid',
        'return' => 
        array (
            'type' => '\\stdClass',
            'description' => '',
        ),
        'category' => 'Framework',
        'package' => 'Restler',
        'author' => 
        array (
            0 => 
            array (
                'email' => 'arul@luracast.com',
                'name' => 'R.Arul Kumaran',
            ),
        ),
        'copyright' => '2010 Luracast',
        'license' => 'http://www.opensource.org/licenses/lgpl-license.php LGPL',
        'link' => 
        array (
            0 => 'http://luracast.com/products/restler/',
        ),
        'version' => '3.0.0rc4',
        'resourcePath' => 'v1/resources/',
        'classDescription' => 'API Class to create Swagger Spec 1.1 compatible id and operation listing',
        'param' => 
        array (
        ),
    ),
    'accessLevel' => 1,
);

//==================== v1/resources/{s0} ====================

$o['v1/resources/{s0}'] = array();

//==== v1/resources/{s0} GET ====

$o['v1/resources/{s0}']['GET'] = array (
    'url' => 'v1/resources/{id}',
    'className' => 'Luracast\\Restler\\Resources',
    'path' => 'v1/resources',
    'methodName' => 'get',
    'arguments' => 
    array (
        'id' => 0,
    ),
    'defaults' => 
    array (
        0 => '',
    ),
    'metadata' => 
    array (
        'description' => '',
        'longDescription' => '',
        'access' => 'hybrid',
        'param' => 
        array (
            0 => 
            array (
                'type' => 'string',
                'name' => 'id',
                'default' => '',
                'required' => false,
                'children' => 
                array (
                ),
                'from' => 'query',
            ),
        ),
        'throws' => 
        array (
            0 => 
            array (
                'code' => 500,
                'reason' => 'RestException',
            ),
        ),
        'return' => 
        array (
            'type' => 
            array (
                0 => 'null',
                1 => 'stdClass',
            ),
            'description' => '',
        ),
        'url' => 'GET {id}',
        'category' => 'Framework',
        'package' => 'Restler',
        'author' => 
        array (
            0 => 
            array (
                'email' => 'arul@luracast.com',
                'name' => 'R.Arul Kumaran',
            ),
        ),
        'copyright' => '2010 Luracast',
        'license' => 'http://www.opensource.org/licenses/lgpl-license.php LGPL',
        'link' => 
        array (
            0 => 'http://luracast.com/products/restler/',
        ),
        'version' => '3.0.0rc4',
        'resourcePath' => 'v1/resources/',
        'classDescription' => 'API Class to create Swagger Spec 1.1 compatible id and operation listing',
    ),
    'accessLevel' => 1,
);

//==================== v1/{s0} ====================

$o['v1/{s0}'] = array();

//==== v1/{s0} GET ====

$o['v1/{s0}']['GET'] = array (
    'url' => 'v1/{name}',
    'className' => 'Data',
    'path' => 'v1',
    'methodName' => 'name_email',
    'arguments' => 
    array (
        'name' => 0,
        'email' => 1,
    ),
    'defaults' => 
    array (
        0 => NULL,
        1 => NULL,
    ),
    'metadata' => 
    array (
        'description' => '',
        'longDescription' => '',
        'url' => 0,
        'resourcePath' => 'v1/',
        'param' => 
        array (
            0 => 
            array (
                'name' => 'name',
                'default' => NULL,
                'required' => true,
                'children' => 
                array (
                ),
                'from' => 'path',
            ),
            1 => 
            array (
                'name' => 'email',
                'default' => NULL,
                'required' => true,
                'children' => 
                array (
                ),
                'from' => 'query',
            ),
        ),
    ),
    'accessLevel' => 0,
);
return $o;