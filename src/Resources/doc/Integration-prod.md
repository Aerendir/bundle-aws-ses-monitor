*Do you like this bundle? [**Leave a &#9733;**](#js-repo-pjax-container) or run `composer global require symfony/thanks && composer thanks` to say thank you to all libraries you use in your current project, this one too!*

How to integrate AWS SES Monitor Bundle with AWS SES and AWS SNS (on `prod` environment)
========================================================================================

Now that you have configured, integrated and tested `SHQAwsSesMonitorBundle` on your local development, it is time to test it on production.

In production we are going to configure our domain Identity, so it is possible to send emails from any email address that belong to the domain itself.

The procedure to verify is a bit more complex than the one tha has to be followed to verify email identities: while for email identities it is sufficient to click on the confirmation email Amazon sends us, in fact, for domain identities it we are required to add some records to our DNS.

Those record will then be read by Amazon that will then confirm our ownership of the domain.

You can find more information about domain identities verification in the page [Verifying Domains in Amazon SES](https://docs.aws.amazon.com/ses/latest/DeveloperGuide/verify-domain-procedure.html).

The first step is to commit to `git` all files you created or modified.

Step 13: Committing changes to `git`
------------------------------------

Git will tell you exactly what you changed, but just in case, here there is a complete list of the files you should commit:

- `config/packages/dev/shq_aws_ses_monitor.yaml`
- `config/packages/dev/swiftmailer.yaml`
- `config/packages/aws.yaml`
- `config/packages/shq_aws_ses_monitor.yaml`
- `config/routes/shq_aws_ses_monitor.yaml`
- `config/bundles.php`
- `config/services.yaml`
- `.env.dist`
- `composer.json`
- `composer.lock` (Yes, your application should have this committed!)
- `phpunit.xml.dist`
- `symfony.lock` (If you use Symfony Flex, your application should have this committed!)

**Commit all changes, but not push to your remote repository now!** Instead, first set the environment variables on your `prod` enviornment.

Step 14: Setting environment variables on production server
-----------------------------------------------------------

The way you are going to set the environment variables on your server is up to your server specifics.

With some services this is possible using the provided control panel; in other you have to deal with the server directly; if you use Docker, then you need to configure them in the `docker-compose.yml` or the `Dockerfile` files: you need to understand this before you can continue.

Assuming you know how to configure environment variables on you production machines, here is the list of the environment variables you need to add:

- `AWS_KEY`
- `AWS_SECRET`
- `AWS_SES_TOPIC_BOUNCES`
- `AWS_SES_TOPIC_COMPLAINTS`
- `AWS_SES_TOPIC_DELIVERIES`
- `MAILER_URL` (if you have just installed `SwiftMailerBundle`)

You don't need to set `NGROK_APP_SCHEME` nor `NGROK_APP_HOST` as they are meant to be used only in `dev` environment.

Step 15: Push
-------------

Now you can push to your remote repository and deploy to your production servers.

Step 16: Update the database schema
-----------------------------------

As we did on our dev machine, we need to update the database schema.

Run this Doctrine's command:

```console
$ bin/console doctrine:schema:update --force
```

No need for a migration as we are going to create new tables related to `SHQAwsSesMonitor`.

ATTENTION: If you modified any other Doctrine entity adding, removing or editing fields, this command will apply these modifications, too. In this case it is better to use migrations!

Step 17: Run the command `aws:ses:configure` for the first time
---------------------------------------------------------------

Connect to your production machine and run the command `aws:ses:configure`.

![](http://www.serendipityhq.com/assets/open-source-projects/bundle-aws-ses-monitor/aws-ses-configure-prod-first-run.gif "First run of aws:ses:configure on prod")

This time, the output will be different than the one we saw on our `dev` machine:

![](http://www.serendipityhq.com/assets/open-source-projects/bundle-aws-ses-monitor/aws-ses-configure-prod-first-run-min.png "First run of aws:ses:configure on prod")

As you can see, the command gives you the DNS record you need to add to your domain to verify its ownership.

You can find this same DNS record in your AWS SES console.

In the mean time, the domain identity is in a "Pending verification" state.

![](http://www.serendipityhq.com/assets/open-source-projects/bundle-aws-ses-monitor/aws-ses-domain-identities-pending-min.png "First run of aws:ses:configure on prod")

Step 18: Add the `TXT` DNS record to your domain
------------------------------------------------

A TXT record is a type of DNS record that provides additional information about your domain.

How to add it to your domain is something we cannot cover here.

In general, your DNS provider (often the domain maintainer) should provide you with a control panel where you can change the DNS records on your own.

Search on Google for information about this operation with your maintainer or contact their support for further information.

Maybe reading first what Amazon tells about this operation can help you: [Amazon SES Domain Verification TXT Records](https://docs.aws.amazon.com/ses/latest/DeveloperGuide/dns-txt-records.html?icmpid=docs_ses_console).

Step 19: Wait... wait... wait...
--------------------------------

Once you added the `TXT` record to your DNS you have to simply wait: the time for your new record to propagate to all DNS servers may take up to 72 hours: once the DNS are propagated and Amazon has verified your ownership of the domain, you can go to the next step.

So, in the mean time... Wait. Patiently! :)

*Do you like this bundle? [**Leave a &#9733;**](#js-repo-pjax-container) or run `composer global require symfony/thanks && composer thanks` to say thank you to all libraries you use in your current project, this one too!*

([Go back to index](Index.md)) | Next step: [Using the information collected](Using.md)
