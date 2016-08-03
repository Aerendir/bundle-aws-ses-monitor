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

The full configuration is as follows. The set values are the default ones:

```yaml
# Default configuration for "AwsSesMonitorBundle"
aws_ses_monitor:
    db_driver: orm #OPTIONL. Currently only ORM supported.
    model_manager_name: null # OPTIONAL. Set this if you are using a custom ORM model manager.
    aws_config:
        credentials_service_name: 'client.amazon.credentials' # REQUIRED. Here the NAME (not the service itself!) of the credentials service set in the previous step.
                                                              # If you omit this, the bundle looks for client.aws.credentials service.
        region: "%amazon.aws.eu_region%" # OPTIONAL. If omitted, the bundle sets this to us-east-1.
        ses_version: "%amazon.ses.version%" # OPTIONAL. The AWS SES API version to use. Defaults to 2010-12-01.
        sns_version: "%amazon.sns.version%" # OPTIONAL. The AWS SNS API version to use. Defaults to 2010-03-31.
    mailers:
        - default
    bounces:
        topic:
            name: ses-your_app-bounces-topic # OPTIONAL. Required only to use the configuration commands. 
            endpoint:
                route_name: _aws_ses_monitor_bounces_endpoint # OTIONAL. The endpoint AWS SNS calls when SES reports a bounce.
                protocol: http # OPTIONAL. The protocol to use. Accepted values are: http, HTTP, https, HTTPS.
                host: your_domain.com # REQUIRED. The hostname of your project when in production.
        filter:
            enabled: true # OPTIONAL. If false, no filtering of bounced recipients will happen. Complained are ever filtered.
            soft_as_hard: false # OPTIONAL. If true, the temporary bounces counts as hard bounces
            max_bounces: 5 # OPTIONAL. The max number of bounces before the address is blacklisted
            soft_blacklist_time: forever # OPTIONAL. NOT YET IMPLEMENTED. The amount of time for wich a temporary bounced address has to be blacklisted. If "forever" emails will never been sent in the future.
            hard_blacklist_time: forever # OPTIONAL. NOT YET IMPLEMENTED. The amount of time for wich an hard bounced address has to be blacklisted. If "forever" emails will never been sent in the future.
            force_send: false # OPTIONAL. If you want to force the sending of e-maills to bounced e-mails. VERY RISKY!
    complaints:
        topic:
            name: ses-your_app-complaints-topic # OPTIONAL. Required only to use the configuration commands.
            endpoint:
                route_name: _aws_ses_monitor_complaints_endpoint # OTIONAL. The endpoint AWS SNS calls when SES reports a complaint.
                protocol: http # OPTIONAL. The protocol to use. Accepted values are: http, HTTP, https, HTTPS.
                host: your_domain.com # REQUIRED. The hostname of your project when in production.
        filter:
            enabled: true # OPTIONAL. If false, no filtering of complained recipients will happen. "false" IS VERY RISKY!
            blacklist_time: forever # OPTIONAL. NOT YET IMPLEMENTED. The amount of time for wich an address has to be blacklisted. If "forever" emails will never been sent in the future.
            force_send: false # OPTIONAL. If you want to force the sending of e-maills to complained e-mails. VERY RISKY!
    deliveries:
        enabled: true # OPTIONAL. By default also the deliveries are tracked.
        topic:
            name: ses-your_app-deliveries-topic # OPTIONAL. Required only to use the configuration commands.
            endpoint:
                route_name: _aws_ses_monitor_deliveries_endpoint # OTIONAL. The endpoint AWS SNS calls when SES reports a delivery.
                protocol: http # OPTIONAL. The protocol to use. Accepted values are: http, HTTP, https, HTTPS.
                host: your_domain.com # REQUIRED. The hostname of your project when in production.
        # Has no filter options
```

Add routing file for bounce endpoint (feel free to edit prefix)

```yaml
# app/config/routing.yml
bouncer:
    resource: '@AwsSesMonitorBundle/Resources/config/routing.yml'
    prefix: /aws/endpoints
```

Step 4: Update your database schema
-----------------------------------

```
$ php app/console doctrine:schema:update --force
```

([Go back to index](Index.md)) | Next step: [Integrate](Integration.md)
