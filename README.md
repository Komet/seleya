Seleya
======

Seleya ist ein Videoportal, dass vorrangig Vorlesungsaufzeichnung anzeigt.
Als Backend wird Opencast Matterhorn verwendet.

Installation
------------

node.js und less sollten installiert sein. 

Ubuntu:
```
sudo apt-get install nodejs node-less
```

```
git clone https://github.com/Komet/seleya.git
cd seleya
cp app/config/parameters.yml.default app/config/parameters.yml
vi app/config/parameters.yml
curl -s https://getcomposer.org/installer | php
./composer.phar update --no-dev
php app/console assetic:dump --env=prod
php app/console assets:install --symlink
php app/console doctrine:schema:update --force
php app/console doctrine:fixtures:load
```
