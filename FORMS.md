# Forms

Forms class generates html forms from http method and target url.

Following attributes can be used for customizing the generated forms.

## @label

### With @param

**Syntax:**

    @param type $name [Description] {@label Custom Name}
    
**Example:**

    @param string $name [Description] {@label Customer's Name}
    
Restler automatically creates a label for each param by using camelCasing and underscore in parameter names. For example `$firstName`, `$first_name` to `First Name`. If you want to override and specify a label yourself use the above annotation.

------------
 
### With @return
   
**Syntax:**

    @return type {@label Custom Name}
    
**Example:**

    @return array {@label Sign In}

Restler by default uses `Submit` as the default text for form submit buttons. You can customize that using the above annotation.
 

## @field

**Syntax:**

    @param type $name [Description] {@field InputType}
    
**Example:**

    @param int $age [Description] {@field number} {@min 6} {@max 22}
    
Html field type used for capturing the value. 
Some possible values are,
 - input
 - checkbox (for boolean)
 - radio (requires @choice)
 - password
 - select (requires @choice)
 - text
 - hidden
 
 When not specified, it will try to guess the field type   


## @message

**Syntax:**

    @param type $name [Description] {@message text}
    
**Example:**

    @param int $age [Description] {@message age restriction requires age between 6 and 22} {@min 6} {@max 22}

Custom error message to display when validation fails.

## Emmet Templates

### @form, @input, @textarea, @radio, @select, @submit, and @fieldset

Allows you to customize how a form element rendering from the selected form style.
Use Emmet syntax for that purpose. Take a look at FormStyles.php for an example.

