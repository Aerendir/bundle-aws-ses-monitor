*Do you like this bundle? [**Leave a &#9733;**](#js-repo-pjax-container) or run `composer global require symfony/thanks && composer thanks` to say thank you to all libraries you use in your current project, this one too!*

How to install AWS SES Monitor Bundle
=====================================

Before you start, we highly suggest you to install
[Symfony Flex](https://symfony.com/doc/current/setup/flex.html): it will automate a lot
of the boilerplate and configuration work.

If you don't have Symfony Flex installed on your applicatioon, we suggest you to dedicate
some time to [upgrade your Symfony app to support it](https://symfony.com/doc/current/setup/flex.html#upgrading-existing-applications-to-flex).

Your life will be easier! 

A note about AwsBundle
----------------------

This bundle requires the bundle [`AwsBundle`](https://github.com/aws/aws-sdk-php-symfony).

It is a bundle that installs the [AWS PHP SDK](https://github.com/aws/aws-sdk-php) automating a basic configuration of
the configurations and Symfony's services required to access the AWS cloud services.

We have fund it useful too maintain our configurations clean, organized and standardized, so we use it extensively.

During the configuration of the `SHQAwsSesMonitorBundle` you will register this bundle, too (nothing complex, anyway).

If you use Symfony Flex, you have to do nothing as Flex will do all for you.

Step 1: Download the SerendipityAwsSesMonitorBundle
---------------------------------------------------

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

We suggest to explicitly install also the `AwsBundle`: this will save your AWS configuration if you decide to remove our bundle in future:

```console
composer require serendipity_hq/aws-ses-monitor-bundle aws/aws-sdk-php-symfony
```

If you want to explicitly install only `SHQAwsSesMonitorBundle`, instead, run this command:

```console
composer require serendipity_hq/aws-ses-monitor-bundle
```

The `AwsBundle` will be installed and configured anyway as a dependency.

This command requires you to have Composer installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

If you have Flex installed, you are done: you can also skip directly to the [Configuration section](Configuration.md)
of this documentation: Flex will automatically register this bundle and the
`aws/aws-sdk-php-symfony` bundle on which this one depends.

Flex will also create the configuration files required to make the two bundle work.

If you don't have Flex installed (or want to understand what Flex did), continue reading. 

Step 2: Enable the `SHQAwsSesMonitorBundle` and the `AwsBundle`
-------------------------------------------------------------------------

`SHQAwsSesMonitorBundle` relies on the bundle `AwsBundle`: as we are interacting with AWS,
the use of this bundle will make sure you will configure the AWS access keys, configurations and services
only once.

So, enable the two bundles.
 
**If you are on SF^4**, add the following lines in the `config/bundles.php` file of your project:

```php
<?php
// config/bundles.php

return [
    // ...

    Aws\Symfony\AwsBundle::class => ['all' => true],
    SerendipityHQ\Bundle\AwsSesMonitorBundle\SHQAwsSesMonitorBundle::class => ['all' => true],
];
```

**If you are on SF^3**, add the following lines in the `app/AppKernel.php` file of your project:

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

            new Aws\Symfony\AwsBundle(),
            new SerendipityHQ\Bundle\AwsSesMonitorBundle\SHQAwsSesMonitorBundle(),
        );

        // ...
    }

    // ...
}
```

The bundles are configured: you can go to the next step.

*Do you like this bundle? [**Leave a &#9733;**](#js-repo-pjax-container) or run `composer global require symfony/thanks && composer thanks` to say thank you to all libraries you use in your current project, this one too!*

([Go back to index](Index.md)) | Next step: [Configure](Configuration.md)
