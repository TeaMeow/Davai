<p align="center">
  x
</p>
<p align="center">
  <i>Though the complexes, reach the destination.</i>
</p>

&nbsp;

# Davai

Davai（давай）is a routing class based on PHP, especially for RESTful and MVC web applications.

&nbsp;

# Features

1. Davai supports reversing paths.

2. You can customize the settings of routes.

3. Davai can handle requests in GET, POST, PUT, DELETE and PATCH methods.

4. You are able to name the route variables.


&nbsp;

# Getting Started

If you are running Apache server, please create a `.htacess` and copy the code below into it.
Next, please redirect ALL the requests to `index.php`. Thus Davai can receive all the requests and handle them.

```
RewriteEngine on
# PHP Extension To None-PHP Extensions
# RewriteRule ^ /%1 [R=301,NE,L]
RewriteCond %{THE_REQUEST} ^[A-Z]{3,}\s/(.+)\.php[^\s]* [NC]
RewriteRule ^ - [R=404,L]

# Hide PHP Extensions
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME}.php -f
RewriteCond %{REQUEST_FILENAME} !-l
RewriteRule ^(.+?)/?$ $1.php [L]

RewriteRule ^.*$ index.php
```

&nbsp;

And, please construct a Davai class:

```php
$davai = new Davai();
```

&nbsp;

Time to write the routes. The `i` in `[i:userID]` means any integers, and `userID`  is the variable name.

We have a document of this, please check it out later.

```php
$davai->get('/user/[i:userId]', function($userId)
{
    echo 'Gotcha! You are reading the data of the user who has id ' . $userId . ' right?';
});
```

&nbsp;

Access `/user/123`, and you should get this shown on your screen:

```php
Gotcha! You are reading the data of the user who has id 123 right?
```

&nbsp;

# References

[dannyvankooten@AltoRouter](https://github.com/dannyvankooten/AltoRouter)

[Laravel - HTTP Routing](https://ihower.tw/blog/archives/6483)

[HTTP Verbs: 談 POST, PUT 和 PATCH 的應用](https://laravel.tw/docs/5.2/routing)

[Using HTTP Methods for RESTful Services](http://www.restapitutorial.com/lessons/httpmethods.html)

[Accessing Incoming PUT Data from PHP](http://www.lornajane.net/posts/2008/accessing-incoming-put-data-from-php)

[PHP detecting request type (GET, POST, PUT or DELETE)](http://stackoverflow.com/questions/359047/php-detecting-request-type-get-post-put-or-delete)
