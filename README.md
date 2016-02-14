<p align="center">
  x
</p>
<p align="center">
  <i>Though the complexes, reach the destination.</i>
</p>

&nbsp;

# Davai

前行者（давай）是一個基於 PHP 的路由類別，很適合用在 RESTful 和 MVC 架構的網站上。

&nbsp;

# 特色

1. 支援反向路徑。

2. 可自訂路由規格。

3. 支援 GET, POST, PUT, DELETE, PATCH 多種方式。

4. 可命名路由變數。


&nbsp;

# 教學

&nbsp;

# 範例

你需要先在你的 `.htacess` 新增這樣的規則，並將之後的網頁都導向 `index.php`，

如此一來才能夠在 `index.php` 中處理任何的路由事宜。

```
RewriteEngine on
# PHP Extension To None PHP Extension
# RewriteRule ^ /%1 [R=301,NE,L]
RewriteCond %{THE_REQUEST} ^[A-Z]{3,}\s/(.+)\.php[^\s]* [NC]
RewriteRule ^ - [R=404,L]


# Hide Extension Of PHP
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME}.php -f
RewriteCond %{REQUEST_FILENAME} !-l 
RewriteRule ^(.+?)/?$ $1.php [L]

RewriteRule ^.*$ index.php
```

&nbsp;

接著初始化你的前行者，像下面這樣。

```php
$davai = new Davai();
```

&nbsp;

接下來開始撰寫路徑，其中的 `{i:userID}` 的 `i` 意思是任何數字，而 `userID` 則是變數名稱，

你可以再詳細的教學文件中找到說明。

```php
$davai->get('/user/{i:userId}', function($userId)
{
    echo '抓到了！你正要讀取編號為 ' . $userId . ' 的使用者對吧！';
});
```

&nbsp;

最後拜訪 `/user/123` 你應該會得到下列的結果。

```php
抓到了！你正要讀取編號為 123 的使用者對吧！
```

&nbsp;

# 可參考文件

[dannyvankooten@AltoRouter](https://github.com/dannyvankooten/AltoRouter)

[Laravel - HTTP Routing](https://ihower.tw/blog/archives/6483)

[HTTP Verbs: 談 POST, PUT 和 PATCH 的應用](https://laravel.tw/docs/5.2/routing)

[Using HTTP Methods for RESTful Services](http://www.restapitutorial.com/lessons/httpmethods.html)

[Accessing Incoming PUT Data from PHP](http://www.lornajane.net/posts/2008/accessing-incoming-put-data-from-php)

[PHP detecting request type (GET, POST, PUT or DELETE)](http://stackoverflow.com/questions/359047/php-detecting-request-type-get-post-put-or-delete)
