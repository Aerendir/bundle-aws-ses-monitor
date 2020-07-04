<p align="center">
    <a href="http://www.serendipityhq.com" target="_blank">
        <img src="http://www.serendipityhq.com/assets/open-source-projects/Logo-SerendipityHQ-Icon-Text-Purple.png">
    </a>
</p>

SHQ AWS SES MONITOR BUNDLE
==========================

[![PHP from Packagist](https://img.shields.io/packagist/php-v/serendipity_hq/aws-ses-monitor-bundle?color=%238892BF)](https://packagist.org/packages/serendipity_hq/aws-ses-monitor-bundle)
[![Latest Stable Version](https://poser.pugx.org/serendipity_hq/aws-ses-monitor-bundle/v/stable.png)](https://packagist.org/packages/serendipity_hq/aws-ses-monitor-bundle)
[![Total Downloads](https://poser.pugx.org/serendipity_hq/aws-ses-monitor-bundle/downloads.svg)](https://packagist.org/packages/serendipity_hq/aws-ses-monitor-bundle)
[![License](https://poser.pugx.org/serendipity_hq/aws-ses-monitor-bundle/license.svg)](https://packagist.org/packages/serendipity_hq/aws-ses-monitor-bundle)

[![Coverage](https://sonarcloud.io/api/project_badges/measure?project=Aerendir_aws-ses-monitor-bundle&metric=coverage)](https://sonarcloud.io/dashboard?id=Aerendir_aws-ses-monitor-bundle)
[![Maintainability Rating](https://sonarcloud.io/api/project_badges/measure?project=Aerendir_aws-ses-monitor-bundle&metric=sqale_rating)](https://sonarcloud.io/dashboard?id=Aerendir_aws-ses-monitor-bundle)
[![Quality Gate Status](https://sonarcloud.io/api/project_badges/measure?project=Aerendir_aws-ses-monitor-bundle&metric=alert_status)](https://sonarcloud.io/dashboard?id=Aerendir_aws-ses-monitor-bundle)
[![Reliability Rating](https://sonarcloud.io/api/project_badges/measure?project=Aerendir_aws-ses-monitor-bundle&metric=reliability_rating)](https://sonarcloud.io/dashboard?id=Aerendir_aws-ses-monitor-bundle)
[![Security Rating](https://sonarcloud.io/api/project_badges/measure?project=Aerendir_aws-ses-monitor-bundle&metric=security_rating)](https://sonarcloud.io/dashboard?id=Aerendir_aws-ses-monitor-bundle)
[![Technical Debt](https://sonarcloud.io/api/project_badges/measure?project=Aerendir_aws-ses-monitor-bundle&metric=sqale_index)](https://sonarcloud.io/dashboard?id=Aerendir_aws-ses-monitor-bundle)
[![Vulnerabilities](https://sonarcloud.io/api/project_badges/measure?project=Aerendir_aws-ses-monitor-bundle&metric=vulnerabilities)](https://sonarcloud.io/dashboard?id=Aerendir_aws-ses-monitor-bundle)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/4c45c317-28c4-40ef-9a1b-01af44b77327/mini.png)](https://insight.sensiolabs.com/projects/4c45c317-28c4-40ef-9a1b-01af44b77327)

![Phan](https://github.com/Aerendir/aws-ses-monitor-bundle/workflows/Phan/badge.svg)
![PHPStan](https://github.com/Aerendir/aws-ses-monitor-bundle/workflows/PHPStan/badge.svg)
![PSalm](https://github.com/Aerendir/aws-ses-monitor-bundle/workflows/PSalm/badge.svg)
![PHPUnit](https://github.com/Aerendir/aws-ses-monitor-bundle/workflows/PHPunit/badge.svg)
![Composer](https://github.com/Aerendir/aws-ses-monitor-bundle/workflows/Composer/badge.svg)
![PHP CS Fixer](https://github.com/Aerendir/aws-ses-monitor-bundle/workflows/PHP%20CS%20Fixer/badge.svg)
![Rector](https://github.com/Aerendir/aws-ses-monitor-bundle/workflows/Rector/badge.svg)

AWS SES Monitor Bundle automates the filtering of [bounced and complained e-mails sent through AWS SES](http://docs.aws.amazon.com/ses/latest/DeveloperGuide/best-practices-bounces-complaints.html).

AWS SES can give you notifications about bounced or complained e-mails both [via e-mail](http://docs.aws.amazon.com/ses/latest/DeveloperGuide/notifications-via-email.html)
 or [via the AWS SNS service](http://docs.aws.amazon.com/ses/latest/DeveloperGuide/notifications-via-sns.html).

Managing these notifications it is possible to know to which e-mails we should not send further e-mails.

**This bundle helps you setting up the automatic handling of notifications via SNS.**

Do you like this bundle? [**Leave a &#9733;**](#js-repo-pjax-container) or run `composer global require symfony/thanks && composer thanks` to say thank you to all libraries you use in your current project, this one too! !

How AWS SES Monitor Bundle integrates with AWS SNS
--------------------------------------------------

Using AWS SES Monitor Bundle you can, using the console of your Symfony's App, create in AWS Simple Notifications Service a topic for bounced emails and one for complained emails and automatically subscribe your app identity to that topics.

The bundle exposes some endpoints called by AWS Simple Email Service when an e-mail is bounced or complained. When those endpoints are called, AWS SES Monitor Bundle persists these emails in the database.

The SwiftMailer plugin included reads these e-mails and automatically disable the sending of e-mails to those addresses.

The entire procedure is automated by the bundle in a really simple and easy way.

Requirements
------------

1. PHP ^7.1
2. Symfony ^3.4|^4.1

DOCUMENTATION
=============

You can read how to install, configure, test and use AWS SES Monitor Bundle in the [documentation](src/Resources/doc/Index.md).
