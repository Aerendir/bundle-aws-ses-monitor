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
    // PhanAccessMethodInternal : 250+ occurrences
    // PhanRedefinedClassReference : 180+ occurrences
    // PhanUnreferencedPublicMethod : 100+ occurrences
    // PhanTypeMismatchArgument : 80+ occurrences
    // PhanAccessClassConstantInternal : 70+ occurrences
    // PhanTypeMismatchArgumentProbablyReal : 50+ occurrences
    // PhanTypeMismatchArgumentReal : 30+ occurrences
    // PhanTypeArraySuspiciousNullable : 25+ occurrences
    // PhanUnreferencedPublicClassConstant : 15+ occurrences
    // PhanDeprecatedFunction : 10+ occurrences
    // PhanPluginUnreachableCode : 5 occurrences
    // PhanReadOnlyPrivateProperty : 4 occurrences
    // PhanUndeclaredProperty : 4 occurrences
    // PhanRedefinedExtendedClass : 3 occurrences
    // PhanUndeclaredMethod : 3 occurrences
    // PhanUnusedClosureParameter : 3 occurrences
    // ConstReferenceConstNotFound : 2 occurrences
    // PhanUndeclaredConstantOfClass : 2 occurrences
    // PhanUnreferencedClosure : 2 occurrences
    // PhanUnusedVariable : 2 occurrences
    // PhanNoopNew : 1 occurrence
    // PhanTypeMismatchDeclaredParamNullable : 1 occurrence
    // PhanTypeMismatchDeclaredReturn : 1 occurrence
    // PhanUnreferencedPrivateProperty : 1 occurrence
    // PhanUnusedVariableCaughtException : 1 occurrence
    // UndeclaredTypeInInlineVar : 1 occurrence

    // Currently, file_suppressions and directory_suppressions are the only supported suppressions
    'file_suppressions' => [
        'src/Command/ConfigureCommand.php' => ['ConstReferenceConstNotFound', 'PhanAccessMethodInternal', 'PhanDeprecatedFunction', 'PhanRedefinedClassReference', 'PhanRedefinedExtendedClass', 'PhanTypeArraySuspiciousNullable'],
        'src/Command/DebugCommand.php' => ['PhanAccessClassConstantInternal', 'PhanAccessMethodInternal', 'PhanDeprecatedFunction', 'PhanRedefinedClassReference', 'PhanRedefinedExtendedClass'],
        'src/Command/SesSendTestEmailsCommand.php' => ['PhanRedefinedClassReference', 'PhanRedefinedExtendedClass'],
        'src/Controller/EndpointController.php' => ['PhanRedefinedClassReference', 'PhanUnreferencedPublicMethod'],
        'src/DependencyInjection/Configuration.php' => ['PhanAccessMethodInternal', 'PhanUndeclaredMethod', 'PhanUnreferencedClosure'],
        'src/Entity/Bounce.php' => ['PhanReadOnlyPrivateProperty', 'PhanUnreferencedPublicClassConstant', 'PhanUnreferencedPublicMethod'],
        'src/Entity/Complaint.php' => ['PhanReadOnlyPrivateProperty', 'PhanUnreferencedPublicClassConstant', 'PhanUnreferencedPublicMethod'],
        'src/Entity/Delivery.php' => ['PhanReadOnlyPrivateProperty', 'PhanUnreferencedPublicMethod'],
        'src/Entity/EmailStatus.php' => ['PhanRedefinedClassReference', 'PhanUnusedClosureParameter'],
        'src/Entity/MailMessage.php' => ['PhanRedefinedClassReference'],
        'src/Entity/Topic.php' => ['PhanReadOnlyPrivateProperty', 'PhanUnreferencedPublicMethod'],
        'src/Manager/EmailStatusManager.php' => ['PhanRedefinedClassReference'],
        'src/Manager/SesManager.php' => ['PhanUnreferencedPublicMethod'],
        'src/Processor/AwsDataProcessor.php' => ['PhanTypeArraySuspiciousNullable'],
        'src/Processor/NotificationProcessor.php' => ['PhanAccessMethodInternal', 'PhanRedefinedClassReference'],
        'src/Processor/SubscriptionProcessor.php' => ['PhanAccessMethodInternal', 'PhanRedefinedClassReference'],
        'src/Service/Monitor.php' => ['PhanAccessClassConstantInternal', 'PhanAccessMethodInternal', 'PhanRedefinedClassReference', 'PhanTypeMismatchDeclaredParamNullable', 'PhanUndeclaredConstantOfClass', 'PhanUnreferencedPublicMethod', 'PhanUnusedVariableCaughtException'],
        'src/Util/Console.php' => ['PhanRedefinedClassReference', 'PhanTypeMismatchDeclaredReturn', 'PhanUndeclaredMethod'],
        'src/Util/EmailStatusAnalyzer.php' => ['PhanAccessMethodInternal', 'PhanRedefinedClassReference'],
        'tests/Command/SesSendTestEmailsCommandTest.php' => ['PhanRedefinedClassReference', 'PhanTypeMismatchArgument', 'PhanUnreferencedPublicMethod', 'UndeclaredTypeInInlineVar'],
        'tests/DependencyInjection/AbstractSerendipityHQAwsSesBouncerExtensionTest.php' => ['PhanTypeArraySuspiciousNullable', 'PhanUnreferencedPublicMethod'],
        'tests/Entity/BounceTest.php' => ['PhanTypeMismatchArgument', 'PhanUnreferencedPublicMethod'],
        'tests/Entity/ComplaintTest.php' => ['PhanTypeMismatchArgument', 'PhanUnreferencedPublicMethod'],
        'tests/Entity/DeliveryTest.php' => ['PhanTypeMismatchArgument', 'PhanUnreferencedPublicMethod'],
        'tests/Entity/EmailStatusTest.php' => ['PhanAccessMethodInternal', 'PhanRedefinedClassReference', 'PhanTypeMismatchArgument', 'PhanUnreferencedPublicMethod'],
        'tests/Entity/MailMessageTest.php' => ['PhanRedefinedClassReference', 'PhanTypeMismatchArgument', 'PhanUnreferencedPublicMethod'],
        'tests/Entity/TopicTest.php' => ['PhanUnreferencedPublicMethod'],
        'tests/Handler/BounceNotificationHandlerTest.php' => ['PhanTypeMismatchArgument', 'PhanTypeMismatchArgumentReal', 'PhanUnreferencedPublicMethod'],
        'tests/Handler/ComplaintNotificationHandlerTest.php' => ['PhanTypeMismatchArgument', 'PhanTypeMismatchArgumentReal', 'PhanUnreferencedPublicMethod'],
        'tests/Handler/DeliveryNotificationHandlerTest.php' => ['PhanTypeMismatchArgument', 'PhanTypeMismatchArgumentReal', 'PhanUnreferencedPublicMethod'],
        'tests/Helper/MessageHelperTest.php' => ['PhanAccessMethodInternal', 'PhanTypeMismatchArgument', 'PhanUnreferencedPublicMethod'],
        'tests/Manager/EmailStatusManagerTest.php' => ['PhanRedefinedClassReference', 'PhanTypeMismatchArgument', 'PhanTypeMismatchArgumentProbablyReal', 'PhanUnreferencedPublicMethod'],
        'tests/Manager/SesManagerTest.php' => ['PhanTypeMismatchArgument', 'PhanUnreferencedPublicMethod'],
        'tests/Manager/SnsManagerTest.php' => ['PhanNoopNew', 'PhanTypeMismatchArgument', 'PhanTypeMismatchArgumentProbablyReal', 'PhanUnreferencedPublicMethod'],
        'tests/Plugin/MonitorFilterPluginTest.php' => ['ConstReferenceConstNotFound', 'PhanPluginUnreachableCode', 'PhanRedefinedClassReference', 'PhanTypeMismatchArgument', 'PhanTypeMismatchArgumentReal', 'PhanUnreferencedPublicMethod', 'PhanUnusedVariable'],
        'tests/Processor/AwsDataProcessorTest.php' => ['PhanAccessClassConstantInternal', 'PhanAccessMethodInternal', 'PhanTypeMismatchArgument', 'PhanTypeMismatchArgumentProbablyReal', 'PhanUnreferencedPublicMethod'],
        'tests/Processor/NotificationProcessorTest.php' => ['PhanRedefinedClassReference', 'PhanTypeMismatchArgument', 'PhanTypeMismatchArgumentReal', 'PhanUnreferencedPublicMethod'],
        'tests/Processor/RequestProcessorTest.php' => ['PhanTypeMismatchArgument', 'PhanTypeMismatchArgumentReal', 'PhanUndeclaredProperty', 'PhanUnreferencedPublicMethod'],
        'tests/Processor/SubscriptionProcessorTest.php' => ['PhanRedefinedClassReference', 'PhanTypeMismatchArgument', 'PhanTypeMismatchArgumentProbablyReal', 'PhanTypeMismatchArgumentReal', 'PhanUnreferencedPublicMethod'],
        'tests/Service/IdentitiesStoreTest.php' => ['PhanAccessMethodInternal', 'PhanUnreferencedPublicMethod'],
        'tests/Service/MonitorTest.php' => ['PhanAccessClassConstantInternal', 'PhanAccessMethodInternal', 'PhanRedefinedClassReference', 'PhanTypeMismatchArgumentProbablyReal', 'PhanTypeMismatchArgumentReal', 'PhanUnreferencedPrivateProperty', 'PhanUnreferencedPublicMethod', 'PhanUnusedVariable'],
        'tests/Util/ConsoleTest.php' => ['PhanAccessMethodInternal', 'PhanRedefinedClassReference', 'PhanTypeMismatchArgument', 'PhanTypeMismatchArgumentProbablyReal', 'PhanUnreferencedPublicMethod'],
        'tests/Util/EmailStatusAnalyzerTest.php' => ['PhanRedefinedClassReference', 'PhanTypeMismatchArgument', 'PhanTypeMismatchArgumentProbablyReal', 'PhanTypeMismatchArgumentReal', 'PhanUnreferencedPublicMethod'],
        'tests/Util/IdentityGuesserTest.php' => ['PhanAccessClassConstantInternal', 'PhanAccessMethodInternal', 'PhanUnreferencedPublicMethod'],
    ],
    // 'directory_suppressions' => ['src/directory_name' => ['PhanIssueName1', 'PhanIssueName2']] can be manually added if needed.
    // (directory_suppressions will currently be ignored by subsequent calls to --save-baseline, but may be preserved in future Phan releases)
];
