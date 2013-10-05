#OAuth Server#

This documentation is broken down into the following sections. They are laid out in what was meant to be a sequential order but feel free to read them in any order you like:

- Initial Setup
- [The Actors Involved](actors-involved.md)
- [Grant Types](grant-types.md)
- [Configuring Your Server](oauth-config.md) 
	- Client Registration
    - Authentication Integration
	- Scope
	- Configuring expiry
	- Authorization Templates

If you're not familiar with OAuth, it's worth familiarizing yourself with the 
basics and in particular understanding the various workflows that OAuth 2.0 offers.
The following two links may help:

- [Official OAuth Specification](http://tools.ietf.org/html/draft-ietf-oauth-v2): 
a bit dry but if you want the "facts" they're there.
- [OAuth2 Simplified](http://aaronparecki.com/articles/2012/07/29/1/oauth2-simplified): 
a nicely summarized overview of how to think through the key aspects of OAuth 2.0

Furthermore, it is worth taking a look at Restler's demonstration of a [Protected API](http://restler3.luracast.com/examples/_005_protected_api/readme.html) to understand how to integration the OAuth functionality into your API. Finally, you should also leverage the great documentation that the [OAuth 2.0 Server PHP provides](http://bshaffer.github.io/oauth2-server-php-docs/).

##Initial Setup##

The great thing about getting the software is that you probably already have it! This assumes you have already installed `Restler`. Restler uses the PHP [Composer](http://getcomposer.org/) package manager which allows it to install all third party dependencies and keep them up-to-date. Now that last statement -- keep it up-to-date -- is made *easy* by composer but not done completely automatically. If you haven't installed Restler in the past few days it's probably a good idea to update all these dependencies. This can be done by:

    cd [your-restler-directory]
	composer update
	
That's it. Pretty easy. If you're the curious type, take a look at the file called `composer.json` (also in the root directory of Restler). There you'll see all of the dependencies explained and the one you should make particular note of is the one that says something like: 

    "bshaffer/oauth2-server-php":"v1.0"
	
The version number might not be the same on yours but it's important to note which version you *are* on. The dependencies between Restler and the OAuth server are loosely coupled so in most cases you will want to be on the latest version that's available. 

The other dependency that has some relevancy to your future OAuth career is a templating library called `twig`. More on how this is used in the Configuration section but you'll find it in the `composer.json` file as something like:

    "twig/twig": "v1.13.0"
	
Ok, if you've followed up to this point you have all the software you need and it's up-to-date. There still two small thing to check: 

1. make sure `public/examples/_015_oauth2_server/cache` has write permissions to create the compiled template files
2. make sure `public/examples/_015_oauth2_server/OAuth2/db` has write permission, this is where `oauth.sqlite` file be created at run time


