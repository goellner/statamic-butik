---
description: >-
  This is the best way to install butik, if you already got an existing project
  running.
---

# Manual

Follow those instructions step by step to set everything up.  

**Check out our starter kit to get you started as fast as possible**

{% page-ref page="starter-kit.md" %}

## Set up your .env file

```bash
DB_CONNECTION=sqlite
DB_FOREIGN_KEYS=true

MOLLIE_KEY=test_XXXXXXXX
```

### SQLite as Database

With this setup, we will use SQLite as a file-based database. It's important to activate the foreign keys as shown above.

More information: [Laravel documentation](https://laravel.com/docs/7.x/database). 

### MySQL as Database

If you prefer using MySQL, feel free to do so. A default setup might look similar to this example:

```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=yourdatabasename
DB_USERNAME=yourusername
DB_PASSWORD=yourpassword
```

More information: [Laravel documentation](https://laravel.com/docs/7.x/database). 

## Run the butik setup command

This script will migrate your database and set up some default values. Everything can be customized to a later point, but it will get you started as fast as possible.

```bash
php artisan butik:setup
```

## Define default country

You need to define your default control in your config file.

{% page-ref page="configuration.md" %}

This country will be default country, as long as the user does not choose another country to ship to.  
This is important with only one country to ship to as well. 

## Allow webhooks

For the best and most stable integrations with the [mollie](https://www.mollie.com/en) payment service provider, we do work with webhooks. They need to »call« your website. To do so, you need to add a CSRF token exception.

```php
// app/Http/Middleware/VerifyCsrfToken.php

protected $except = [
    'butik/webhook/mollie',
];
```

{% hint style="danger" %}
Your orders will get through, but your orders won't get updated, _butik_ can't send any emails without knowing if a purchase has been successfull or not.
{% endhint %}

## Set up Redis queues

A Redis queue driver will give your page a speed boost, especially with sending emails.

{% hint style="info" %}
Butik does work with the default Laravel / Statamic set up. You don't need to set it up, we do recommend it though. 
{% endhint %}

The short and incomplete version, on how to set it up:

* Add in your .env file `QUEUE_DRIVER=redis`
* Install predis via composer `composer require predis/predis`

[The complete documentation](https://laravel.com/docs/master/redis)

##  Set up emails

Let me remind you to set up your email settings inside the statamic env file.

{% hint style="info" %}
Send yourself a test mail via the Statamic control panel to check if they are working correctly
{% endhint %}

 [Statamic email configuration documentation](https://statamic.dev/email)

## Done

{% hint style="success" %}
Congratulations. Your basic setup is finished.
{% endhint %}

