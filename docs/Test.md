*Do you like this bundle? [**Leave a &#9733;**](#js-repo-pjax-container) or run `composer global require symfony/thanks && composer thanks` to say thank you to all libraries you use in your current project, this one too!*

How to test AWS SES e-mails sending
===================================

To test the working of this mechanism on your local machine you have to use a tunneling app.
Follow the instructions [here](https://blogs.aws.amazon.com/php/post/Tx2CO24DVG9CAK0/Testing-Webhooks-Locally-for-Amazon-SNS) to setup one and test the bundle.

1. Start the Symfony App's Server (`app/console server:run`)
2. Start the tunnel on the same port using Ngrok (`ngrok http 8000`)
3. Edit the config_dev.yml file to set the new host for topics get from the previous command
4. Comment the `if` in web/app_dev.php taht starts with `if (isset($_SERVER['HTTP_CLIENT_IP'])`
5. Execute the command to create the topics and configure the database
6. Go to Amazon AWS SNS console, select the section "Topics" and click on the created topic
7. Open a new browser window and go to the URL `http://localhost:4040/inspect/http` (this URL is used by Ngrok to show you the traffic inspections)
8. In the AWS SNS Console, click on the menu item "Request confirmation"
9. Observe the Ngrok browser window and see what happens :)
10. Come back to the Amazon SNS console and see the topic status: is it confirmed?

Test the email sending and the integration with the AWS SNS service
-------------------------------------------------------------------

The reference document is [Testing Amazon SES Email Sending](http://docs.aws.amazon.com/ses/latest/DeveloperGuide/mailbox-simulator.html).

Before start, be sure you have enabled the sending of emails trough SwiftMailer:

```yaml
swiftmailer:
    disable_delivery: false
    #delivery_address: hello@aerendir.me
```

*Do you like this bundle? [**Leave a &#9733;**](#js-repo-pjax-container) or run `composer global require symfony/thanks && composer thanks` to say thank you to all libraries you use in your current project, this one too!*

([Go back to index](Index.md))
