Simple PHP Redirect
===================

This is a _really_ simple project. And as it's only my second foray into PHP, I doubt it's very well coded.

The application is just a URI redirect, so for example if I wanted to link to the Cyotek Blog (which is currently at https://www.cyotek.com/blog) from an application I could create an entry named "blog" pointing to that URL and then my application would call "https://go.cyotek.com?id=blog". If this was later changed to https://blog.cyotek.com/, I can update the entry in the database and existing code will find the new location.

Of course, if I _did_ do that, I'd still have put some sort of redirect in place which makes this project a bit moot for URL's that you have full control over. However if you make use of external services such as PayPal then this project could be more useful. Another slight benefit is that URL's should be slightly shorter, although this isn't really the goal of this project. 

Requirements
------------

* PHP with PDO and SQLite support
* Raw access to the database as there's no management interface to change settings or add new URI's.

Setup
-----

Create a SQLite database named `redirects.db` and apply `redirects.db.sql` to this database.

Populate the `Url` table with URL's.

There are two configuration settings in the `Config` table, **Title** and **HomePage**. Use these to configure the information displayed when viewing index.php without providing a redirect ID, which is slightly more friendly than display an "not set" error.

Calling the redirect
--------------------

Call `index.php` with a `id` query string parameter that contains either the numeric ID or slug of the URL to redirect.

### Parameter injection

If your URL entry includes `{name}` sequences, then the redirect will try and replace these with query string parameters.

For example, if you had a URL entry named `docs` set with the URL pattern `https://docs.cyotek.com/cyowcopy/{version}/`, you could call it as `?id=docs&version=1.2`, resulting in a final URL of `https://docs.cyotek.com/cyowcopy/1.2/`. Missing parameters will be substituted with blank values. 

Using the management pages
--------------------------

There are three basic overview pages, `activity.php`, `error.php` and `list.php`. These are "protected" via the use of an API key, which can be supplied by either the `ApiKey` header or `apikey` query string parameter.

To configure the api key, change the value of the **ApiKey** entry in the `Config` table. Note that if the key is left blank, the management pages will not load. 

> There are no configuration pages for adding, removing or updating URL entries - you'd need to dig into the database to do that. I do plan on adding them at some point, but at the same time I don't want to worry about injection attacks or weak security given my beginners knowledge of PHP.

