# @param and @var

Following attributes can be used with `@param`, they also apply for `@var` comment
that is added to model class properties.

Just replace

    @param [type] $name ...

with 

    @var [type] ...

in the examples below

## @from

**Syntax:**

    @param [type] $name [Description] {@from path|body|query|head}
    
**Example:**

    @param string $name user name {@from body}

Overrides the parameter mapping, defines where to expect the parameter from.
Value should be one of the following

 - **path** as part of url
 - **query** as part of the query string
 - **body** as part of the body of the request
 - **head** as part of the http header
 
Please note that unlike path and head other values are only suggestive and
primarily used by API Explorer to build the interface
    
## @type

**Syntax:**

string

    @param string $name [Description] {@type email|date|datetime|timestamp}

array
 
    @param array  $name [Description] {@type className}
    
**Examples:**

    @param string $email email id {@type email}
    
Sub types for validation and documentation purpose. Email will validate the
given string as email. Date and datetime will be validated as standard mysql
date formats respectively. Timestamp will be validated as Unix timestamp.

    @param array $author array of Authors {@type Author}
    
States that the given array of Author instances. 

Take a look at [Type API Class](public/tests/param/Type.php) and tests in
[type.feature](features/tests/param/type.feature)

## @choice

**Syntax:**

    @param string $name [Description] {@choice option1,option2...}
    
**Example:**

    @param string $gender {@choice male,female,third}

Value for the parameter should match one of the choices, used for validation.

## @min & @max

**Syntax:**

    @param string|int|float|array $name [Description] {@min value}{@max value}

**Examples:**

string

    @param string $name 3 to 10 characters in length {@min 3}{@max 10}

array

    @param array $items at least one item is needed  {@min 1}

integer

    @param int $age valid age should be 18 or above  {@min 18}

Minimum and maximum values for a parameter. For string and array parameters
it is applied to the length. For numeric parameters it sets the range for the
value.

Take a look at [MinMax API Class](public/tests/param/MinMax.php) and tests in
[minmax.feature](features/tests/param/minmax.feature)

## @fix

**Syntax:**

    @param string|int|float|array $name [Description] {@fix true}
    
**Example:**

    @param string $name 3 to 10 characters in length {@max 10}{@fix true}
    
Suggests the validator to attempt fixing the validation problem. In the above
example Validator will trim off the excess characters instead of throwing an
exception

Take a look at [MinMaxFix API Class](public/tests/param/MinMaxFix.php) and
tests in [minmaxfix.feature](features/tests/param/minmaxfix.feature)


## @pattern

**Syntax:**

    @param string $name [Description] {@pattern /REGEX_HERE/REGEX_OPTIONS}
    
**Example:**

    @param string $password at least one alpha and one numeric character
    						{@pattern /^(?:[0-9]+[a-z]|[a-z]+[0-9])[a-z0-9]*$/i}
    
Used by the validator to make sure the parammeter value matches the regex pattern. It uses preg_match for this verification. Please note that `/` should be used as the delimiter.

Take a look at [MinMaxFix API Class](public/tests/param/Validation.php) and
tests in [minmaxfix.feature](features/tests/param/validation.feature)

## @message

**Syntax:**

    @param string|int|float $name [Description] {@message value}
    
**Example:**

    @param string $password Password 
    						{@message Strong password with at least one alpha and one numeric character is required}
    
Used by the validator to show a custom error message when invalid value is submitted. Use it to list the requirements for a parameter

Take a look at [MinMaxFix API Class](public/tests/param/Validation.php) and
tests in [minmaxfix.feature](features/tests/param/validation.feature)

## @example

**Syntax:**

    @param string|int|float $name [Description] {@example value}
    
**Example:**

    @param string $name Name {@example Arul Kumaran}
    
Used by the explorer to prefill the value for the parameter