<?php $o = array();

// ** THIS IS AN AUTO GENERATED FILE. DO NOT EDIT MANUALLY ** 

//==================== v1/api/method/{n0} ====================

$o['v1/api/method/{n0}'] = array();

//==== v1/api/method/{n0} POST ====

$o['v1/api/method/{n0}']['POST'] = array (
    'url' => 'v1/api/method/{param1}',
    'className' => 'Api',
    'path' => 'v1/api',
    'methodName' => 'postMethod',
    'arguments' => 
    array (
        'param1' => 0,
        'param2' => 1,
        'param3' => 2,
    ),
    'defaults' => 
    array (
        0 => NULL,
        1 => NULL,
        2 => 'optional',
    ),
    'metadata' => 
    array (
        'description' => 'Auto routed method which maps to POST api/method/{param1}',
        'longDescription' => '',
        'param' => 
        array (
            0 => 
            array (
                'type' => 'int',
                'name' => 'param1',
                'description' => 'map to url',
                'default' => NULL,
                'required' => true,
                'from' => 'path',
            ),
            1 => 
            array (
                'type' => 'array',
                'name' => 'param2',
                'description' => 'map to request body',
                'default' => NULL,
                'required' => true,
                'from' => 'body',
            ),
            2 => 
            array (
                'type' => 'string',
                'name' => 'param3',
                'description' => 'map to query string',
                'default' => 'optional',
                'required' => false,
                'from' => 'query',
            ),
        ),
        'return' => 
        array (
            'type' => 'string',
            'description' => '',
        ),
        'resourcePath' => 'v1/api/',
    ),
    'accessLevel' => 0,
);

//==================== v1/api/somanyways ====================

$o['v1/api/somanyways'] = array();

//==== v1/api/somanyways GET ====

$o['v1/api/somanyways']['GET'] = array (
    'url' => 'v1/api/somanyways',
    'className' => 'Api',
    'path' => 'v1/api',
    'methodName' => 'soManyWays',
    'arguments' => 
    array (
        'p1' => 0,
        'p2' => 1,
        'p3' => 2,
    ),
    'defaults' => 
    array (
        0 => NULL,
        1 => NULL,
        2 => 'optional',
    ),
    'metadata' => 
    array (
        'description' => 'Auto routed method that creates all possible routes.',
        'longDescription' => 'This was the standard behavior for Restler 2',
        'smart-auto-routing' => 'false',
        'resourcePath' => 'v1/api/',
        'param' => 
        array (
            0 => 
            array (
                'name' => 'p1',
                'default' => NULL,
                'required' => true,
                'from' => 'path',
            ),
            1 => 
            array (
                'name' => 'p2',
                'default' => NULL,
                'required' => true,
                'from' => 'path',
            ),
            2 => 
            array (
                'name' => 'p3',
                'default' => 'optional',
                'required' => false,
                'from' => 'query',
            ),
        ),
    ),
    'accessLevel' => 0,
);

//==================== v1/api/somanyways/{s0} ====================

$o['v1/api/somanyways/{s0}'] = array();

//==== v1/api/somanyways/{s0} GET ====

$o['v1/api/somanyways/{s0}']['GET'] = array (
    'url' => 'v1/api/somanyways/{p1}',
    'className' => 'Api',
    'path' => 'v1/api',
    'methodName' => 'soManyWays',
    'arguments' => 
    array (
        'p1' => 0,
        'p2' => 1,
        'p3' => 2,
    ),
    'defaults' => 
    array (
        0 => NULL,
        1 => NULL,
        2 => 'optional',
    ),
    'metadata' => 
    array (
        'description' => 'Auto routed method that creates all possible routes.',
        'longDescription' => 'This was the standard behavior for Restler 2',
        'smart-auto-routing' => 'false',
        'resourcePath' => 'v1/api/',
        'param' => 
        array (
            0 => 
            array (
                'name' => 'p1',
                'default' => NULL,
                'required' => true,
                'from' => 'path',
            ),
            1 => 
            array (
                'name' => 'p2',
                'default' => NULL,
                'required' => true,
                'from' => 'path',
            ),
            2 => 
            array (
                'name' => 'p3',
                'default' => 'optional',
                'required' => false,
                'from' => 'query',
            ),
        ),
    ),
    'accessLevel' => 0,
);

//==================== v1/api/somanyways/{s0}/{s1} ====================

$o['v1/api/somanyways/{s0}/{s1}'] = array();

//==== v1/api/somanyways/{s0}/{s1} GET ====

$o['v1/api/somanyways/{s0}/{s1}']['GET'] = array (
    'url' => 'v1/api/somanyways/{p1}/{p2}',
    'className' => 'Api',
    'path' => 'v1/api',
    'methodName' => 'soManyWays',
    'arguments' => 
    array (
        'p1' => 0,
        'p2' => 1,
        'p3' => 2,
    ),
    'defaults' => 
    array (
        0 => NULL,
        1 => NULL,
        2 => 'optional',
    ),
    'metadata' => 
    array (
        'description' => 'Auto routed method that creates all possible routes.',
        'longDescription' => 'This was the standard behavior for Restler 2',
        'smart-auto-routing' => 'false',
        'resourcePath' => 'v1/api/',
        'param' => 
        array (
            0 => 
            array (
                'name' => 'p1',
                'default' => NULL,
                'required' => true,
                'from' => 'path',
            ),
            1 => 
            array (
                'name' => 'p2',
                'default' => NULL,
                'required' => true,
                'from' => 'path',
            ),
            2 => 
            array (
                'name' => 'p3',
                'default' => 'optional',
                'required' => false,
                'from' => 'query',
            ),
        ),
    ),
    'accessLevel' => 0,
);

//==================== v1/api/somanyways/{s0}/{s1}/{s2} ====================

$o['v1/api/somanyways/{s0}/{s1}/{s2}'] = array();

//==== v1/api/somanyways/{s0}/{s1}/{s2} GET ====

$o['v1/api/somanyways/{s0}/{s1}/{s2}']['GET'] = array (
    'url' => 'v1/api/somanyways/{p1}/{p2}/{p3}',
    'className' => 'Api',
    'path' => 'v1/api',
    'methodName' => 'soManyWays',
    'arguments' => 
    array (
        'p1' => 0,
        'p2' => 1,
        'p3' => 2,
    ),
    'defaults' => 
    array (
        0 => NULL,
        1 => NULL,
        2 => 'optional',
    ),
    'metadata' => 
    array (
        'description' => 'Auto routed method that creates all possible routes.',
        'longDescription' => 'This was the standard behavior for Restler 2',
        'smart-auto-routing' => 'false',
        'resourcePath' => 'v1/api/',
        'param' => 
        array (
            0 => 
            array (
                'name' => 'p1',
                'default' => NULL,
                'required' => true,
                'from' => 'path',
            ),
            1 => 
            array (
                'name' => 'p2',
                'default' => NULL,
                'required' => true,
                'from' => 'path',
            ),
            2 => 
            array (
                'name' => 'p3',
                'default' => 'optional',
                'required' => false,
                'from' => 'query',
            ),
        ),
    ),
    'accessLevel' => 0,
);

//==================== v1/api/method2 ====================

$o['v1/api/method2'] = array();

//==== v1/api/method2 POST ====

$o['v1/api/method2']['POST'] = array (
    'url' => 'v1/api/method2',
    'className' => 'Api',
    'path' => 'v1/api',
    'methodName' => 'whatEver',
    'arguments' => 
    array (
        'anything' => 0,
    ),
    'defaults' => 
    array (
        0 => NULL,
    ),
    'metadata' => 
    array (
        'description' => 'Manually routed method. we can specify as many routes as we want',
        'longDescription' => '',
        'url' => 'GET what/ever/you/want',
        'resourcePath' => 'v1/api/',
        'param' => 
        array (
            0 => 
            array (
                'name' => 'anything',
                'default' => NULL,
                'required' => true,
                'from' => 'path',
            ),
        ),
    ),
    'accessLevel' => 0,
);

//==================== v1/api/method2/{s0} ====================

$o['v1/api/method2/{s0}'] = array();

//==== v1/api/method2/{s0} POST ====

$o['v1/api/method2/{s0}']['POST'] = array (
    'url' => 'v1/api/method2/{anything}',
    'className' => 'Api',
    'path' => 'v1/api',
    'methodName' => 'whatEver',
    'arguments' => 
    array (
        'anything' => 0,
    ),
    'defaults' => 
    array (
        0 => NULL,
    ),
    'metadata' => 
    array (
        'description' => 'Manually routed method. we can specify as many routes as we want',
        'longDescription' => '',
        'url' => 'GET what/ever/you/want',
        'resourcePath' => 'v1/api/',
        'param' => 
        array (
            0 => 
            array (
                'name' => 'anything',
                'default' => NULL,
                'required' => true,
                'from' => 'path',
            ),
        ),
    ),
    'accessLevel' => 0,
);

//==================== v1/api/what/ever/you/want ====================

$o['v1/api/what/ever/you/want'] = array();

//==== v1/api/what/ever/you/want GET ====

$o['v1/api/what/ever/you/want']['GET'] = array (
    'url' => 'v1/api/what/ever/you/want',
    'className' => 'Api',
    'path' => 'v1/api',
    'methodName' => 'whatEver',
    'arguments' => 
    array (
        'anything' => 0,
    ),
    'defaults' => 
    array (
        0 => NULL,
    ),
    'metadata' => 
    array (
        'description' => 'Manually routed method. we can specify as many routes as we want',
        'longDescription' => '',
        'url' => 'GET what/ever/you/want',
        'resourcePath' => 'v1/api/',
        'param' => 
        array (
            0 => 
            array (
                'name' => 'anything',
                'default' => NULL,
                'required' => true,
                'from' => 'path',
            ),
        ),
    ),
    'accessLevel' => 0,
);

//==================== * ====================

$o['*'] = array();

//==== * v1/api/all ====

$o['*']['v1/api/all'] = array (
    'GET' => 
    array (
        'url' => 'v1/api/all/*',
        'className' => 'Api',
        'path' => 'v1/api',
        'methodName' => 'allIsMine',
        'arguments' => 
        array (
        ),
        'defaults' => 
        array (
        ),
        'metadata' => 
        array (
            'description' => 'Manually wildcard routed method. all paths that begin with `all` will be routed to this method',
            'longDescription' => '',
            'url' => 'GET all/*',
            'resourcePath' => 'v1/api/',
            'param' => 
            array (
            ),
        ),
        'accessLevel' => 0,
    ),
);
return $o;