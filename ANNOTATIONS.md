Supported Annotations
---------------------

You may use the following php doc comments to annotate your methods.
All tags except @url can also be defined at the class level.

<table>
    <tr>
        <th>Tag</th>
        <th>Description</th>
    </tr>
    <tr>
        <td>@url</td>
        <td>
        Syntax:
        <pre>@url GET|POST|PUT|PATCH|DELETE custom/{dynamic}/route</pre>
        Example:
        <pre>@url POST authors/{id}/books</pre>
        Overrides auto routes and creates manual routes. use as many as you need
        </td>
    </tr>
    <tr>
        <td>@access</td>
        <td>
        Syntax:
        <pre>@access private|public|protected|hybrid</pre>
        Example:
        <pre>@access protected</pre>
        Access control for api methods. PHPDoc only supports private and public,
        Restler adds <b>protected</b> for api that needs authentication,
        <b>hybrid</b> for api that enhances resulting data for authenticated users.
        </td>
    </tr>
    <tr>
        <td>@smart-auto-routing</td>
        <td>
        Syntax:
        <pre>@smart-auto-routing true|false</pre>
        Example:
        <pre>@smart-auto-routing false</pre>
        Smart auto routing is enabled by default. Avoids creating multiple
        routes that can increase the ambiguity when set to true. when a method
        parameter is optional it is not mapped to the url and should only be
        used in request body or as query string `/resource?id=value`.
        When a parameter is required and is  scalar, it will be mapped as
        part of the url `/resource/{id}`
        </td>
    </tr>
    <tr>
        <td>@class</td>
        <td>
        Syntax:
        <pre>@class ClassName {@propertyName value}</pre>
        Example:
        <pre>@class AccessControl {@requires user} {@level 5}</pre>
        Inject property of the specified class with specified value
        </td>
    </tr>
    <tr>
        <td>@cache</td>
        <td>
        Syntax:
        <pre>@cache headerCacheControlValue</pre>
        Example:
        <pre>@cache max-age={expires}, must-revalidate</pre>
        Specify value to set CacheControl Header, it can use @expires value as
        shown in the example
        </td>
    </tr>
    <tr>
        <td>@expires</td>
        <td>
        Syntax:
        <pre>@expires numberOfSeconds</pre>
        Example:
        <pre>@expires 30</pre>
        Sets the content to expire immediately when set to zero alternatively
        you can specify the number of seconds the content will expire
        </td>
    </tr>
    <tr>
        <td>@throttle</td>
        <td>
        Syntax:
        <pre>@throttle numberOfMilliSeconds</pre>
        Example:
        <pre>@throttle 3000</pre>
        Sets the time in milliseconds for bandwidth throttling, which will
        become the minimum response time for each API request.
        </td>
    </tr>
    <tr>
        <td>@status</td>
        <td>
        Syntax:
        <pre>@status httpStatusCode</pre>
        Example:
        <pre>@status 201</pre>
        Sets the HTTP Status code for the successful response.
        </td>
    </tr>
    <tr>
        <td>@header</td>
        <td>
        Syntax:
        <pre>@header httpHeader</pre>
        Example:
        <pre>@header Link: &lt;meta.rdf>; rel=meta</pre>
        Sets or overrides the specific HTTP Header.
        </td>
    </tr>
    <tr>
        <td>@param</td>
        <td>
        Syntax:
        <pre>@param [type] Name [Description] {@name value}</pre>
        Example:
        <pre>@param int $num1 increment value {@min 5} {@max 100}</pre>
        Standard @param comment that sets the type and description of a parameter.
        Supported child attributes are documented under 
        <a href="PARAM.md">@param</a>
        </td>
    </tr>
    <tr>
        <td>@throws</td>
        <td>
        Syntax:
        <pre>@throws httpStatusCode [Reason]</pre>
        Example:
        <pre>@throws 404 No Author for specified id</pre>
        Documents possible error responses for the API call.
        </td>
    </tr>
    <tr>
        <td>@return</td>
        <td>
        Syntax:
        <pre>@return type [Description]</pre>
        Example:
        <pre>@return Author an instance of iValueObject</pre>
        Documents the structure of success response, user defined classes must
        extend iValueObject.
        </td>
    </tr>
    <tr>
        <td>@var</td>
        <td>
        Syntax:
        <pre>@var [type] [Description] {@name value}</pre>
        Example:
        <pre>@var int policy age {@min 18} {@max 100}</pre>
        Stadard @var comments that are used with properties of model classes.
        Supported child attributes are documented under <a href="PARAM.md">@param</a>
        </td>
    </tr>
</table>