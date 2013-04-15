Seleya
======

Seleya ist ein Videoportal, dass vorrangig Vorlesungsaufzeichnungen anzeigt.
Als Backend wird Opencast Matterhorn verwendet.

Installation
------------

node.js und less sollten installiert sein, bspw. für Ubuntu:

```
sudo apt-get install nodejs node-less
```

Installation von Seleya:

```
git clone https://github.com/Komet/seleya.git
cd seleya
cp app/config/parameters.yml.default app/config/parameters.yml
vi app/config/parameters.yml
curl -s https://getcomposer.org/installer | php
./composer.phar update --no-dev
php app/console assetic:dump --env=prod
php app/console assets:install --symlink
php app/console doctrine:migrations:migrate
php app/console doctrine:fixtures:load
```

Apache Konfiguration:

```
...
DocumentRoot /var/www/seleya/htdocs/web
<Directory /var/www/seleya/htdocs/web>
    Options Indexes FollowSymLinks MultiViews
    AllowOverride None
    Order allow,deny
    allow from all
    <IfModule mod_rewrite.c>
        RewriteEngine On
        RewriteCond %{REQUEST_FILENAME} !-f
        RewriteRule ^(.*)$ /app.php [QSA,L]
    </IfModule>
</Directory>
...
```
