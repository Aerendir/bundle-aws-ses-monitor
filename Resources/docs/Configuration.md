How to configure AWS SES Monitor Bundle
=======================================

The AWS SES Monitor Bundle uses the [`AWS SDK for PHP`](https://aws.amazon.com/it/documentation/sdk-for-php/)
 to connect and communicate with AWS API.

The bundle creates an `Aws\SesClient` and an `Aws\SnsClient` that it uses to perform all task required to setup the environment on AWS.

To access to AWS you need a pair of credentials. Before continuing, if you have not already done it, [create this pair of credentials](https://aws.amazon.com/it/developers/access-keys/).


Step 3: Configure the AWS Client
--------------------------------

First, add the configuration parameters to `parameters.yml`:

```yaml
parameters:
    ...
    amazon.aws.key: 'your_key'
    amazon.aws.secret: 'your_secret'
    amazon.aws.eu_region: 'eu-west-1' # You can omit this. If omitted, the bundle sets this to us-east-1
    amazon.ses.version: '2010-12-01' # You can omit this. If omitted, the bundle sets this to 2010-12-01
    amazon.sns.version: '2010-03-31' # You can omit this. If omitted, the bundle sets this to 2010-03-31
```

NOTE: Do not forget to add those parameters also to your `parameters.dist.yml`.

### Create an AWS\Credentials service

Create the `Credentials` service needed by the `AWS\Client` to pass it your access information:
 
```yaml
 services:
     ...
     client.amazon.credentials:
         class: Aws\Credentials\Credentials
         arguments: ["%amazon.aws.key%", "%amazon.aws.secret%"]
```

STEP 4: CONFIGURE AWS SES MONITOR BUNDLE
----------------------------------------

The minimum configuration required is as follows:

```yaml
# Default configuration for "ShivasBouncerBundle"
aws_ses_monitor:
    model_manager_name: null # if using custom ORM model manager, provide name, otherwise leave as null
    aws_config:
        # Here the NAME (not the service itself!) of the credentials service set in the previous step.
        # If you omit this, the bundle looks for client.aws.credentials service.
        credentials_service_name: 'client.amazon.credentials'
        region: "%amazon.aws.eu_region%" # You can omit this. If omitted, the bundle sets this to us-east-1
        ses_version: "%amazon.ses.version%" # You can omit this. If omitted, the bundle sets this to 2010-12-01
        sns_version: "%amazon.sns.version%" # You can omit this. If omitted, the bundle sets this to 2010-03-31
    bounces_endpoint:
        route_name:           _aws_monitor_bounces_endpoint
        protocol:             HTTP # HTTP or HTTPS
        host:                 localhost.local # hostname of your project when in production
    complaints_endpoint:
        route_name:           _aws_monitor_complaints_endpoint
        protocol:             HTTP # HTTP or HTTPS
        host:                 localhost.local # hostname of your project when in production
    filter:
        enabled:              true # if false, no filtering of bounced recipients will happen
        filter_not_blacklists: false # if false, all temporary bounces will not make that address to be filtered forever
        number_of_bounces_for_blacklist: 5 # The number of bounces required to permanently blacklist the address
        mailer_name:          # array of mailer names where to register filtering plugin
            - default
```

```yaml
# Default configuration for "ShivasBouncerBundle"
aws_ses_monitor:
    db_driver: orm # currently only ORM supported
    model_manager_name: null # if using custom ORM model manager, provide name, otherwise leave as null
    aws_config:
        # Here the NAME (not the service itself!) of the credentials service set in the previous step.
        # If you omit this, the bundle looks for client.aws.credentials service.
        credentials_service_name: 'client.amazon.credentials'
        region: "%amazon.aws.eu_region%" # You can omit this. If omitted, the bundle sets this to us-east-1
        ses_version: "%amazon.ses.version%" # You can omit this. If omitted, the bundle sets this to 2010-12-01
        sns_version: "%amazon.sns.version%" # You can omit this. If omitted, the bundle sets this to 2010-03-31
    bounces_endpoint:
        route_name:           _aws_monitor_bounces_endpoint
        protocol:             HTTP # HTTP or HTTPS
        host:                 localhost.local # hostname of your project when in production
    complaints_endpoint:
        route_name:           _aws_monitor_complaints_endpoint
        protocol:             HTTP # HTTP or HTTPS
        host:                 localhost.local # hostname of your project when in production
    filter:
        enabled:              true # if false, no filtering of bounced recipients will happen
        filter_not_blacklists: false # if false, all temporary bounces will not make that address to be filtered forever
        number_of_bounces_for_blacklist: 5 # The number of bounces required to permanently blacklist the address
        mailer_name:          # array of mailer names where to register filtering plugin
            - default
```

Add routing file for bounce endpoint (feel free to edit prefix)

```yaml
# app/config/routing.yml
bouncer:
    resource: '@ShivasBouncerBundle/Resources/config/routing.yml'
    prefix: /aws/endpoints
```

Step 4: Update your database schema
-----------------------------------

```
$ php app/console doctrine:schema:update --force
```

([Go back to index](Index.md)) | Next step: [Integrate](Integration.md)
