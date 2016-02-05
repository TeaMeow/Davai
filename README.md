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
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule . index.php [L]
```

&nbsp;

接著初始化你的前行者，像下面這樣。

```php
$davai = Davai();
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
