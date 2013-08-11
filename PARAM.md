Attributes for `@param` comments
--------------------------------

Following attributes can be used with `@param`, they apply for `@var` comment
that is added to model class properties. Just replace 

    @param [type] $name ...

with 

    @var [type] ...

in the examples below

<table>
     <tr>
        <td>@from</td>
        <td>
        Syntax:
        <pre>@param [type] $name [Description] {@from url|body|query|head}</pre>
        Example:
        <pre>@param string $name user name {@from body}</pre>
        override parameter mapping, define where to expect the parameter from.
        Value should be one of the following
        <ul>
            <li><code>path</code> as part of url</li>
            <li><code>query</code> as part of the query string</li>
            <li><code>body</code> as part of the body of the request</li>
            <li><code>head</code> as part of the http header</li>
        </ul>
       Please note that unlike <code>path</code> and <code>head</code> other
       values are only suggestive and primarly used by API Explorer to build 
       the interface
        </td>
    </tr>
    <tr>
        <td>@type</td>
        <td>
        Syntax:
        <pre>@param string $name [Description] {@type email|date|datetime|timestamp}</pre>
        <pre>@param array  $name [Description] {@type className}</pre>
        Example:
        <pre>@param string $email email id {@type email}</pre>
        sub types for validation and documentation purpose. 
        email will validate the given string as email.
        date, datetime, and timestamp will be validated as standard mysql date formats respectively
        <pre>@param array $author array of Authors {@type Author}</pre>
        States that the given array of Author instances.
        <br/><br/>
        Take a look at <a href="public/tests/param/Type.php">Type API Class</a> and tests in        
        <a href="features/tests/param/type.feature">type.feature</a>
        </td>
    </tr>
    <tr>
        <td>@choice</td>
        <td>
        Syntax:
        <pre>@param string $name [Description] {@choice option1,option2...}</pre>
        Example:
        <pre>@param string $gender {@choice male,female,third}</pre>
        Value for the parameter should match one of the choices, used for validation
        </td>
    </tr>
    <tr>
        <td>@min @max</td>
        <td>
        Syntax:
        <pre>@param string|int|float|array $name [Description] {@min value}{@max value}</pre>
        Example:
        <pre>@param string $name 3 to 10 characters in length {@min 3}{@max 10}</pre>
        <pre>@param array $items at least one item is needed  {@min 1}</pre>
        <pre>@param int $age valid age should be 18 or above  {@min 18}</pre>
        Minimum and maximum values for a parameter. For string and array parameters it is applied
        to the length. For number parameters it sets the range for the value.
        <br/><br/>
        Take a look at <a href="public/tests/param/MinMax.php">MinMax API Class</a> and tests in        
        <a href="features/tests/param/minmax.feature">minmax.feature</a>
        </td>
    </tr>
    <tr>
        <td>@fix</td>
        <td>
        Syntax:
        <pre>@param string|int|float|array $name [Description] {@fix true}</pre>
        Example:
        <pre>@param string $name 3 to 10 characters in length {@max 10}{@fix true}</pre>
        suggests the validator to attempt fixing the validation problem. In the above example
        Validator will trim off the excess characters instead of throwing an exception
        <br/><br/>
        Take a look at <a href="public/tests/param/MinMaxFix.php">MinMaxFix API Class</a> and tests in 
        <a href="features/tests/param/minmaxfix.feature">minmaxfix.feature</a>
        </td>
    </tr>
</table>

More to follow