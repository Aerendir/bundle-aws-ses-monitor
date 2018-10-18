*Do you like this bundle? [**Leave a &#9733;**](#js-repo-pjax-container) or run `composer global require symfony/thanks && composer thanks` to say thank you to all libraries you use in your current project, this one too!*

How to configure AWS SES Monitor Bundle
=======================================

The AWS SES Monitor Bundle uses the [`AWS SDK for PHP`](https://aws.amazon.com/it/documentation/sdk-for-php/)
 to connect and communicate with AWS API.

It is installed through the [`AwsBundle`](https://github.com/aws/aws-sdk-php-symfony) bundle.

To access to AWS you need a pair of credentials.

**Before continuing, if you have not already done it, [create this pair of credentials](https://aws.amazon.com/it/developers/access-keys/).**

The steps required to configure the `SHQAwsSesMonitorBundle` are those:

1. Get a pair of credentials from AWS to access its cloud services (so, also SES and SNS used to handle the notifications about bounces, complaints and deliveries);
2. Configure the AWS PHP SDK;
3. Configure the `SHQAwsSesMonitorBundle`.

*NOTE: If you are using Symfony Flex, the basic configuration files, environment variables and services will be created automatically.*

So, if you don't have Flex installed, let's start by creating all the required files.

Step 3: Create the configuration files and the environment variables (SF4)
--------------------------------------------------------------------------

To configure both `SHQAwsSesMonitorBundle` and `AwsBundle` we need to create the following files:

1. Create two files named `shq_aws_ses_monitor.yaml`, one in `config` folder and one in `config/packages/dev`;
2. Create two files named `aws.yaml`, one in `config/packages` folder and one in `config/packages/dev`;
3. Add to both your `.env` and `.env.dist` files the environment variables `AWS_KEY` and `AWS_SECRET`.

Remember: if you are using Flex all those files and variables are already created by the Flex recipes.

Step 3: Configure AWS
---------------------

Step 3: Set the required environment variables and services
-----------------------------------------------------------

First, add the required environment variables.

On your local machine, you will use the `.env` file. In `prod` you have to set them the way your server requires them to be set.

The variables in the `.env` file are different from the variables required on your production servers.

    # .env
    
    ## Not required on production
    NGROK_APP_SCHEME=https
    NGROK_APP_HOST=xxxxxxxx.ngrok.io
    
    ## Convenient variables that make easier configuration
    # Keep attention: this may be httpS on production!
    APP_SCHEME=http
    
    # This has to be the same of bin/console server:start on development machines
    APP_HOST=127.0.0.1:8000
    
    ## This may be required by other bundles that use AWS
    AWS_ACCESS_KEY_ID=your-aws-key-id
    AWS_ACCESS_KEY_SECRET==your-aws-key-secret
    
    ## Variabes specific to AWS SES Monitor Bundle
    AWS_SES_TOPIC_BOUNCES=tbme-dev-ses-bounces-topic
    AWS_SES_TOPIC_COMPLAINTS=tbme-dev-ses-complaints-topic
    AWS_SES_TOPIC_DELIVERIES=tbme-dev-ses-deliveries-topic

*NOTE 1: Do not forget to add those parameters also to your `.env.dist`.*

*NOTE 2: In this example we use `NGROK_APP_SCHEME` and `NGROK_APP_HOST` together with `APP_SCHEME` and `APP_HOST`. This is because using `APP_SCHEME` and `APP_HOST` is a convenient way of having those information at hand as they are required by a lot of bundles and configurations. BUT we have also `NGROK_APP_SCHEME` and `NGROK_APP_HOST` as those are parameters only used in development when really sending emails through AWS SES.
Maybe you have sufficient experience with Symfony to already know the convenience of having both pairs of configs: if you are not understanding what we are sayng, feel free to open an issue: we will explain to you further on this topic.*

### Create an AWS\Credentials service

Create the `Credentials` service needed by the `AWS\Client` to pass it your access information:

```yaml
# config/services.yaml

services:
    ...
    Aws\Credentials\Credentials:
        arguments: ['%env(AWS_ACCESS_KEY_ID)%', '%env(AWS_ACCESS_KEY_SECRET)%']
```

STEP 4: CONFIGURE AWS SES MONITOR BUNDLE
----------------------------------------

The full configuration is as follows. The set values are the default ones:

```yaml
# config/packages/shq_aws_ses_monitor.yaml (used for all environments)
shq_aws_ses_monitor:
    aws_config:
        # REQUIRED. Here the NAME (not the service itself!) of the credentials service set in the previous step.
        # If you omit this, the bundle looks for Aws\Credentials\Credentials service.
        credentials_service_name: 'Aws\Credentials\Credentials'

        # OPTIONAL. If omitted, the bundle sets this to eu-west-1.
        # If you use this, remember to add it to .env and .env.dist files
        region: "%env(AWS_REGION)%"

        # OPTIONAL. The AWS SES API version to use. Defaults to 2010-12-01.
        # If you use this, remember to add it to .env and .env.dist files
        ses_version: "%env(AWS_SES_VERSION)%"

        # OPTIONAL. The AWS SNS API version to use. Defaults to 2010-03-31.
        # If you use this, remember to add it to .env and .env.dist files
        sns_version: "%env(AWS_SNS_VERSION)%"

    endpoint:
        # OPTIONAL. The scheme to use. Defaults to "https". Accepted values are: http, HTTP, https, HTTPS.
        scheme: '%env(APP_SCHEME)%'

        # REQUIRED. The hostname of your project when in production. No default value.
        host: '%env(APP_HOST)%'

    # On which mailers you want to activate the filter plugin.
    # This requires that at least one between bounces and complaints filters are activated.
    # If both bounces and complaints filter are not activated, the filter plugin is not
    # added to any mailer.
    mailers:
        - default

    # Configuration for bounced emails
    bounces:
        # OPTIONAL. If false, no tracking of bounced recipients will happen.
        # Without tracking them, bounced emails cannot be filtered.
        # "false" IS VERY RISKY for the health of your AWS SES account.
        track: true

        # REQUIRED. The name of the topic to create on SNS to which SES will notify bounced emails.
        # Requires you to add AWS SES Monitor routings to your configuration (see next section "Add routing")
        topic: ses-your_app-bounces-topic

        # Configuration for the SwiftMailer filter plugin
        filter:            
            # OPTIONAL. If true, the temporary bounces counts as hard bounces
            # More infor about the difference here:
            # - https://docs.aws.amazon.com/ses/latest/DeveloperGuide/deliverability-and-ses.html#bounce
            # - https://aws.amazon.com/it/blogs/messaging-and-targeting/email-definitions-bounces/
            soft_as_hard: false

            # OPTIONAL. The max number of bounces before the address is blacklisted (no more emails will be sent to it)
            max_bounces: 5

            # OPTIONAL. NOT YET IMPLEMENTED. The amount of time for wich a temporary bounced address has to be blacklisted. If "forever" emails will never been sent in the future.
            soft_blacklist_time: forever

            # OPTIONAL. NOT YET IMPLEMENTED. The amount of time for wich an hard bounced address has to be blacklisted. If "forever" emails will never been sent in the future.
            hard_blacklist_time: forever

            # OPTIONAL. If you want to force the sending of e-mails to bounced e-mails. VERY RISKY!
            force_send: false

    # Configuration for complained emails
    complaints:
        # OPTIONAL. If false, no tracking of complained recipients will happen.
        # Without tracking them, complained emails cannot be filtered.
        # "false" IS VERY RISKY for the health of your AWS SES account.
        track: true

        # REQUIRED. The name of the topic to create on SNS to which SES will notify complained emails.
        # Requires you to add AWS SES Monitor routings to your configuration (see next section "Add routing")
        topic: ses-your_app-complaints-topic

        # Configuration for the SwiftMailer filter plugin
        filter:
            # OPTIONAL. NOT YET IMPLEMENTED. The amount of time for wich an address has to be blacklisted. If "forever" emails will never been sent in the future.
            blacklist_time: forever

            # OPTIONAL. If you want to force the sending of e-mails to complained e-mails. VERY RISKY!
            force_send: false

    # Configuration for delivered emails
    deliveries:
        # OPTIONAL. If false, no trcking of delivered emails will happen.
        # Not trcking them has no relevant impact, BUT makes impossible to
        # understand the health of an email address
        track: true

        # REQUIRED. The name of the topic to create on SNS to which SES will notify delivered emails.
        # Requires you to add AWS SES Monitor routings to your configuration (see next section "Add routing")
        topic: ses-your_app-deliveries-topic
```

Step 5: Add routing file for bounce endpoint
--------------------------------------------

To get notifications from AWS SES through SNS, you need to activate the Aws Ses Monitor Bundle endpoint route: SNS will send this route to notify what happened with the email addresses to which you sent emails.

With this notification from SNS, Aws Ses Monitor Bundle is able to understand if is secure to send emails to a particular address or not. 

To configure the endpoint route, add this to your routing configuration:

```yaml
# config/routes/shq_aws_ses_monitor.yaml
_shq_aws_ses_monitor:
    resource: '@SHQAwsSesMonitorBundle/Resources/config/routing.yml'
    prefix: /endpoints/aws
```

Step 6: Update your database scheme
-----------------------------------

Now it's time to update your database to create the tables required by Aws Ses Monitor bundle.

A simple forced update is sufficient and also secure as you will not go to modify any one of your still existent tables:

```console
app/console doctrine:scheme:update --force
```

Now that you have completed the configuration, it is time to integrate your app with AWS SES and SNS.

*Do you like this bundle? [**Leave a &#9733;**](#js-repo-pjax-container) or run `composer global require symfony/thanks && composer thanks` to say thank you to all libraries you use in your current project, this one too!*

([Go back to index](Index.md)) | Next step: [Integrate](Integration.md)
