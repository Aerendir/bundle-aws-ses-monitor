*Do you like this bundle? [**Leave a &#9733;**](#js-repo-pjax-container) or run `composer global require symfony/thanks && composer thanks` to say thank you to all libraries you use in your current project, this included!*

How to use the information collected
====================================

`SHQAwsSesMonitorBundle` collects a lot of inrmation about emails, specifically about their status.

The main entity you should use is the `EmailStatus`: this is the entry point to get all the information about a particular email.

The methods available in the `EmailStatus` entity are those:

- `EmailStatus::getEmail()`: Returns the email address represented by the object;
- Information about **Bounces**:
  - `EmailStatus::getBounces()`: Returns all the `Bounce` entities related to the email address
  - `EmailStatus::getHardBouncesCount()`: Returns the amount of hard bounces
  - `EmailStatus::getSoftBouncesCount()`: Returns the amount of soft bounces
  - `EmailStatus::getLastBounceType()`: Returns the type of the last bounce (One between `Bounce::TYPE_PERMANENT` and `Bounce::TYPE_TRANSIENT`)
  - `EmailStatus::getLastTimeBounced()`: Returns a `\DateTime` object of the last time the email bounced one of your messages
- Information about **Complaints**:
  - `EmailStatus::getComplaints()`: Returns the `Complaint` entities related to the email address
  - `EmailStatus::getLastTimeComplained()`: Returns a `\DateTime` object of the last time the email complained about one of your messages
- Information about **Deliveries** (available only if activated in the configuration):
  - `EmailStatus::getDeliveries()`: Returns the `Complaint` entities related to the email address
  - `EmailStatus::getLastTimeDelivered()`: Returns a `\DateTime` object of the last time the email correctly received one of your messages

***NOTE 1**: The `EmailStatus::getHardBouncesCount()` and `EmailStatus::getSoftBouncesCount()` methods don't trigger a new query to the database nor perform any complex task: the values returned are hard set when a Bounce is added. This way there is no overhed in calling those methods, also when there are a lot of Bounces.*

***NOTE 2**: The `EmailStatus` entity exposes other three methods to add Bounces, Complaints and Deliveries but they are meant only for `@internal` use and you should never use them!*

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

([Go back to index](Index.md))
