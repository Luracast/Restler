# Forms

Forms class generates html forms from http method and target url.

Following attributes can be used for customizing the generated forms.

## @label

### With @param

**Syntax:**

    @param type $name [Description] {@label Custom Name}
    
**Example:**

    @param string $name [Description] {@label Customer's Name}
    
Restler automatically creates label for each param by using camelCasing and underscore in parameter names. For example `$firstName`, `$first_name` to `First Name`. If you want to override and specify a label yourself use the above annotation.

------------
 
### With @return
   
**Syntax:**

    @return type {@label Custom Name}
    
**Example:**

    @return array {@label Sign In}

Restler by default uses `Submit` as the default text for form submit buttons. You can customize that using the above anotation.
 

## @field


## @message


## @form


## @input


## @textarea


## @radio


## @select


## @submit


## @fieldset




