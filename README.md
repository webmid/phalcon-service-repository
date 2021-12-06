# Description

In this repository we learn that how to create service layer between database and controller in Phalcon framework.

After install, You may need change some config in public/index.php or service.php or config.php



## Installation

After clone repository

```
composer install
```
Config database:
```
app/config/config.php
```
Config database for unit test:
```
tests/Unit/AbstractUnitTest.php
```
for migration database table:
```
phalcon migration run
```

Run Unit Test:

```
vendor/bin/phpunit
```

Add new service:

Add this code in public/index.php

```php
$di->set('postServiceRepo', function () {
    return new MyApp\Repos\ServiceRepository(new \MyApp\Models\Post());
});
```

Then in controller:

```php
 $postRepo = $this->di->getPostServiceRepo();
 $posts = $postRepo->all()->toArray();
```

