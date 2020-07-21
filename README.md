<p align="center">
    <a href="http://www.serendipityhq.com" target="_blank">
        <img style="max-width: 350px" src="http://www.serendipityhq.com/assets/open-source-projects/Logo-SerendipityHQ-Icon-Text-Purple.png">
    </a>
</p>

<h1 align="center">Serendipity HQ AWS SES Monitor Bundle</h1>
<p align="center">AWS SES Monitor Bundle automates the filtering of <a href="http://docs.aws.amazon.com/ses/latest/DeveloperGuide/best-practices-bounces-complaints.html">bounced and complained e-mails sent through AWS SES</a>.</p>
<p align="center">
    <a href="https://github.com/Aerendir/bundle-aws-ses-monitor/releases"><img src="https://img.shields.io/packagist/v/serendipity_hq/bundle-aws-ses-monitor.svg?style=flat-square"></a>
    <a href="https://opensource.org/licenses/MIT"><img src="https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square"></a>
    <a href="https://github.com/Aerendir/bundle-aws-ses-monitor/releases"><img src="https://img.shields.io/packagist/php-v/serendipity_hq/bundle-aws-ses-monitor?color=%238892BF&style=flat-square&logo=php" /></a>
    <a title="Tested with Symfony ^3.0" href="https://github.com/Aerendir/bundle-aws-ses-monitor/actions?query=branch%3Adev"><img title="Tested with Symfony ^3.0" src="https://img.shields.io/badge/Symfony-%5E3.0-333?style=flat-square&logo=symfony" /></a>
    <a title="Tested with Symfony ^4.0" href="https://github.com/Aerendir/bundle-aws-ses-monitor/actions?query=branch%3Adev"><img title="Tested with Symfony ^4.0" src="https://img.shields.io/badge/Symfony-%5E4.0-333?style=flat-square&logo=symfony" /></a>
    <a title="Tested with Symfony ^5.0" href="https://github.com/Aerendir/bundle-aws-ses-monitor/actions?query=branch%3Adev"><img title="Tested with Symfony ^5.0" src="https://img.shields.io/badge/Symfony-%5E5.0-333?style=flat-square&logo=symfony" /></a>
</p>

## Current Status

[![Coverage](https://sonarcloud.io/api/project_badges/measure?project=Aerendir_bundle-aws-ses-monitor&metric=coverage)](https://sonarcloud.io/dashboard?id=Aerendir_bundle-aws-ses-monitor)
[![Maintainability Rating](https://sonarcloud.io/api/project_badges/measure?project=Aerendir_bundle-aws-ses-monitor&metric=sqale_rating)](https://sonarcloud.io/dashboard?id=Aerendir_bundle-aws-ses-monitor)
[![Quality Gate Status](https://sonarcloud.io/api/project_badges/measure?project=Aerendir_bundle-aws-ses-monitor&metric=alert_status)](https://sonarcloud.io/dashboard?id=Aerendir_bundle-aws-ses-monitor)
[![Reliability Rating](https://sonarcloud.io/api/project_badges/measure?project=Aerendir_bundle-aws-ses-monitor&metric=reliability_rating)](https://sonarcloud.io/dashboard?id=Aerendir_bundle-aws-ses-monitor)
[![Security Rating](https://sonarcloud.io/api/project_badges/measure?project=Aerendir_bundle-aws-ses-monitor&metric=security_rating)](https://sonarcloud.io/dashboard?id=Aerendir_bundle-aws-ses-monitor)
[![Technical Debt](https://sonarcloud.io/api/project_badges/measure?project=Aerendir_bundle-aws-ses-monitor&metric=sqale_index)](https://sonarcloud.io/dashboard?id=Aerendir_bundle-aws-ses-monitor)
[![Vulnerabilities](https://sonarcloud.io/api/project_badges/measure?project=Aerendir_bundle-aws-ses-monitor&metric=vulnerabilities)](https://sonarcloud.io/dashboard?id=Aerendir_bundle-aws-ses-monitor)

[![Phan](https://github.com/Aerendir/bundle-aws-ses-monitor/workflows/Phan/badge.svg)](https://github.com/Aerendir/bundle-aws-ses-monitor/actions?query=branch%3Adev)
[![PHPStan](https://github.com/Aerendir/bundle-aws-ses-monitor/workflows/PHPStan/badge.svg)](https://github.com/Aerendir/bundle-aws-ses-monitor/actions?query=branch%3Adev)
[![PSalm](https://github.com/Aerendir/bundle-aws-ses-monitor/workflows/PSalm/badge.svg)](https://github.com/Aerendir/bundle-aws-ses-monitor/actions?query=branch%3Adev)
[![PHPUnit](https://github.com/Aerendir/bundle-aws-ses-monitor/workflows/PHPunit/badge.svg)](https://github.com/Aerendir/bundle-aws-ses-monitor/actions?query=branch%3Adev)
[![Composer](https://github.com/Aerendir/bundle-aws-ses-monitor/workflows/Composer/badge.svg)](https://github.com/Aerendir/bundle-aws-ses-monitor/actions?query=branch%3Adev)
[![PHP CS Fixer](https://github.com/Aerendir/bundle-aws-ses-monitor/workflows/PHP%20CS%20Fixer/badge.svg)](https://github.com/Aerendir/bundle-aws-ses-monitor/actions?query=branch%3Adev)
[![Rector](https://github.com/Aerendir/bundle-aws-ses-monitor/workflows/Rector/badge.svg)](https://github.com/Aerendir/bundle-aws-ses-monitor/actions?query=branch%3Adev)

## Features

AWS SES can give you notifications about bounced or complained e-mails both [via e-mail](http://docs.aws.amazon.com/ses/latest/DeveloperGuide/notifications-via-email.html)
 or [via the AWS SNS service](http://docs.aws.amazon.com/ses/latest/DeveloperGuide/notifications-via-sns.html).

Managing these notifications it is possible to know to which e-mails we should not send further e-mails.

**This bundle helps you setting up the automatic handling of notifications via SNS.**

Do you like this bundle? [**Leave a &#9733;**](#js-repo-pjax-container) or run `composer global require symfony/thanks && composer thanks` to say thank you to all libraries you use in your current project, this one too! !

### How AWS SES Monitor Bundle integrates with AWS SNS

Using AWS SES Monitor Bundle you can, using the console of your Symfony's App, create in AWS Simple Notifications Service a topic for bounced emails and one for complained emails and automatically subscribe your app identity to that topics.

The bundle exposes some endpoints called by AWS Simple Email Service when an e-mail is bounced or complained. When those endpoints are called, AWS SES Monitor Bundle persists these emails in the database.

The SwiftMailer plugin included reads these e-mails and automatically disable the sending of e-mails to those addresses.

The entire procedure is automated by the bundle in a really simple and easy way.

## Documentation

You can read how to install, configure, test and use AWS SES Monitor Bundle in the [documentation](src/Resources/doc/Index.md).
