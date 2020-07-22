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
    // PhanAccessMethodInternal : 620+ occurrences
    // PhanRedefinedClassReference : 600+ occurrences
    // PhanTypeMismatchArgument : 110+ occurrences
    // PhanUnreferencedPublicMethod : 100+ occurrences
    // PhanRedefinedExtendedClass : 25+ occurrences
    // PhanAccessClassConstantInternal : 15+ occurrences
    // PhanTypeArraySuspiciousNullable : 15+ occurrences
    // PhanUnreferencedPublicClassConstant : 15+ occurrences
    // PhanReadOnlyPrivateProperty : 7 occurrences
    // PhanPluginUnreachableCode : 5 occurrences
    // PhanUndeclaredProperty : 4 occurrences
    // PhanUndeclaredMethod : 3 occurrences
    // PhanUnusedClosureParameter : 3 occurrences
    // ConstReferenceConstNotFound : 2 occurrences
    // PhanUnreferencedClosure : 2 occurrences
    // PhanDeprecatedClass : 1 occurrence
    // PhanDeprecatedFunction : 1 occurrence
    // PhanNoopConstant : 1 occurrence
    // PhanNoopNew : 1 occurrence
    // PhanTypeMismatchDeclaredParamNullable : 1 occurrence
    // PhanTypeMismatchPropertyDefault : 1 occurrence
    // PhanTypeMissingReturn : 1 occurrence
    // PhanUnreferencedPrivateProperty : 1 occurrence
    // PhanUnusedVariable : 1 occurrence
    // PhanUnusedVariableCaughtException : 1 occurrence

    // Currently, file_suppressions and directory_suppressions are the only supported suppressions
    'file_suppressions' => [
        'src/Command/ConfigureCommand.php' => ['ConstReferenceConstNotFound', 'PhanAccessMethodInternal', 'PhanRedefinedClassReference', 'PhanRedefinedExtendedClass', 'PhanTypeArraySuspiciousNullable'],
        'src/Command/DebugCommand.php' => ['PhanAccessClassConstantInternal', 'PhanAccessMethodInternal', 'PhanRedefinedClassReference', 'PhanRedefinedExtendedClass', 'PhanTypeMissingReturn'],
        'src/Command/SesSendTestEmailsCommand.php' => ['PhanRedefinedClassReference', 'PhanRedefinedExtendedClass'],
        'src/Controller/EndpointController.php' => ['PhanDeprecatedClass', 'PhanRedefinedClassReference', 'PhanUnreferencedPublicMethod'],
        'src/DependencyInjection/Configuration.php' => ['PhanAccessMethodInternal', 'PhanDeprecatedFunction', 'PhanUndeclaredMethod', 'PhanUnreferencedClosure'],
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
        'src/Service/Monitor.php' => ['PhanAccessClassConstantInternal', 'PhanAccessMethodInternal', 'PhanNoopConstant', 'PhanRedefinedClassReference', 'PhanTypeArraySuspiciousNullable', 'PhanTypeMismatchDeclaredParamNullable', 'PhanUnreferencedPublicMethod', 'PhanUnusedVariableCaughtException'],
        'src/Util/Console.php' => ['PhanRedefinedClassReference', 'PhanUndeclaredMethod'],
        'src/Util/EmailStatusAnalyzer.php' => ['PhanAccessMethodInternal', 'PhanRedefinedClassReference'],
        'tests/Command/SesSendTestEmailsCommandTest.php' => ['PhanAccessMethodInternal', 'PhanRedefinedClassReference', 'PhanRedefinedExtendedClass', 'PhanTypeMismatchArgument', 'PhanUnreferencedPublicMethod'],
        'tests/DependencyInjection/AbstractSerendipityHQAwsSesBouncerExtensionTest.php' => ['PhanRedefinedExtendedClass', 'PhanUnreferencedPublicMethod'],
        'tests/Entity/BounceTest.php' => ['PhanRedefinedExtendedClass', 'PhanTypeMismatchArgument', 'PhanUnreferencedPublicMethod'],
        'tests/Entity/ComplaintTest.php' => ['PhanRedefinedExtendedClass', 'PhanTypeMismatchArgument', 'PhanUnreferencedPublicMethod'],
        'tests/Entity/DeliveryTest.php' => ['PhanRedefinedExtendedClass', 'PhanTypeMismatchArgument', 'PhanUnreferencedPublicMethod'],
        'tests/Entity/EmailStatusTest.php' => ['PhanAccessMethodInternal', 'PhanRedefinedClassReference', 'PhanRedefinedExtendedClass', 'PhanTypeMismatchArgument', 'PhanUnreferencedPublicMethod'],
        'tests/Entity/MailMessageTest.php' => ['PhanRedefinedClassReference', 'PhanRedefinedExtendedClass', 'PhanTypeMismatchArgument', 'PhanUnreferencedPublicMethod'],
        'tests/Entity/TopicTest.php' => ['PhanRedefinedExtendedClass', 'PhanUnreferencedPublicMethod'],
        'tests/Handler/BounceNotificationHandlerTest.php' => ['PhanAccessMethodInternal', 'PhanRedefinedClassReference', 'PhanRedefinedExtendedClass', 'PhanTypeMismatchArgument', 'PhanUnreferencedPublicMethod'],
        'tests/Handler/ComplaintNotificationHandlerTest.php' => ['PhanAccessMethodInternal', 'PhanRedefinedClassReference', 'PhanRedefinedExtendedClass', 'PhanTypeMismatchArgument', 'PhanUnreferencedPublicMethod'],
        'tests/Handler/DeliveryNotificationHandlerTest.php' => ['PhanAccessMethodInternal', 'PhanRedefinedClassReference', 'PhanRedefinedExtendedClass', 'PhanTypeMismatchArgument', 'PhanUnreferencedPublicMethod'],
        'tests/Helper/MessageHelperTest.php' => ['PhanAccessMethodInternal', 'PhanReadOnlyPrivateProperty', 'PhanRedefinedClassReference', 'PhanRedefinedExtendedClass', 'PhanTypeMismatchArgument', 'PhanTypeMismatchPropertyDefault', 'PhanUnreferencedPublicMethod'],
        'tests/Manager/EmailStatusManagerTest.php' => ['PhanAccessMethodInternal', 'PhanRedefinedClassReference', 'PhanRedefinedExtendedClass', 'PhanTypeMismatchArgument', 'PhanUnreferencedPublicMethod'],
        'tests/Manager/SesManagerTest.php' => ['PhanRedefinedExtendedClass', 'PhanTypeMismatchArgument', 'PhanUnreferencedPublicMethod'],
        'tests/Manager/SnsManagerTest.php' => ['PhanAccessMethodInternal', 'PhanNoopNew', 'PhanReadOnlyPrivateProperty', 'PhanRedefinedClassReference', 'PhanRedefinedExtendedClass', 'PhanTypeMismatchArgument', 'PhanUnreferencedPublicMethod'],
        'tests/Plugin/MonitorFilterPluginTest.php' => ['ConstReferenceConstNotFound', 'PhanAccessMethodInternal', 'PhanPluginUnreachableCode', 'PhanRedefinedClassReference', 'PhanRedefinedExtendedClass', 'PhanTypeMismatchArgument', 'PhanUnreferencedPublicMethod', 'PhanUnusedVariable'],
        'tests/Processor/AwsDataProcessorTest.php' => ['PhanAccessMethodInternal', 'PhanRedefinedClassReference', 'PhanRedefinedExtendedClass', 'PhanTypeMismatchArgument', 'PhanUnreferencedPublicMethod'],
        'tests/Processor/NotificationProcessorTest.php' => ['PhanAccessMethodInternal', 'PhanRedefinedClassReference', 'PhanRedefinedExtendedClass', 'PhanTypeMismatchArgument', 'PhanUnreferencedPublicMethod'],
        'tests/Processor/RequestProcessorTest.php' => ['PhanAccessMethodInternal', 'PhanRedefinedClassReference', 'PhanRedefinedExtendedClass', 'PhanTypeMismatchArgument', 'PhanUndeclaredProperty', 'PhanUnreferencedPublicMethod'],
        'tests/Processor/SubscriptionProcessorTest.php' => ['PhanAccessMethodInternal', 'PhanRedefinedClassReference', 'PhanRedefinedExtendedClass', 'PhanTypeMismatchArgument', 'PhanUnreferencedPublicMethod'],
        'tests/Service/IdentitiesStoreTest.php' => ['PhanAccessMethodInternal', 'PhanRedefinedExtendedClass', 'PhanUnreferencedPublicMethod'],
        'tests/Service/MonitorTest.php' => ['PhanAccessClassConstantInternal', 'PhanAccessMethodInternal', 'PhanReadOnlyPrivateProperty', 'PhanRedefinedClassReference', 'PhanRedefinedExtendedClass', 'PhanTypeMismatchArgument', 'PhanUnreferencedPrivateProperty', 'PhanUnreferencedPublicMethod'],
        'tests/Util/ConsoleTest.php' => ['PhanAccessMethodInternal', 'PhanRedefinedClassReference', 'PhanRedefinedExtendedClass', 'PhanTypeMismatchArgument', 'PhanUnreferencedPublicMethod'],
        'tests/Util/EmailStatusAnalyzerTest.php' => ['PhanAccessMethodInternal', 'PhanRedefinedClassReference', 'PhanRedefinedExtendedClass', 'PhanTypeMismatchArgument', 'PhanUnreferencedPublicMethod'],
        'tests/Util/IdentityGuesserTest.php' => ['PhanAccessClassConstantInternal', 'PhanAccessMethodInternal', 'PhanRedefinedExtendedClass', 'PhanUnreferencedPublicMethod'],
    ],
    // 'directory_suppressions' => ['src/directory_name' => ['PhanIssueName1', 'PhanIssueName2']] can be manually added if needed.
    // (directory_suppressions will currently be ignored by subsequent calls to --save-baseline, but may be preserved in future Phan releases)
];
