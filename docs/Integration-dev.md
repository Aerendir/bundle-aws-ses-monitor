*Do you like this bundle? [**Leave a &#9733;**](#js-repo-pjax-container) or run `composer global require symfony/thanks && composer thanks` to say thank you to all libraries you use in your current project, this included!*

How to integrate AWS SES Monitor Bundle with AWS SES and AWS SNS (on `dev` environment)
=======================================================================================

Now that we have configured the `SHQAwsSesMonitorBundle` we need to integrate the three parts of the system:

- `SHQAwsSesMonitorBundle` with AWS SES;
- `SHQAwsSesMonitorBundle` with AWS SNS;
- AWS SES with AWS SNS;

`SHQAwsSesMonitorBundle` automates a lot of the manual work. Anyway you have to do some things that the bundle cannot technically manage on its side.

But stay calm as `SHQAwsSesMonitorBundle` will tell you exactly what you have to do!

Let's start to integrate all the parts using our development machine (`dev` environment).

Step 8: Install Ngrok
---------------------

To test locally the integration between your app and AWS SES and AWS SNS, [you need to use a tunneling app](https://blogs.aws.amazon.com/php/post/Tx2CO24DVG9CAK0/Testing-Webhooks-Locally-for-Amazon-SNS).

A tunneling app basically

> expose the locally running PHP server to the public internet. Ngrok does this by creating a tunnel to a specified port on your local machine.

[Ngrok](https://ngrok.com/) is really simple to use: [once installed](https://ngrok.com/download), you have to do two simple things:

1. Start the local PHP web server;
2. Start Ngrok pointing it to the local port on which the local PHP web server is listening.

![](http://www.serendipityhq.com/assets/open-source-projects/bundle-aws-ses-monitor/ngrok-start.gif "Starting Ngrok")

In console commands, this is like this:

```console
$ bin/console server:start

[OK] Server listening on http://127.0.0.1:8000

```

The server started and is listening on the port `8000`: use it to start Ngrok:

````console
$ grok http 8000
````

*NOTE: Here we use `http` as schema as this refers to our local development machine that hasn't a digital certificate that makes it secure.*

![](http://www.serendipityhq.com/assets/open-source-projects/bundle-aws-ses-monitor/ngrok-started-min.png "Ngrok started")

As you can see, Ngrok gives you two URLs: one secure and another one insecure, but they are basically the same.

Open the file `.env` and edit the variables `NGROK_APP_SCHEME` and `NGROK_APP_HOST` using the secure URL that Ngrok gave you:

```apacheconfig
# .env

###> serendipity_hq/aws-ses-monitor-bundle ###
NGROK_APP_SCHEME=https
NGROK_APP_HOST=b2dc2f69.ngrok.io
...
###< serendipity_hq/aws-ses-monitor-bundle ###
```

Before we can start configuring AWS SES, we need to do one last thing: configure our domain.

Step 9: Configure our domain for the email `test_aws`
-----------------------------------------------------

On development environment, the `SHQAwsSesMonitorBundle` will configure an Identity for the email `test_aws@example.com`.

To this email address, Amazon will send a confirmation email with a link you have to click to confirm your ownership of the email.

So, your domain has to be configured accordingly.

For this you have two options (deending on your domain provider):

1. Set a catch-all email address: to this email address are delivered all the emails sent to any non existent email address;
2. Explicitly create an email address named `test_aws@example.com`.

We prefer the first solution as it will not force us to deal with a new email account and also because it is always a good thing to have a catch all email address: the mistypings are frequently when someone has to send us emails!

So, go to configure your catch all email address, then come back here to continue the configuration of AWS SES.

Done?

Well, we are now ready for the next step: configuring AWS SES and AWS SNS using the built-in command `aws:ses:configure`.

Step 10: Run the command `aws:ses:configure` for the first time
--------------------------------------------------------------

All starts with this simple command: `aws:ses:configure`.

Before executing it, open a window in your browser and go the URL `http://localhost:4040`: here Ngrok will show you all the incoming requests.

![](http://www.serendipityhq.com/assets/open-source-projects/bundle-aws-ses-monitor/ngrok-started-web-interface-min.png "Ngrok's web interface")

On this page you will see all the calls to the endpoint registere in the AWS SNS topic.

Now, run the command:

```console
$ bin/console aws:ses:configure
```

![](http://www.serendipityhq.com/assets/open-source-projects/bundle-aws-ses-monitor/aws-ses-configure-dev-first-run.gif "First run of aws:ses:configure on dev")

As you can see the command gives you a lot of information: let's understand them!

#### What the command `aws:ses:configure` did

When you run the command `aws:ses:configure` the bundle does the following things:

1. Creates the Identity on AWS SES;
2. Creates the Topics on AWS SNS;
3. Creates the Subscriptions on AWS SNS.

And in fact, if you go to your AWS dashbard, you will see this:

1. In AWS SES dashboard, the email Identity `test_aws@coommercio.com`, in a `pending verification` status;
![](http://www.serendipityhq.com/assets/open-source-projects/bundle-aws-ses-monitor/aws-ses-email-identities-pending-min.png "The pending verification email Identity on AWS SES")
2. In AWS SNS dashboard, in the Topics section, the Topics for each kind of notification the SES Identity may send (bounces, complaints and deliveries)
![](http://www.serendipityhq.com/assets/open-source-projects/bundle-aws-ses-monitor/aws-sns-topics-min.png "The topics created for the AWS SES email Identity")
3. In AWS SNS dashboard, in the Subscriptions section, the Subscriptions to the Topics

If you see the Ngrok web interface, you will see also that Ngrok received some requests from AWS SNS:

![](http://www.serendipityhq.com/assets/open-source-projects/bundle-aws-ses-monitor/ngrok-aws-sns-confirmation-payloads-min.png "First run of aws:ses:configure on dev")

Those requests are from AWS SNS that tries to confirm the created Subscriptions: they was all successful.

Now, come back to the command, and read what it tells us:

![](http://www.serendipityhq.com/assets/open-source-projects/bundle-aws-ses-monitor/aws-ses-configure-dev-first-run-min.png "First run of aws:ses:configure on dev")

It tells us two important things:

1. That we need to confirm the email Identity;
2. That it skipped the configuration of the Identity `coommercio.com` (that is our production domain Identity).

The command is safe: it understands the current environment, and it is not in production, it skips all production Identities. Equally, when in production, it skips all development identities.

So you can always run it securely, without fearing you'll break something of your identities running it.

This is a security measure we implemented as we happened that running the command in development we configured our production identities to send notifications to our development machines, breaking our system: this will never happen again now due to this check on the environment in which the command is run.

Coming back to AWS SES, when you asked AWS SES to create the email Identity `test_aws@emample.com`, AWS SES sent a confirmation email to the email address:

![](http://www.serendipityhq.com/assets/open-source-projects/bundle-aws-ses-monitor/aws-ses-confirmation-email-min.png "AWS SES Identity confirmation email")

Click on the link in the email to confirm the Identity.

Now the email Identity is verified and can be used to send emails:

![](http://www.serendipityhq.com/assets/open-source-projects/bundle-aws-ses-monitor/aws-ses-email-identities-verified-min.png "The verified email Identity on AWS SES")

Now it's time to test the bundle on our development machine.

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

([Go back to index](Index.md)) | Next step: [Test (`dev` environment)](Test-dev.md)
