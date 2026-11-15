<?php
/**
 * This is an automatically generated baseline for Phan issues.
 * When Phan is invoked with --load-baseline=path/to/baseline.php,
 * The pre-existing issues listed in this file won't be emitted.
 *
 * This file can be updated by invoking Phan with --save-baseline=path/to/baseline.php
 * (can be combined with --load-baseline)
 */
return [
    // # Issue statistics:
    // PhanAccessMethodInternal : 150+ occurrences
    // PhanRedefinedClassReference : 75+ occurrences
    // PhanUnreferencedPublicMethod : 40+ occurrences
    // PhanAccessClassConstantInternal : 15+ occurrences
    // PhanTypeArraySuspiciousNullable : 15+ occurrences
    // PhanUnreferencedPublicClassConstant : 15+ occurrences
    // PhanDeprecatedClassConstant : 8 occurrences
    // PhanUnreferencedClass : 7 occurrences
    // PhanReadOnlyPrivateProperty : 4 occurrences
    // PhanUndeclaredMethod : 4 occurrences
    // PhanRedefinedExtendedClass : 3 occurrences
    // PhanUnusedPublicNoOverrideMethodParameter : 3 occurrences
    // PhanUnreferencedClosure : 2 occurrences
    // ConstReferenceConstNotFound : 1 occurrence
    // PhanTypeMismatchDeclaredReturn : 1 occurrence
    // PhanUnusedVariableCaughtException : 1 occurrence

    'file_suppressions' => [
        'src/Command/ConfigureCommand.php' => [
            'ConstReferenceConstNotFound' => ['\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Command\\ConfigureCommand::configureTopics'],
            'PhanAccessMethodInternal' => ['\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Command\\ConfigureCommand::buildDkimDnsString', '\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Command\\ConfigureCommand::checkIdentityDkimConfiguration', '\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Command\\ConfigureCommand::checkIdentityFromDomain', '\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Command\\ConfigureCommand::checkIdentityNotificationTopic', '\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Command\\ConfigureCommand::checkIdentityVerificationStatus', '\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Command\\ConfigureCommand::configureIdentities', '\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Command\\ConfigureCommand::configureIdentity', '\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Command\\ConfigureCommand::configureSubscription', '\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Command\\ConfigureCommand::configureTopic', '\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Command\\ConfigureCommand::execute', '\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Command\\ConfigureCommand::getTopicName', '\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Command\\ConfigureCommand::initializeConfiguration', '\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Command\\ConfigureCommand::subscribeIdentityToTopic'],
            'PhanRedefinedClassReference' => ['\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Command\\ConfigureCommand', '\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Command\\ConfigureCommand::__construct', '\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Command\\ConfigureCommand::canProceed', '\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Command\\ConfigureCommand::configure', '\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Command\\ConfigureCommand::execute', '\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Command\\ConfigureCommand::initializeConfiguration', '\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Command\\ConfigureCommand::logActionsToTake', '\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Command\\ConfigureCommand::logSkippedIdentities'],
            'PhanRedefinedExtendedClass' => ['src/Command/ConfigureCommand.php'],
            'PhanTypeArraySuspiciousNullable' => ['\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Command\\ConfigureCommand::configureTopic'],
            'PhanUnreferencedClass' => ['src/Command/ConfigureCommand.php']
        ],
        'src/Command/DebugCommand.php' => [
            'PhanAccessClassConstantInternal' => ['\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Command\\DebugCommand::execute', '\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Command\\DebugCommand::showData'],
            'PhanAccessMethodInternal' => ['\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Command\\DebugCommand::execute', '\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Command\\DebugCommand::showData', '\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Command\\DebugCommand::validateAccountData', '\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Command\\DebugCommand::validateIdentitiesData'],
            'PhanRedefinedClassReference' => ['\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Command\\DebugCommand', '\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Command\\DebugCommand::__construct', '\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Command\\DebugCommand::configure', '\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Command\\DebugCommand::execute', '\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Command\\DebugCommand::showData'],
            'PhanRedefinedExtendedClass' => ['src/Command/DebugCommand.php'],
            'PhanUnreferencedClass' => ['src/Command/DebugCommand.php']
        ],
        'src/Command/SesSendTestEmailsCommand.php' => [
            'PhanRedefinedClassReference' => ['\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Command\\SesSendTestEmailsCommand::__construct', '\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Command\\SesSendTestEmailsCommand::execute'],
            'PhanRedefinedExtendedClass' => ['src/Command/SesSendTestEmailsCommand.php'],
            'PhanUnreferencedClass' => ['src/Command/SesSendTestEmailsCommand.php']
        ],
        'src/Controller/EndpointController.php' => [
            'PhanUnreferencedClass' => ['src/Controller/EndpointController.php'],
            'PhanUnreferencedPublicMethod' => ['\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Controller\\EndpointController::endpoint']
        ],
        'src/DependencyInjection/Configuration.php' => [
            'PhanAccessMethodInternal' => ['\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\DependencyInjection\\Configuration::getConfigTreeBuilder', '\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\DependencyInjection\\Configuration::validateTopic'],
            'PhanUndeclaredMethod' => ['\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\DependencyInjection\\Configuration::getConfigTreeBuilder'],
            'PhanUnreferencedClosure' => ['\\closure']
        ],
        'src/DependencyInjection/SHQAwsSesMonitorExtension.php' => [
            'PhanUnreferencedClass' => ['src/DependencyInjection/SHQAwsSesMonitorExtension.php']
        ],
        'src/Entity/Bounce.php' => [
            'PhanDeprecatedClassConstant' => ['\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Entity\\Bounce'],
            'PhanReadOnlyPrivateProperty' => ['\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Entity\\Bounce'],
            'PhanUnreferencedPublicClassConstant' => ['\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Entity\\Bounce'],
            'PhanUnreferencedPublicMethod' => ['\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Entity\\Bounce::getAction', '\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Entity\\Bounce::getDiagnosticCode', '\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Entity\\Bounce::getEmailStatus', '\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Entity\\Bounce::getId', '\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Entity\\Bounce::getMailMessage', '\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Entity\\Bounce::getReportingMta', '\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Entity\\Bounce::getStatus', '\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Entity\\Bounce::getSubType', '\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Entity\\Bounce::isPermanent']
        ],
        'src/Entity/Complaint.php' => [
            'PhanDeprecatedClassConstant' => ['\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Entity\\Complaint'],
            'PhanReadOnlyPrivateProperty' => ['\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Entity\\Complaint'],
            'PhanUnreferencedPublicClassConstant' => ['\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Entity\\Complaint'],
            'PhanUnreferencedPublicMethod' => ['\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Entity\\Complaint::getArrivalDate', '\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Entity\\Complaint::getComplaintFeedbackType', '\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Entity\\Complaint::getEmailStatus', '\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Entity\\Complaint::getId', '\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Entity\\Complaint::getMailMessage', '\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Entity\\Complaint::getUserAgent']
        ],
        'src/Entity/Delivery.php' => [
            'PhanDeprecatedClassConstant' => ['\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Entity\\Delivery'],
            'PhanReadOnlyPrivateProperty' => ['\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Entity\\Delivery'],
            'PhanUnreferencedPublicMethod' => ['\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Entity\\Delivery::getEmailStatus', '\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Entity\\Delivery::getId', '\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Entity\\Delivery::getMailMessage', '\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Entity\\Delivery::getProcessingTimeMillis', '\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Entity\\Delivery::getReportingMta', '\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Entity\\Delivery::getSmtpResponse']
        ],
        'src/Entity/EmailStatus.php' => [
            'PhanDeprecatedClassConstant' => ['\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Entity\\EmailStatus'],
            'PhanUnreferencedPublicMethod' => ['\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Entity\\EmailStatus::getAddress', '\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Entity\\EmailStatus::getBounces', '\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Entity\\EmailStatus::getDeliveries', '\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Entity\\EmailStatus::getLastTimeBounced', '\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Entity\\EmailStatus::getLastTimeComplained', '\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Entity\\EmailStatus::getLastTimeDelivered'],
            'PhanUnusedPublicNoOverrideMethodParameter' => ['\\closure']
        ],
        'src/Entity/MailMessage.php' => [
            'PhanDeprecatedClassConstant' => ['\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Entity\\MailMessage'],
            'PhanUnreferencedPublicMethod' => ['\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Entity\\MailMessage::getBounces', '\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Entity\\MailMessage::getCommonHeaders', '\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Entity\\MailMessage::getComplaints', '\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Entity\\MailMessage::getDeliveries', '\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Entity\\MailMessage::getHeaders', '\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Entity\\MailMessage::getMessageId', '\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Entity\\MailMessage::getSendingAccountId', '\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Entity\\MailMessage::getSentFrom', '\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Entity\\MailMessage::getSentOn', '\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Entity\\MailMessage::getSourceArn']
        ],
        'src/Entity/Topic.php' => [
            'PhanReadOnlyPrivateProperty' => ['\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Entity\\Topic'],
            'PhanUnreferencedPublicMethod' => ['\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Entity\\Topic::getId', '\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Entity\\Topic::getName']
        ],
        'src/EventListener/MonitorFilterListener.php' => [
            'PhanUnreferencedClass' => ['src/EventListener/MonitorFilterListener.php'],
            'PhanUnreferencedPublicMethod' => ['\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\EventListener\\MonitorFilterListener::onMessage']
        ],
        'src/Manager/SesManager.php' => [
            'PhanUnreferencedPublicMethod' => ['\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Manager\\SesManager::getClient', '\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Manager\\SesManager::listIdentities']
        ],
        'src/Processor/AwsDataProcessor.php' => [
            'PhanTypeArraySuspiciousNullable' => ['\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Processor\\AwsDataProcessor::processSubscriptionAttributes', '\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Processor\\AwsDataProcessor::processTopicAttributes']
        ],
        'src/Processor/NotificationProcessor.php' => [
            'PhanAccessMethodInternal' => ['\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Processor\\NotificationProcessor::processRequest']
        ],
        'src/Processor/SubscriptionProcessor.php' => [
            'PhanAccessMethodInternal' => ['\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Processor\\SubscriptionProcessor::processRequest']
        ],
        'src/SHQAwsSesMonitorBundle.php' => [
            'PhanUnreferencedClass' => ['src/SHQAwsSesMonitorBundle.php']
        ],
        'src/Service/Monitor.php' => [
            'PhanAccessClassConstantInternal' => ['\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Service\\Monitor::findConfiguredIdentity', '\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Service\\Monitor::getAccount', '\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Service\\Monitor::getLiveIdentitiesList', '\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Service\\Monitor::getLiveIdentity', '\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Service\\Monitor::getLiveTopic', '\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Service\\Monitor::getLiveTopicsList', '\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Service\\Monitor::getTopicSubscriptions', '\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Service\\Monitor::identityRequiresTopicSubscription', '\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Service\\Monitor::liveIdentityExists'],
            'PhanAccessMethodInternal' => ['\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Service\\Monitor::fetchAccountData', '\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Service\\Monitor::fetchIdentitiesData', '\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Service\\Monitor::fetchSubscriptionsData', '\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Service\\Monitor::fetchTopicsData', '\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Service\\Monitor::findConfiguredIdentity', '\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Service\\Monitor::fromDomainCanBeSynched', '\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Service\\Monitor::getConfiguredIdentitiesList', '\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Service\\Monitor::retrieve'],
            'PhanRedefinedClassReference' => ['\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Service\\Monitor', '\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Service\\Monitor::retrieve'],
            'PhanUnreferencedPublicMethod' => ['\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Service\\Monitor::getLiveIdentitiesList'],
            'PhanUnusedVariableCaughtException' => ['\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Service\\Monitor::fetchSubscriptionsData']
        ],
        'src/Util/Console.php' => [
            'PhanRedefinedClassReference' => ['\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Util\\Console::clear', '\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Util\\Console::createSection', '\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Util\\Console::createWriter', '\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Util\\Console::overwrite'],
            'PhanTypeMismatchDeclaredReturn' => ['\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Util\\Console::createSection'],
            'PhanUndeclaredMethod' => ['\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Util\\Console::clear', '\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Util\\Console::createSection', '\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Util\\Console::overwrite']
        ],
        'src/Util/EmailStatusAnalyzer.php' => [
            'PhanAccessMethodInternal' => ['\\SerendipityHQ\\Bundle\\AwsSesMonitorBundle\\Util\\EmailStatusAnalyzer::canReceiveMessages']
        ],
    ],
    // 'directory_suppressions' => ['src/directory_name' => ['PhanIssueName1', 'PhanIssueName2']] can be manually added if needed.
    // (directory_suppressions will currently be ignored by subsequent calls to --save-baseline, but may be preserved in future Phan releases)
];
