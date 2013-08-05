Child Anotations for `@param`
-----------------------------

Following child annotations can be used with `@param`

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
        states that the given array of Author instances
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
</table>

More to follow