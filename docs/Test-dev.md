*Do you like this bundle? [**Leave a &#9733;**](#js-repo-pjax-container) or run `composer global require symfony/thanks && composer thanks` to say thank you to all libraries you use in your current project, this one too!*

How to test AWS SES e-mails sending
===================================

AWS SES make available some [test email addresses](https://docs.aws.amazon.com/en_us/ses/latest/DeveloperGuide/mailbox-simulator.html).

Those addresses simulates the behavior of mailboxes that returns the various possible status codes:

- Success;
- Hard Bounce;
- Soft Bounce;
- Complaint;
- Suppressionlist.

`SHQAwsSesMonitorBundle` has a command to send an email to each one of those addresses: this way AWS SES will send back the notifications and you can see how exactly the bundle behaves and how it manages them.

The command is `aws:ses:monitor:test:swiftmailer`.

But before you can use it from your development machine, you have to be sure Swiftmailer is not sending emails to the `TEST_DELIVERY_ADDRESS` you can set in your application.

Step 11: Ensuring SwiftMailer `dev` configuration is correct
------------------------------------------------------------

Before you can send emails to the test mailboxes of AWS SES, you need to be sure that nothing is configured in `dev` in a way that can prevent the emails from being sent or delivered.

Below are the instructions to correctly configure SwiftMailer in `dev` environments.

###Enabling delivery in SwiftMailer in a `dev` envrionment, too

SwiftMailer has a handy option that [disables the real sending of emails](https://symfony.com/doc/current/email/dev_environment.html#disabling-sending): this is useful in `dev` environments to avoid sending emails to real recipients when developing the application.

To test the `SHQAwsSesMonitorBundle` you need to enable the sending of emails for real also in `dev` environment.

Otherwise, the emails will not be delivered to AWS SES test mailboxes.

To enable the sending of emails in SwiftMailer when it runs in `dev` environment, open the file `config/packages/dev/swiftmailer.yaml`.

There there is a config node called `disable_delivery`: be sure it is set to false or it is not present at all (the default value is, in fact, `false`, so if it isn't there, the emails are sent for real):

```yaml
# config/packages/test/swiftmailer.yaml

swiftmailer:
    disable_delivery: false
```

###Disabling the `TEST_DELIVERY_ADDRESS` in SwiftMailer

SwiftMailer has another handy option to set a test delivery address: this way anytime you send an email from your application running in a development or test environment, the email is not sent to the real recipient but, instead, it is sent to a test email address.

To test the `SHQAwsSesMonitorBundle` you need to disable the sending of emails to the test mailbox or to whitelist the AWS SES mailboxes.

Otherwise, the emails will not be delivered to AWS SES test mailboxes.

To disable the sending of emails to the test mailbox in `dev` environment, open the file `config/packages/dev/swiftmailer.yaml`.

There there is a config node called `delivery_addresses`: if it is set, then the best approach is to whitelist the AWS SES mailboxes: this way, you will not forget to edit again the configuration once finished to test the bundle.

[To whitelist the AWS SES mailboxes](https://symfony.com/doc/current/email/dev_environment.html#sending-to-a-specified-address-but-with-exceptions), add the node `delivery_whitelist`:

```yaml
# config/packages/dev/swiftmailer.yaml
swiftmailer:
    delivery_addresses: ['dev@example.com']
    delivery_whitelist:
       # all email addresses matching these regexes will be delivered
       # like normal, as well as being sent to dev@example.com
       # simulator.amonses.com is the domain of test mailboxes
       # and we whitelist it
       - '/@simulator\.amazonses\.com$/'
```

Step 12: Sending the test email to the test mailboxes of AWS SES
----------------------------------------------------------------

To send the test emails, simply run the command `aws:ses:monitor:test:swiftmailer` and provide the verified test Identity `test_aws@example.com` when required:

![](http://www.serendipityhq.com/assets/open-source-projects/bundle-aws-ses-monitor/aws-ses-monitor-test-swiftmailer.gif "Send test email to test AWS SES mailboxes")

Once the command finishes, if you check Ngrok again, you will see the notifications sent by AWS SNS informing of the current status of the recipient emails:

![](http://www.serendipityhq.com/assets/open-source-projects/bundle-aws-ses-monitor/ngrok-aws-sns-aws-ses-monitor-test-swiftmailer-min.png "Send test email to test AWS SES mailboxes")

If you check your database, the notifications are also in the database and will be used by the Swiftmailer plugin to filter out recipients that are bounced or complained.

*NOTE: To send emails through AWS SES you need to use a verified Identity.
Technically, the command `aws:ses:monitor:test:swiftmailer` accepts any verified Identity.
But currently, if you have followed this documentation until now, you have only one identity configured: `test_aws`.
If this is the case, then you need to use this `test_aws@example.com` Identity to send the test email.
If you use an Identity not configured, an `\InvalidArgumentException` is thrown.*

Now that you are sure your system works, it's time to move it in production!

*Do you like this bundle? [**Leave a &#9733;**](#js-repo-pjax-container) or run `composer global require symfony/thanks && composer thanks` to say thank you to all libraries you use in your current project, this one too!*

([Go back to index](Index.md)) | Next step: [Integrate on `prod`](Integration-prod.md)
