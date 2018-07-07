Do you like this bundle? [**Leave a &#9733;**](#js-repo-pjax-container)!

How to integrate AWS SES with your application
==============================================

Step 5: Setup bounces and complaints handling
---------------------------------------------

Now it's time to create our topics for bounces and complaints. As told in the [post](http://sesblog.amazon.com/post/TxJE1JNZ6T9JXK/-Handling-span-class-matches-Bounces-span-and-Complaints.pdf)
Handling Bounces and Complaints on the [Amazon SES Blog](http://sesblog.amazon.com/), topics should follow the following nomenclature:

    ses-bounces-topic

Something like `ses-your_app-bounces-topic` may be better to avoid conflicts with other apps of yours.

Set those names in your configuration:

```yaml
SHQAwsSesMonitorBundle
aws_seSHQAwsSesMonitorBundle    bounces:
        topic:
            name: ses-your_app-bounces-topic # OPTIONAL. Required only to use the configuration commands. 
            ...
    complaints:
        topic:
            name: ses-your_app-complaints-topic # OPTIONAL. Required only to use the configuration commands.
            ...
    deliveries:
        enabled: true # OPTIONAL. By default also the deliveries are tracked.
        topic:
            name: ses-your_app-deliveries-topic # OPTIONAL. Required only to use the configuration commands.
            ...
```

Aws Ses Monitor Bundle will create the topics on Amazon SNS. During creation it will also set the topic to call an endpoint on your application each time a new notification is created.
This way you don't need to use also the AWS SQS service as you will never lose the notifications that are immediately persisted directly into the application.

```yaml
SHQAwsSesMonitorBundle
shq_aws_ses_monitor:
    ...
    bounSHQAwsSesMonitorBundle
            name: ses-your_app-bounces-topic # OPTIONAL. Required only to use the configuration commands. 
            endpoint:
                ...
                host: yourapp.com # REQUIRED. The hostname of your project when in production.
        ...
    complaints:
        topic:
            name: ses-your_app-complaints-topic # OPTIONAL. Required only to use the configuration commands.
            endpoint:
                ...
                host: yourapp.com # REQUIRED. The hostname of your project when in production.
        ...
    deliveries:
        enabled: true # OPTIONAL. By default also the deliveries are tracked.
        topic:
            name: ses-your_app-deliveries-topic # OPTIONAL. Required only to use the configuration commands.
            endpoint:
                ...
                host: yourapp.com # REQUIRED. The hostname of your project when in production.
```

Now you are ready to launch the console commands to create the topics on AWS SNS.

So, run in console:
```
app/console aws:ses:monitor:setup:bounces-topic
```

and then

```
app/console aws:ses:monitor:setup:complaints-topic
```

and then

```
app/console aws:ses:monitor:setup:deliveries-topic
```

This will use your AWS Credentials to fetch available identities and will provide you the option to choose what identities to subscribe to.

What will happen:

1. `ses-your_app-bounces-topic` topic will be created
2. All chosen identities will be configured to send Bounce notifications to that topic
3. Your project url will be provided as HTTP or HTTPS (configuration) endpoint for AWS
4. Automatic subscription confirmation will occur on AWS request to confirm (if your endpoint is reachable)

Do you like this bundle? [**Leave a &#9733;**](#js-repo-pjax-container)!

([Go back to index](Index.md)) | Next step: [Test](Test.md)
