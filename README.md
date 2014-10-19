#yii2-cms#

CMS для Yii2.

#Возможности#

 * Модули: авторизация, пользователи, меню, страницы, новости, теги, поиск, медиа менеджер и т.д.
 * Древовидные категории новостей.
 * Встроенная система контроля версий документов.
 * Поиск на основе Elastic Search.
 * SEO-friendly адреса страниц (ЧПУ)

#Установка#

Сперва устанавливаем [advanced application template](http://www.yiiframework.com/doc-2.0/guide-tutorial-advanced-app.html).

### Настройка Nginx
```nginx
server {
    charset utf-8;
    client_max_body_size 128M;

    listen 80; ## listen for ipv4
    #listen [::]:80 default_server ipv6only=on; ## listen for ipv6

    server_name yiicms.proj;
    root        /home/roman/www/yiicms/frontend/web;
    index       index.php;

    access_log  /home/roman/www/yiicms/log/access.log;
    error_log   /home/roman/www/yiicms/log/error.log;

    #необходимо добавить в папку frontend/web симлинк на backend/web под названием admin
	  location /admin/ {
        try_files $uri $uri/ /admin/index.php?$args;
    }

    location / {
		# Redirect everything that isn't a real file to index.php
        try_files $uri $uri/ /index.php?$args;
    }

    # uncomment to avoid processing of calls to non-existing static files by Yii
    location ~ \.(js|css|png|jpg|gif|swf|ico|pdf|mov|fla|zip|rar)$ {
        try_files $uri =404;
    }
    #error_page 404 /404.html;

	location ~ \.php$ {
        include fastcgi_params;
        fastcgi_pass unix:/var/run/php5-fpm.sock;
        try_files $uri =404;
    }

    location ~ /\.(ht|svn|git) {
        deny all;
    }
}
```

### Установка yii2-cms
Запускаем через composer

    php composer.phar require --prefer-dist menst/yii2-cms "*"
    
Или добавляем  

    "menst/yii2-cms": "*"
    
в require секцию composer.json файла.

### Настройка расширения
Заменяем фронтенд, бэкенд и консольное приложения на соответсвующие из данного расширения. Для этого правим файлы:
 * /backend/web/index.php

```php
  $application = new \menst\cms\frontend\Application($config); // yii\web\Application($config);
```

 * /frontend/web/index.php   

```php
  $application = new \menst\cms\backend\Application($config); // yii\web\Application($config);
```

 * /yii.php

```php
  $application = new \menst\cms\console\Application($config); // yii\console\Application($config);
```

Нужно отредактировать стандартный конфиг: /frontend/config/main.php, /backend/config/main.php

```php   
  'components' => [
      'user' => [
          //'identityClass' => 'common\models\User',  //закоментировать или удалить эту строку
          'enableAutoLogin' => true,
      ],
    ]
```
### Миграция
Добавляем таблицы в БД.

    php yii migrate --migrationPath=@menst/cms/migrations

### Поиск(опционально)
 * Установить [Elasticsearch](http://www.elasticsearch.org/guide/en/elasticsearch/reference/current/_installation.html)
 * Применяем миграцию для Elasticsearch

```
  php yii migrate --migrationPath=@menst/cms/migrations/elasticsearch
```

 * Добавляем в бутстрап фронтенда и бэкенда модуль 'cms/search'. Правим файлы /frontend/config/main.php и /backend/config/main.php

```php
  'bootstrap' => ['log', 'cms/search'],
```
  
