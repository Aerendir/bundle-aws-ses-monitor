How to integrate AWS SES with your application
==============================================

Step 5: Setup bounces and complaints handling
---------------------------------------------

Now it's time to create our topics for bounces and complaints. As told in the [post](http://sesblog.amazon.com/post/TxJE1JNZ6T9JXK/-Handling-span-class-matches-Bounces-span-and-Complaints.pdf)
Handling Bounces and Complaints on the [Amazon SES Blog](http://sesblog.amazon.com/), topics should follow the following nomenclature:

    ses-bounces-topic

Something like `ses-your_app-bounces-topic` may be better to avoid conflicts with other apps of yours.

So, run in console:
```
app/console awssesmonitor:sns:setup-bounces-topic ses-your_app-bounces-topic
```

and then

```
app/console awssesmonitor:sns:setup-complaints-topic ses-your_app-complaints-topic
```

This will use your AWS Credentials to fetch available identities and will provide you the option to choose what identities to subscribe to.

What will happen:

1. `ses-your_app-bounces-topic` topic will be created
2. All chosen identities will be configured to send Bounce notifications to that topic
3. Your project url will be provided as HTTP or HTTPS (configuration) endpoint for AWS
4. Automatic subscription confirmation will occur on AWS request to confirm (if your endpoint is reachable)

([Go back to index](Index.md)) | Next step: [Test](Test.md)
