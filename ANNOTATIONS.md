# Supported Annotations

You may use the following php doc comments to annotate your API methods.
All tags except `@url`, `@param` and `@var` can also be defined at the class
level so that they will be applied for all the api methods. You can override
them at method level individually when needed.

## @url     

**Syntax:**

    @url GET|POST|PUT|PATCH|DELETE custom/{dynamic}/route

**Example:**

    @url POST authors/{id}/books

Overrides auto routes and creates manual routes. Use as many as you need to map
the same method to multiple routes.

## @access     

**Syntax:**

    @access private|public|protected|hybrid
    
**Example:**

    @access protected

Access control for api methods. PHPDoc only supports private and public,
Restler adds protected for api that needs authentication, hybrid for api that
enhances resulting data for authenticated users.


## @smart-auto-routing     

**Syntax:**

    @smart-auto-routing true|false
    
**Example:**

    @smart-auto-routing false
    
Smart auto routing is the default as it avoids creating multiple routes that
can increase the ambiguity.

 - when a method parameter is optional, it is not mapped to the url and
   should only be used in request body or as a query string like `/resource?id=value`.

 - When a required parameter is scalar, it will be mapped as part
   of the url like `/resource/{id}`

When set to `false` it creates all possible routes instead.


## @class     

**Syntax:**

    @class ClassName {@propertyName value}

**Example:**

    @class AccessControl {@requires user} {@level 5}
    
Sets property of the specified class with specified value when the class
instance is created by Restler. Property can also be a static property.

## @cache     

**Syntax:**

    @cache headerCacheControlValue
    
**Example:**

    @cache max-age={expires}, must-revalidate
    
Specifies value to set CacheControl Header, it can use @expires value as shown
in the example

## @expires     

**Syntax:**

    @expires numberOfSeconds
    
**Example:**

    @expires 30

When set to zero the content will expire immediately. Alternatively you can
specify the number of seconds the content will expire for client side and proxy
caching.

## @throttle     

**Syntax:**

    @throttle numberOfMilliSeconds
    
**Example:**

    @throttle 3000
    
Sets the time in milliseconds for bandwidth throttling, which will become the
minimum response time for each API request.

## @status     

**Syntax:**

    @status httpStatusCode
    
**Example:**

    @status 201

Sets the HTTP Status code for the successful response.

## @header     

**Syntax:**

    @header httpHeader

**Example:**

    @header Link: <meta.rdf>; rel=meta

Sets or overrides the specific HTTP Header.

## @param     

**Syntax:**

    @param [type] Name [Description] {@name value}
    
**Example:**

    @param int $num1 increment value {@min 5} {@max 100}
    
Standard @param comment that sets the type and description of a parameter.
Check out supported child attributes under [@param](PARAM.md) documentation.

## @throws     

**Syntax:**

    @throws RestException [httpStatusCode] [Reason]
    
or
    
    @throws AnyOtherException [Reason]

**Example:**

    @throws RestException 404 No Author for specified id

Documents possible error responses for the API call. When the exception thrown is 
not `RestException`, http status code `500` is used.

## @return     

**Syntax:**

    @return type [Description]

**Example:**

    @return Author an instance of iValueObject

Documents the structure of success response, user defined classes must extend
iValueObject.


## @var

**Syntax:**

    @var [type] [Description] {@name value}

**Example:**

    @var int policy age {@min 18} {@max 100}

When an api method has custom class as one of the parameter or return value
@var comments can be used with properties of that class. They will be used 
for validation and documentation. Supported child attributes
are same as that of @param. So they are documented under [@param](PARAM.md).


## @format

**Syntax:**

    @format formatName
    
**Example:**

    @format HtmlFormat
    
IF you want to force the request and or response format for a specific api 
method @format comment can be used. Make sure to add those formats to 
overriding formats.  

For example, `$restler->setOverridingFormats('HtmlFormat','UploadFormat');`


## @view

**Syntax:**

    @view Name
    
**Example:**

    @view profile.twig 
       
Specify the view file to be loaded by HtmlFormat for the given api method
as relative path from the `HtmlFormat::viewPath` and optionally include 
the template engine as the extension. If extension is missing, it uses 
the `HtmlFormat::template` for finding the template engine, 
and thus the extension.


## @errorView

**Syntax:**

    @errorView Name
    
**Example:**

    @errorView profile.twig
        
Similar to the `@view` but only used with an exception.


---------------
