Html Format <requires>PHP >= 5.3</requires>
-----------

 <tag>view</tag>
 <tag>html</tag>


Add a custom view to your data using view templates in various formats.
It currently supports

 - php (default)
 - twig (requires `"twig/twig"`)
 - mustache / handlebar (requires `"mustache/mustache"`)

> **Note:-** if you want your favourite template library to be supported
> submit a pull request, just follow the style of existing ones as a guide

When HtmlFormat is used with out defining a view it uses debug view to present
data and more information

> This API Server is made using the following php files/folders
> 
> * index.php      (gateway)
> * Tasks.php      (api)
> * iTasks.php      (helper)
> * Resources.php      (api)
> * restler.php      (framework)
> * JsonFormat.php      (format)
> * HtmlFormat.php      (format)

This API Server exposes the following URIs

    GET    resources                  ⇠ Luracast\Restler\Resources::index()
    GET    resources/generatenickname ⇠ Luracast\Restler\Resources::generateNickname()
    GET    resources/v{version}       ⇠ Luracast\Restler\Resources::get()
    GET    resources/{id}-v{version}  ⇠ Luracast\Restler\Resources::get()
    POST   tasks                      ⇠ Tasks::post()
    GET    tasks                      ⇠ Tasks::index()
    GET    tasks/setdb                ⇠ Tasks::setDB()
    PATCH  tasks/{id}                 ⇠ Tasks::patch()
    GET    tasks/{id}                 ⇠ Tasks::get()
    DELETE tasks/{id}                 ⇠ Tasks::delete()








*[index.php]: _013_html/index.php
*[Tasks.php]: _013_html/Tasks.php
*[iTasks.php]: _013_html/DB/iTasks.php
*[Resources.php]: ../../vendor/Luracast/Restler/Resources.php
*[restler.php]: ../../vendor/restler.php
*[JsonFormat.php]: ../../vendor/Luracast/Restler/Format/JsonFormat.php
*[HtmlFormat.php]: ../../vendor/Luracast/Restler/Format/HtmlFormat.php

