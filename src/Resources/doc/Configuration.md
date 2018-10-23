*Do you like this bundle? [**Leave a &#9733;**](#js-repo-pjax-container) or run `composer global require symfony/thanks && composer thanks` to say thank you to all libraries you use in your current project, this one too!*

How to configure SHQ AWS SES Monitor Bundle
===========================================

The AWS SES Monitor Bundle uses the [`AWS SDK for PHP`](https://aws.amazon.com/it/documentation/sdk-for-php/)
 to connect and communicate with AWS API.

It is installed through the [`AwsBundle`](https://github.com/aws/aws-sdk-php-symfony) bundle.

To access to AWS you need a pair of credentials.

**Before continuing, if you have not already done it, [create this pair of credentials](https://docs.aws.amazon.com/en_us/IAM/latest/UserGuide/id_credentials_access-keys.html).**

The steps required to configure the `SHQAwsSesMonitorBundle` are those:

1. Get a pair of credentials from AWS to access its cloud services (so, also SES and SNS used to handle the notifications about bounces, complaints and deliveries);
2. Configure the AWS PHP SDK;
3. Configure the `SHQAwsSesMonitorBundle`.

*NOTE: If you are using Symfony Flex, the basic configuration files, environment variables and services will be created automatically.*

*REMEMBER to add the files created by Flex to `git` and then `commit`!*

So, if you don't have Flex installed, let's start by creating all the required files.

Create the configuration files and the environment variables (SF4)
------------------------------------------------------------------

To configure both `SHQAwsSesMonitorBundle` and `AwsBundle` we need to create the following files:

1. Create two files named `shq_aws_ses_monitor.yaml`, one in `config` folder and one in `config/packages/dev`;
2. Create two files named `aws.yaml`, one in `config/packages` folder and one in `config/packages/dev`;
3. Add to both your `.env` and `.env.dist` files the environment variables `AWS_KEY` and `AWS_SECRET`.

Remember: if you are using Flex all those files and variables are already created by the Flex recipes.

Step 3: Configure AWS
---------------------

As told, the `SHQAwsSesMonitorBundle` uses the [`AWS SDK for PHP`](https://aws.amazon.com/it/documentation/sdk-for-php/)
to connect and communicate with the AWS API.

AWS released a Symfony bundle to make easier to configure the services of the clients required to interact with the cloud services, so let's configure the `AwsBundle`.

Open the file `config/packages/aws.yaml` (or create it if you don't use Flex).

This is its current configuration:

```yaml
aws:
    version: latest
    region: us-east-1
    credentials:
        key: "%env(AWS_KEY)%"
        secret: "%env(AWS_SECRET)%"
```

As you can see, it currently only configures the `credentials`.

So, our first step is to set those credentials.

### Step 3.1: Configure AWS credentials in the `.env` file

Open the file `.env` that is in the root folder of your app.

There you will find this section created by Flex (add the variables if you don't use Flex):

```apacheconfig
###> aws/aws-sdk-php-symfony ###
AWS_KEY=not-a-real-key
AWS_SECRET=@@not-a-real-secret
###< aws/aws-sdk-php-symfony ###
```

So, you need to simply set here the credentials you get from the AWS console.

To know how to get your credentials, read [Managing Access Keys for IAM Users](https://docs.aws.amazon.com/en_us/IAM/latest/UserGuide/id_credentials_access-keys.html) on the AWS documentation.

Once you have configured the credentials, it's time to configure the services of the AWS PHP SDK clients required by the `SHQAwsSesMonitrBundle`.

### Step 3.2: Configure the AWS PHP SDK services

`SHQAwsSesMonitorBundle` requires two services to work:

1. Simple Email Service (SES);
2. Simple Notification Service (SNS).

The first is required to send emails through SES; the second is required to get the notifications SES produces about delivered, bounced and complained emails (more about this in a bit).

To configure the services, edit the `config/packages/aws.yaml` file adding the required services as follows:

```yaml
aws:
    version: latest
    region: us-east-1
    credentials:
        key: "%env(AWS_KEY)%"
        secret: "%env(AWS_SECRET)%"
    # Add this
    Ses:
        version: '2010-12-01'
    # and add this
    Sns:
        version: '2010-03-31'
```

As you can see you have to simply use the name of the AWS cloud service you want to use and add its version: the `AwsBundle` takes care of the rest.

Finally, configure the region from which you want your services have to run.
 
For example, as we are in Naples (Italy), we use the `eu-west-1` region instead of the pre-configured `us-east-1` one.

Step 4: Configure `SHQAwsSesMonitorBundle`
------------------------------------------

Now it's time to start configuring the `SHQAwsSesMonitorBundle`.

`SHQAwsSesMonitorBundle` does not only send emails through AWS SES.

It also helps you mange the identities you want your emails being sent from.

So, if you don't know how AWS SES works or what a SES Identity is, let's recap the concept of Identity.

### What are Identities in AWS SES

The entire AWS SES system revolves around a simple concept: the one of Identity.

As told by Amazon in [Verifying Identities in Amazon SES](https://docs.aws.amazon.com/en_us/ses/latest/DeveloperGuide/verify-addresses-and-domains.html):

> In Amazon SES, an identity is an email address or domain that you use to send email.

**`SHQAwsSesMonitorBundle` handles the entire verification process automatically.**

But to do this, you need to configure the identities you want to use in your application.

So, an Identity is basically an email or a domain.

The easiest way to configure your Identities, is to verify your entire domain: doing this, you'll be able to send emails from any address that belongs to that domain.

So, if you verify the domain `serendipityhq.com`, you'll be able to send emails using any address that belong to `serendipityhq.com`: `ciao@serendipityhq.com`, `hello@serendipityhq.com`, `aerendir@serendipityhq.com`.

In the AWS SES documentation you can find more information about the [verification of domains](https://docs.aws.amazon.com/en_us/ses/latest/DeveloperGuide/verify-domains.html) and about the [verification of emails](https://docs.aws.amazon.com/en_us/ses/latest/DeveloperGuide/verify-email-addresses.html).

### Configure the domain Identity (for production)

So, as told, the easiest way to verify the email addresses (they are identities) you want to send emails from is to verify the entire domain to which they belong to (and the domain is an Identity, too).

So, this is the minimal configuration required (ignore the `endpoint` node for the moment):

```yaml
# config/packages/shq_aws_ses_monitor.yaml

shq_aws_ses_monitor:
    endpoint:
        ...
    identities:
        serendipityhq.com: ~
```

This configuration is sufficient to use the built-in command `aws:ses:configure` that will configure your AWS SES account using the defaults (more on the full configuration in a bit).

DON'T USE THE COMMAND NOW: we need to configure the development environment first!

As told in the AWS SES documentation about how to verify domains, the domain name is case insensitive (while the emails are not: SerendipityHQ.com == serendipityhq.com while hello@serendipityhq.com != HELLO@serendipityhq.com).

Our domain Identity is configured: we should now configure a development test email Identity, so we can securely try the AWS SES service from our development machine.

### Configure the test email Identity (for development)

`SHQAwsSesMonitorBundle` provides a simple and secure way of testing locally the AWS SES service.

It will make you able to use a test email address to use locally: this is important because each Identity (being it an email or a domain) can register different endpoints.

We will speak about endpoints in a bit: for the moment, trust us, we added this feature for a real reason!

So, configure your test email identity opening the file `config/packages/dev/shq_aws_ses_monitor.yaml` and putting in it this minimal configuration:

```yaml
# config/packages/dev/shq_aws_ses_monitor.yaml

shq_aws_ses_monitor:
    endpoint:
        ...
    identities:
        test_aws@serendipityhq.com: ~
```

Again, ignore for the moment the `endpoint` node: we will configure it in a bit.

The test email identity have to be called `test_aws`: it is a fixed name defined in `SerendipityHQ\Bundle\AwsSesMonitorBundle\Util\IdentityGuesser::TEST_MAILBOX`.

You will configure only this Identity for the development environment and nothing else.

Now that we have our identities both for production and for development, let's configure the endpoint to which we will receive the notifications.

Step 5: Configure the endpoint for AWS SNS notifications
--------------------------------------------------------

When you send emails through AWS SES, it will inform you about what happened sending a notification to the endpoint you set when verifying the Identities on AWS SES.

The documentation about [Amazon SES Notifications Through Amazon SNS](https://docs.aws.amazon.com/ses/latest/DeveloperGuide/notifications-via-sns.html) clearly states that:

> In order to send email using Amazon SES, you must configure it to send bounce and complaint notifications by using one of the following methods:
>
> - By sending notifications to an Amazon SNS topic. The procedure for setting up this type of notification is included in this section.
> - ...

So, this means that when you send emails through AWS SES, it will inform you about what happened sending a notification to AWS SNS (or one of the other methods, but we will not use them).

You can read more about the different kinds of notifications in [Amazon SNS Notification Contents for Amazon SES](https://docs.aws.amazon.com/en_us/ses/latest/DeveloperGuide/notification-contents.html) on the AWS SES documentation.

`SHQAwsSesMonitorBundle` automates the entire integration between AWS SES and AWS SNS making your life as developer a lot easier!

To understand well what the bundles do, you need to first understand what AWS SES and AWS SNS require you to do do manually: we are going to explain you exactly this in the next parapgraphs.

To configure the integration between AWS SES and AWS SNS, the steps are more or less like those (not necessarily in this order):

1. You verify an Identity on AWS SES;
2. You set an AWS SNS topic the Identity must use to notify events;
3. You create a topic on AWS SNS to be used by an AWS SES Identity;
4. When you create the topic you set in it an URL to call when new notifications arrive;
5. When new notification arrive, the AWS SNS topic calls the endpoint you set.

So, this means the following two things:
1. When you create a topic on AWS SNS that has to be used by AWS SES, you need to set an endpoint URL and only one;
2. Each AWS SES Identity subscribes to a topic and only one.

And consequently, this means the following things:

1. On production you'll want to get the notifications at an URL like `https://serendipityhq.com/endpoints/aws`;
2. On development you'll want to get the notifications at an URL like `https://xxxxx.ngrok.com/endpoints/aws` (more about Ngrok in a bit);
3. You need a topic for `prod` notifications and a topic for `dev` notifications;
4. If you configure an Identity on `dev`, its endpoint will point to your local machine and if you use it to send emails on `prod` it will continue to send notifications to your `dev` URL.

So, this told, we have splitted the configuration: you have to set different configurations for `dev` and for `prod`.

Both configurations require three steps:

1. Define the name of the AWS SNS topics in the environment variables;
2. Configure the endpoint's URL in configuration files;
3. Configure the endpoint's route/controller.

Let's start by configuring the names of topics to use in AWS SNS.

### Step 5.1: Configure the topics

According to [AWS SNS documentation](https://docs.aws.amazon.com/sns/latest/dg/CreateTopic.html):

> A topic is a communication channel to send messages and subscribe to notifications. It provides an access point for publishers and subscribers to communicate with each other.

Basically, when you send an email through AWS SES, what happens is this:

1. AWS SES sends the email for real;
2. AWS SES receives the status of the email sent (bounces, complained or delivered);
3. AWS SES publishes this status to the topic on AWS SNS;
4. AWS SNS notify our endpoint on our app about the new message;
5. Our endpoint reads the notification and acts consequently (for example blacklisting bounced or complained email addresses).

So, to configure our topics we need to use environment variables as they are different for production and for development.

On your local machine, you will use the `.env` file; in `prod` you have to set them the way your server requires them to be set.

If you open your `.env` file, you will find a section like this (add it if you don't use Symfony Flex):

```apacheconfig
# .env

###> serendipity_hq/aws-ses-monitor-bundle ###
## Not required on production
NGROK_APP_SCHEME=https
NGROK_APP_HOST=xxxxxxxx.ngrok.io

## Variabes specific to AWS SES Monitor Bundle
AWS_SES_TOPIC_BOUNCES=your-app-dev-ses-bounces-topic
AWS_SES_TOPIC_COMPLAINTS=your-app-dev-ses-complaints-topic
AWS_SES_TOPIC_DELIVERIES=your-app-dev-ses-deliveries-topic
###< serendipity_hq/aws-ses-monitor-bundle ###
```

For the moment ignore the `NGROK_*` variables: we will use them later.

Instead, focus on the `your-app-dev-*-topic` variables.

They set the name of the topics to create on AWS SNS and to which subscribe the AWS SES identites (more about this in a bit).

As a general rule, we like to set the names of the topis following these three simple rules:

1. Differentiate development topics from the production ones;
2. Add an acronym of the app to differentiate those topics from all others eventually already created in the AWS SNS account;
3. Highlight the fact that the topics are for AWS SES.

So, following these rules, we always have two sets of topics for AWS SES: one for production and one for development.

Do you remember what we said opening this section? An AWS Identity can subscribe to only one AWS SNS topic and each AWS SNS topic can have only one endpoint set: so, to differentiate the topics to use in `prod` from the topics to use on `dev`, we need different topics with different names and this will make us able to send emails through AWS SES both on `prod` and also from our `dev` machines, but using development identities (that we will configure in a bit). 
So, our sets of topics appear like those:

**Development topics** (set in the `.env` file)

- `your-app-dev-ses-bounces-topic`
- `your-app-dev-ses-complaints-topic`
- `your-app-dev-ses-deliveries-topic`

**Production topics** (set the way your hosting requires they being set)

- `your-app-prod-ses-bounces-topic`
- `your-app-prod-ses-complaints-topic`
- `your-app-prod-ses-deliveries-topic`

This differentiation is fundamental to recognize which topics belongs to which environment and to which app when using the AWS SNS control panel: all topics, in fact, are created in the same AWS SNS account!

*NOTE: You can also configure only one topic for all the three kind of notifications (deliveries, bounces and complaints), but we've found more comfortable to use three different topics for each kind of them. Anyway, always differentiate by environment (`dev` != `prod`)*

Now that we have our topics configured, it's time to configure the URL of our endpoint.

### Step 5.2: Configure the URL of the endpoint

The endpoint to which AWS SNS will send the notifications it receives from AWS SES has this form:

    [https][serendipityhq.com]/[endpoints/aws]

- `[https]` is the scheme;
- `[serendipityhq.com]` is the host;
- `[endpoints/aws]` is the route that points to the controller that manages the notifications from AWS SNS.

As the route is always the same both on `prod` and on `dev`, let's start configuring it.


#### Step 5.2.1: Configuring the route (the path) of the endpoint

This is really easy: if you use Symfony Flex it is automatically configured for you.

If you don't use Flex, add a file `shq_aws_ses_monitor.yaml` in the folder `config/routes`.

The content of the file is this:

```yaml
# config/routes/shq_aws_ses_monitor.yaml

_shq_aws_ses_monitor:
    resource: '@SHQAwsSesMonitorBundle/Resources/config/routing.yml'
    prefix: /endpoints/aws
```

Now we need to configure the URL of our app.

#### Step 5.2.2: Configuring the URL (the schema and the host) of our endpoint (for production)

To configure the scheme and the host of our endpoint we need again to take into account the differences between `prod` and `dev` environments.

So, open the file `config/packages/shq_aws_ses_monitor.yaml`.

It contains this:

```yaml
# config/packages/dev/shq_aws_ses_monitor.yaml

shq_aws_ses_monitor:
    endpoint:
        # We suggest to use an environment variable: '%env(APP_SCHEME)%'
        scheme: 'https'
        # We suggest to use an environment variable: '%env(APP_HOST)%'
        host: 'example.com'
    identities:
        serendipityhq.com: ~
```

You have to simply set the scheme and the host of you app on `prod`: really simple!

#### Step 5.2.3: Configuring the URL (the schema and the host) of our endpoint (for development)

If you open the file `config/packages/dev/shq_aws_ses_monitor.yaml` you will read this

```yaml
# config/packages/dev/shq_aws_ses_monitor.yaml

shq_aws_ses_monitor:
    endpoint:
        # Use Ngrok to test the bundle on development
        scheme: '%env(NGROK_APP_SCHEME)%'
        host: '%env(NGROK_APP_HOST)%'
    identities:
        test_aws@serendipityhq.com: ~
```

Ngrok is a tunneling tool that creates a public URL to be used as endpoint in AWS SNS. When this endpoint is called, Ngrok tunnels the request to your local machine.

Basically, AWS SNS thinks it is speaking with a public server but in reality it is speaking with your local machine: this makes us able to test AWS SES and AWS SNS from our development machines.

We will tell you how to use Ngrok deeper in the next paragraphs. For the moment it is sufficient to say that once started, Ngrok will give you an URL like `https://xxxxxx.ngrok.com`: this is the URL we will use to configure the `dev` endpoint node of the `SHQAwsSesMonitorBundle`.

As you are noticing, we use environment variables: this is simply a question of convenience: each time you start Ngrok it will give you different URLs, so you need to update the configuration.

If you update directly the configuration file, you would each time be asked to commit by `git` and this is really annoying.

Using environment variables, instead, permits us to edit the `.env` file that is ignored by `git` and so, when we update the Ngrok URL we don't have to take care of `git`.

This is helpful also when collaborating on the same app with more developers.

As you don't know how Ngrok works, for the moment ignore the `dev` configuration of the `endpoint` node, but remember that it is here: we will use it in the next paragraphs!

Step 6: Update your database scheme
-----------------------------------

Now it's time to update your database to create the tables required by `SHQAwsSesMonitorBundle`.

Execute this Doctrine's command:

```console
$ bin/console doctrine:schema:update --force
```

No need for a migration as we are going to create new tables related to `SHQAwsSesMonitor`.

ATTENTION: If you modified any other Doctrine entity adding, removing or editing fields, this command will apply these modifications, too. In this case it is better to use migrations!

Step 7: Configure Swiftmailer to use AWS SES
--------------------------------------------

The last step is to configure [Swiftmailer](https://symfony.com/doc/current/email.html) to instruct it to use AWS SES to send emails.

If you haven't already installed it, [run this command](https://symfony.com/doc/current/email.html#installation):

```console
$ composer require symfony/swiftmailer-bundle
```

Open the file `.env` and find this block:

```apacheconfig
###> symfony/swiftmailer-bundle ###
# For Gmail as a transport, use: "gmail://username:password@localhost"
# For a generic SMTP server, use: "smtp://localhost:25?encryption=&auth_mode="
# Delivery is disabled by default via "null://localhost"
MAILER_URL=null://localhost
###< symfony/swiftmailer-bundle ###
```

You need to change it to something like this:

```apacheconfig
###> symfony/swiftmailer-bundle ###
# For Gmail as a transport, use: "gmail://username:password@localhost"
# For a generic SMTP server, use: "smtp://localhost:25?encryption=&auth_mode="
# Delivery is disabled by default via "null://localhost"
MAILER_URL=smtp://email-smtp.eu-west-1.amazonaws.com:587?encryption=tls&username=YOUR-AWS-USERNAME&password=YOUR-AWS-PASSWORD
###< symfony/swiftmailer-bundle ###
```

Let' break it in parts:

```
[smtp]://[email-smtp.eu-west-1.amazonaws.com]:[587]?encryption=tls&[username=YOUR-AWS-USERNAME]&[password=YOUR-AWS-PASSWORD]
```

- `smtp` is the protocol to use;
- `email-smtp.eu-west-1.amazonaws.com`: is the SMTP host;
- `587`: is the port;

Then there are the username and password to use to connect via SMTP.

You can find the host and the port in your AWS SES console in `Email Sending > SMTP Settings`.

From that page you can create also a pait of credentials to use in the string required to configure Swiftmailer: follow the procedure, then set the pair in the Swiftmailer's configuration string. 

Now that you have completed the configuration of the `SHQAwsSesMonitorBundle`, it is time to configure your AWS SES and AWS SNS accounts to integrate them.

*Do you like this bundle? [**Leave a &#9733;**](#js-repo-pjax-container) or run `composer global require symfony/thanks && composer thanks` to say thank you to all libraries you use in your current project, this one too!*

([Go back to index](Index.md)) | Next step: [Integrate on `dev`](Integration-dev.md)
