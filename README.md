Bouncer bundle
==============

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/56ca074b-524c-4ebe-84f4-f7d0772814b0/mini.png)](https://insight.sensiolabs.com/projects/56ca074b-524c-4ebe-84f4-f7d0772814b0)
[![Build Status](https://travis-ci.org/shivas/bouncer-bundle.svg)](https://travis-ci.org/shivas/bouncer-bundle)
[![Total Downloads](https://img.shields.io/packagist/dt/shivas/bouncer-bundle.svg?style=flat)](https://packagist.org/packages/shivas/bouncer-bundle)

Symfony2 bundle to automate AWS SES users using swiftmailer to filter out bouncing email recipients inside project.

AWS SES users know, if you get big amount of Bouncing emails, AWS will send you into probation period.
In some cases, there is no easy way to solve issue. This bundle solves problem transparently filtering recipients lists trough own database built my listening on AWS SNS Bounce topic that it creates and hooks to your identity.

Requirements:
=============

1. You use AWS SES to send your emails
2. You have AWS API key
3. You have confirmed email identity (email or whole domain)

Installation
============

Step 1: Download the Bundle
---------------------------

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```bash
$ composer require shivas/bouncer-bundle "~0.1"
```

This command requires you to have Composer installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

Step 2: Enable the Bundle
-------------------------

Then, enable the bundle by adding the following line in the `app/AppKernel.php`
file of your project:

```php
<?php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...

             new Shivas\BouncerBundle\ShivasBouncerBundle(),
        );

        // ...
    }

    // ...
}
```

Step 3: Add configuration
-------------------------

```yaml
# Default configuration for "ShivasBouncerBundle"
shivas_bouncer:
    db_driver:            orm # currently only ORM supported
    model_manager_name:   null # if using custom ORM model manager, provide name, otherwise leave as null 
    aws_api_key:
        key:                  ~ # Required, your AWS API KEY
        secret:               ~ # Required, your AWS API SECRET
        region:               us-east-1 # Required, region of AWS to use
    bounce_endpoint:
        route_name:           _shivasbouncerbundle_bounce_endpoint
        protocol:             HTTP # HTTP or HTTPS
        host:                 localhost.local # hostname of your project when in production
    filter:
        enabled:              true # if false, no filtering of bounced recipients will happen
        mailer_name:          # array of mailer names where to register filtering plugin
            - default
 ```
 
 Add routing file for bounce endpoint (feel free to edit prefix)
 
```yaml
# app/config/routing.yml
bouncer:
    resource: @ShivasBouncerBundle/Resources/config/routing.yml
    prefix: /aws/endpoints
```
 
Step 4: Update your database schema
-----------------------------------

```
$ php app/console doctrine:schema:update --force
```
 
Step 5: Setup subscription to Bounce topic
------------------------------------------

Run in console:
```
./app/console swiftmailer:sns:setup-bounce-topic Bounce
```

This will use your AWS keys to fetch available identities, and provide you option to choose what identities to subscribe to.
"Bounce" in console is name of topic to setup (Naming rules should follow AWS naming rules for topics)

What will happen:

1. Bounce topic will be created
2. All chosen identities will be configured to send Bounce notifications to that topic
3. Your project url will be provided as HTTP or HTTPS (configuration) endpoint for AWS
4. Automatic subscription confirmation will occur on AWS request to confirm (if your endpoint is reachable)

Contribute
----------

Contribute trough issues or pull request. 

Todo
----

Mapping for MongoDB and other supported databases by Doctrine
