*Do you like this bundle? [**Leave a &#9733;**](#js-repo-pjax-container) or run `composer global require symfony/thanks && composer thanks` to say thank you to all libraries you use in your current project, this included!*

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

The command is `aws:ses:monitor:test:mailer` (`aws:ses:monitor:test:swiftmailer` still works as an alias).

It sends the emails synchronously through the default transport of Symfony Mailer, also when your application sends its emails asynchronously through Symfony Messenger, and tells you which ones were not sent: those rejected because all their recipients are bounced or complained, and those the transport failed to send.

But before you can use it from your development machine, you have to be sure Symfony Mailer is really sending emails in your `dev` environment.

Step 11: Ensuring Symfony Mailer `dev` configuration is correct
---------------------------------------------------------------

Before you can send emails to the test mailboxes of AWS SES, you need to be sure that nothing is configured in `dev` in a way that can prevent the emails from being sent or delivered.

Below are the instructions to correctly configure Symfony Mailer in `dev` environments.

### Enabling delivery in Symfony Mailer in a `dev` environment, too

Symfony Mailer can [disable the real sending of emails](https://symfony.com/doc/current/mailer.html#disabling-delivery): this is useful in `dev` environments to avoid sending emails to real recipients when developing the application.

To test the `SHQAwsSesMonitorBundle` you need to enable the sending of emails for real also in `dev` environment.

Otherwise, the emails will not be delivered to AWS SES test mailboxes.

Be sure the `MAILER_DSN` used in `dev` is not `null://null` and that no `framework.mailer.dsn` set to `null://null` exists in `config/packages/dev/` (or in a `when@dev` block):

```yaml
# config/packages/mailer.yaml
when@dev:
    framework:
        mailer:
            dsn: '%env(MAILER_DSN)%'
```

### Disabling the forced recipients in Symfony Mailer

Symfony Mailer can [always send to the same address](https://symfony.com/doc/current/mailer.html#always-send-to-the-same-address): this way anytime you send an email from your application running in a development or test environment, the email is not sent to the real recipient but, instead, it is sent to a test email address.

To test the `SHQAwsSesMonitorBundle` you need to disable the sending of emails to the test mailbox or to allow the AWS SES mailboxes.

Otherwise, the emails will not be delivered to AWS SES test mailboxes.

If the `envelope.recipients` node is set in `dev`, the best approach is to allow the AWS SES mailboxes: this way, you will not forget to edit again the configuration once finished to test the bundle.

To allow the AWS SES mailboxes (Symfony 7.1 and later), add the node `envelope.allowed_recipients`:

```yaml
# config/packages/mailer.yaml
when@dev:
    framework:
        mailer:
            envelope:
                recipients: ['dev@example.com']
                allowed_recipients:
                    # all email addresses matching these regexes will be delivered
                    # like normal, as well as being sent to dev@example.com
                    # simulator.amazonses.com is the domain of test mailboxes
                    # and we allow it
                    - '.*@simulator\.amazonses\.com'
```

On Symfony 6.4, which has no `allowed_recipients`, remove the `envelope.recipients` node while you test the bundle.

Step 12: Sending the test email to the test mailboxes of AWS SES
----------------------------------------------------------------

To send the test emails, simply run the command `aws:ses:monitor:test:mailer` and provide the verified test Identity `test_aws@example.com` when required:

![](http://www.serendipityhq.com/assets/open-source-projects/bundle-aws-ses-monitor/aws-ses-monitor-test-swiftmailer.gif "Send test email to test AWS SES mailboxes")

Once the command finishes, if you check Ngrok again, you will see the notifications sent by AWS SNS informing of the current status of the recipient emails:

![](http://www.serendipityhq.com/assets/open-source-projects/bundle-aws-ses-monitor/ngrok-aws-sns-aws-ses-monitor-test-swiftmailer-min.png "Send test email to test AWS SES mailboxes")

If you check your database, the notifications are also in the database and will be used by the Symfony Mailer filter to remove the recipients that are bounced or complained.

*NOTE: To send emails through AWS SES you need to use a verified Identity.
Technically, the command `aws:ses:monitor:test:mailer` accepts any verified Identity.
But currently, if you have followed this documentation until now, you have only one identity configured: `test_aws`.
If this is the case, then you need to use this `test_aws@example.com` Identity to send the test email.
If you use an Identity not configured, an `\InvalidArgumentException` is thrown.*

Now that you are sure your system works, it's time to move it in production!

<hr />
<h3 align="center">
    <b>Do you like this bundle?</b><br />
    <b><a href="#js-repo-pjax-container">LEAVE A &#9733;</a></b>
</h3>
<p align="center">
    or run<br />
    <code>composer global require symfony/thanks && composer thanks</code><br />
    to say thank you to all libraries you use in your current project, this included!
</p>
<hr />

([Go back to index](Index.md)) | Next step: [Integrate on `prod`](Integration-prod.md)
