<?php $o=array();



############### GET ###############

$o['GET']=array();

#==== GET author

$o['GET']['author']=array (
	  'class_name' => 'Author',
	  'method_name' => 'get',
	  'arguments' => 
	  array (
	    'id' => 0,
	  ),
	  'defaults' => 
	  array (
	    0 => NULL,
	  ),
	  'metadata' => 
	  array (
	  ),
	  'method_flag' => 0,
	);

#==== GET author/:id

$o['GET']['author/:id']=array (
	  'class_name' => 'Author',
	  'method_name' => 'get',
	  'arguments' => 
	  array (
	    'id' => 0,
	  ),
	  'defaults' => 
	  array (
	    0 => NULL,
	  ),
	  'metadata' => 
	  array (
	  ),
	  'method_flag' => 0,
	);


############### POST ###############

$o['POST']=array();

#==== POST author

$o['POST']['author']=array (
	  'class_name' => 'Author',
	  'method_name' => 'post',
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
	  ),
	  'method_flag' => 0,
	);


############### PUT ###############

$o['PUT']=array();

#==== PUT author

$o['PUT']['author']=array (
	  'class_name' => 'Author',
	  'method_name' => 'put',
	  'arguments' => 
	  array (
	    'id' => 0,
	    'request_data' => 1,
	  ),
	  'defaults' => 
	  array (
	    0 => NULL,
	    1 => NULL,
	  ),
	  'metadata' => 
	  array (
	  ),
	  'method_flag' => 0,
	);

#==== PUT author/:id

$o['PUT']['author/:id']=array (
	  'class_name' => 'Author',
	  'method_name' => 'put',
	  'arguments' => 
	  array (
	    'id' => 0,
	    'request_data' => 1,
	  ),
	  'defaults' => 
	  array (
	    0 => NULL,
	    1 => NULL,
	  ),
	  'metadata' => 
	  array (
	  ),
	  'method_flag' => 0,
	);


############### DELETE ###############

$o['DELETE']=array();

#==== DELETE author

$o['DELETE']['author']=array (
	  'class_name' => 'Author',
	  'method_name' => 'delete',
	  'arguments' => 
	  array (
	    'id' => 0,
	  ),
	  'defaults' => 
	  array (
	    0 => NULL,
	  ),
	  'metadata' => 
	  array (
	  ),
	  'method_flag' => 0,
	);

#==== DELETE author/:id

$o['DELETE']['author/:id']=array (
	  'class_name' => 'Author',
	  'method_name' => 'delete',
	  'arguments' => 
	  array (
	    'id' => 0,
	  ),
	  'defaults' => 
	  array (
	    0 => NULL,
	  ),
	  'metadata' => 
	  array (
	  ),
	  'method_flag' => 0,
	);
return $o;