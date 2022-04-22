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
    // PhanUndeclaredStaticMethod : 410+ occurrences
    // PhanRedefinedClassReference : 290+ occurrences
    // PhanAccessMethodInternal : 240+ occurrences
    // PhanUndeclaredMethod : 160+ occurrences
    // PhanUnreferencedPublicMethod : 100+ occurrences
    // PhanAccessClassConstantInternal : 70+ occurrences
    // PhanRedefinedExtendedClass : 30+ occurrences
    // PhanTypeArraySuspiciousNullable : 25+ occurrences
    // PhanUnreferencedClass : 25+ occurrences
    // PhanUnreferencedPublicClassConstant : 15+ occurrences
    // PhanImpossibleIntersectionType : 9 occurrences
    // PhanUnreferencedProtectedMethod : 8 occurrences
    // PhanReadOnlyPrivateProperty : 4 occurrences
    // PhanTypeMismatchArgumentProbablyReal : 3 occurrences
    // PhanUnreferencedClosure : 3 occurrences
    // PhanUnusedClosureParameter : 3 occurrences
    // ConstReferenceConstNotFound : 2 occurrences
    // PhanUndeclaredConstantOfClass : 2 occurrences
    // PhanUnusedVariable : 2 occurrences
    // PhanNoopNew : 1 occurrence
    // PhanRedefinedInheritedInterface : 1 occurrence
    // PhanTypeMismatchDeclaredReturn : 1 occurrence
    // PhanUnreferencedPrivateProperty : 1 occurrence
    // PhanUnusedVariableCaughtException : 1 occurrence
    // UndeclaredTypeInInlineVar : 1 occurrence

    // Currently, file_suppressions and directory_suppressions are the only supported suppressions
    'file_suppressions' => [
        'src/Command/ConfigureCommand.php' => ['ConstReferenceConstNotFound', 'PhanAccessMethodInternal', 'PhanRedefinedClassReference', 'PhanRedefinedExtendedClass', 'PhanTypeArraySuspiciousNullable'],
        'src/Command/DebugCommand.php' => ['PhanAccessClassConstantInternal', 'PhanAccessMethodInternal', 'PhanRedefinedClassReference', 'PhanRedefinedExtendedClass'],
        'src/Command/SesSendTestEmailsCommand.php' => ['PhanRedefinedClassReference', 'PhanRedefinedExtendedClass'],
        'src/Controller/EndpointController.php' => ['PhanRedefinedClassReference', 'PhanRedefinedExtendedClass', 'PhanUnreferencedClass', 'PhanUnreferencedPublicMethod'],
        'src/DependencyInjection/Configuration.php' => ['PhanAccessMethodInternal', 'PhanRedefinedClassReference', 'PhanRedefinedInheritedInterface', 'PhanUndeclaredMethod', 'PhanUnreferencedClosure'],
        'src/DependencyInjection/SHQAwsSesMonitorExtension.php' => ['PhanRedefinedClassReference', 'PhanRedefinedExtendedClass'],
        'src/Entity/Bounce.php' => ['PhanReadOnlyPrivateProperty', 'PhanUnreferencedPublicClassConstant', 'PhanUnreferencedPublicMethod'],
        'src/Entity/Complaint.php' => ['PhanReadOnlyPrivateProperty', 'PhanUnreferencedPublicClassConstant', 'PhanUnreferencedPublicMethod'],
        'src/Entity/Delivery.php' => ['PhanReadOnlyPrivateProperty', 'PhanUnreferencedPublicMethod'],
        'src/Entity/EmailStatus.php' => ['PhanUnusedClosureParameter'],
        'src/Entity/Topic.php' => ['PhanReadOnlyPrivateProperty', 'PhanUnreferencedPublicMethod'],
        'src/Handler/AbstractNotification.php' => ['PhanRedefinedClassReference'],
        'src/Handler/BounceNotificationHandler.php' => ['PhanRedefinedClassReference'],
        'src/Handler/ComplaintNotificationHandler.php' => ['PhanRedefinedClassReference'],
        'src/Handler/DeliveryNotificationHandler.php' => ['PhanRedefinedClassReference'],
        'src/Helper/MessageHelper.php' => ['PhanRedefinedClassReference'],
        'src/Manager/SesManager.php' => ['PhanUnreferencedPublicMethod'],
        'src/Manager/SnsManager.php' => ['PhanRedefinedClassReference'],
        'src/Processor/AwsDataProcessor.php' => ['PhanTypeArraySuspiciousNullable'],
        'src/Processor/NotificationProcessor.php' => ['PhanAccessMethodInternal', 'PhanRedefinedClassReference'],
        'src/Processor/RequestProcessor.php' => ['PhanRedefinedClassReference'],
        'src/Processor/SubscriptionProcessor.php' => ['PhanAccessMethodInternal', 'PhanRedefinedClassReference'],
        'src/SHQAwsSesMonitorBundle.php' => ['PhanRedefinedClassReference', 'PhanRedefinedExtendedClass'],
        'src/Service/Monitor.php' => ['PhanAccessClassConstantInternal', 'PhanAccessMethodInternal', 'PhanRedefinedClassReference', 'PhanUndeclaredConstantOfClass', 'PhanUnreferencedPublicMethod', 'PhanUnusedVariableCaughtException'],
        'src/Util/Console.php' => ['PhanRedefinedClassReference', 'PhanTypeMismatchDeclaredReturn', 'PhanUndeclaredMethod'],
        'src/Util/EmailStatusAnalyzer.php' => ['PhanAccessMethodInternal'],
        'tests/Command/SesSendTestEmailsCommandTest.php' => ['PhanRedefinedClassReference', 'PhanRedefinedExtendedClass', 'PhanUndeclaredStaticMethod', 'PhanUnreferencedClass', 'PhanUnreferencedPublicMethod', 'UndeclaredTypeInInlineVar'],
        'tests/DependencyInjection/AbstractSerendipityHQAwsSesBouncerExtensionTest.php' => ['PhanRedefinedClassReference', 'PhanRedefinedExtendedClass', 'PhanTypeArraySuspiciousNullable', 'PhanUndeclaredStaticMethod', 'PhanUnreferencedProtectedMethod', 'PhanUnreferencedPublicMethod'],
        'tests/DependencyInjection/YamlAwsSesMonitorBundleExtensionTest.php' => ['PhanRedefinedClassReference', 'PhanUnreferencedClass'],
        'tests/Entity/BounceTest.php' => ['PhanRedefinedExtendedClass', 'PhanUndeclaredMethod', 'PhanUndeclaredStaticMethod', 'PhanUnreferencedClass', 'PhanUnreferencedPublicMethod'],
        'tests/Entity/ComplaintTest.php' => ['PhanRedefinedExtendedClass', 'PhanUndeclaredMethod', 'PhanUndeclaredStaticMethod', 'PhanUnreferencedClass', 'PhanUnreferencedPublicMethod'],
        'tests/Entity/DeliveryTest.php' => ['PhanRedefinedExtendedClass', 'PhanUndeclaredMethod', 'PhanUndeclaredStaticMethod', 'PhanUnreferencedClass', 'PhanUnreferencedPublicMethod'],
        'tests/Entity/EmailStatusTest.php' => ['PhanAccessMethodInternal', 'PhanRedefinedExtendedClass', 'PhanUndeclaredMethod', 'PhanUndeclaredStaticMethod', 'PhanUnreferencedClass', 'PhanUnreferencedPublicMethod'],
        'tests/Entity/MailMessageTest.php' => ['PhanRedefinedExtendedClass', 'PhanUndeclaredMethod', 'PhanUndeclaredStaticMethod', 'PhanUnreferencedClass', 'PhanUnreferencedPublicMethod'],
        'tests/Entity/TopicTest.php' => ['PhanRedefinedExtendedClass', 'PhanUndeclaredStaticMethod', 'PhanUnreferencedClass', 'PhanUnreferencedPublicMethod'],
        'tests/Handler/BounceNotificationHandlerTest.php' => ['PhanRedefinedClassReference', 'PhanRedefinedExtendedClass', 'PhanUndeclaredMethod', 'PhanUndeclaredStaticMethod', 'PhanUnreferencedClass', 'PhanUnreferencedPublicMethod'],
        'tests/Handler/ComplaintNotificationHandlerTest.php' => ['PhanRedefinedClassReference', 'PhanRedefinedExtendedClass', 'PhanUndeclaredMethod', 'PhanUndeclaredStaticMethod', 'PhanUnreferencedClass', 'PhanUnreferencedPublicMethod'],
        'tests/Handler/DeliveryNotificationHandlerTest.php' => ['PhanRedefinedClassReference', 'PhanRedefinedExtendedClass', 'PhanUndeclaredMethod', 'PhanUndeclaredStaticMethod', 'PhanUnreferencedClass', 'PhanUnreferencedPublicMethod'],
        'tests/Helper/MessageHelperTest.php' => ['PhanAccessMethodInternal', 'PhanRedefinedClassReference', 'PhanRedefinedExtendedClass', 'PhanUndeclaredMethod', 'PhanUndeclaredStaticMethod', 'PhanUnreferencedClass', 'PhanUnreferencedProtectedMethod', 'PhanUnreferencedPublicMethod'],
        'tests/Manager/EmailStatusManagerTest.php' => ['PhanRedefinedExtendedClass', 'PhanUndeclaredMethod', 'PhanUndeclaredStaticMethod', 'PhanUnreferencedClass', 'PhanUnreferencedPublicMethod'],
        'tests/Manager/SesManagerTest.php' => ['PhanRedefinedExtendedClass', 'PhanUndeclaredMethod', 'PhanUndeclaredStaticMethod', 'PhanUnreferencedClass', 'PhanUnreferencedPublicMethod'],
        'tests/Manager/SnsManagerTest.php' => ['PhanNoopNew', 'PhanRedefinedClassReference', 'PhanRedefinedExtendedClass', 'PhanUndeclaredMethod', 'PhanUndeclaredStaticMethod', 'PhanUnreferencedClass', 'PhanUnreferencedProtectedMethod', 'PhanUnreferencedPublicMethod'],
        'tests/Plugin/MonitorFilterPluginTest.php' => ['ConstReferenceConstNotFound', 'PhanRedefinedExtendedClass', 'PhanUndeclaredMethod', 'PhanUndeclaredStaticMethod', 'PhanUnreferencedClass', 'PhanUnreferencedPublicMethod', 'PhanUnusedVariable'],
        'tests/Processor/AwsDataProcessorTest.php' => ['PhanAccessClassConstantInternal', 'PhanAccessMethodInternal', 'PhanRedefinedExtendedClass', 'PhanUndeclaredMethod', 'PhanUndeclaredStaticMethod', 'PhanUnreferencedClass', 'PhanUnreferencedPublicMethod'],
        'tests/Processor/NotificationProcessorTest.php' => ['PhanImpossibleIntersectionType', 'PhanRedefinedClassReference', 'PhanRedefinedExtendedClass', 'PhanUndeclaredMethod', 'PhanUndeclaredStaticMethod', 'PhanUnreferencedClass', 'PhanUnreferencedProtectedMethod', 'PhanUnreferencedPublicMethod'],
        'tests/Processor/RequestProcessorTest.php' => ['PhanRedefinedClassReference', 'PhanRedefinedExtendedClass', 'PhanUndeclaredMethod', 'PhanUndeclaredStaticMethod', 'PhanUnreferencedClass', 'PhanUnreferencedProtectedMethod', 'PhanUnreferencedPublicMethod'],
        'tests/Processor/SubscriptionProcessorTest.php' => ['PhanImpossibleIntersectionType', 'PhanRedefinedClassReference', 'PhanRedefinedExtendedClass', 'PhanUndeclaredMethod', 'PhanUndeclaredStaticMethod', 'PhanUnreferencedClass', 'PhanUnreferencedClosure', 'PhanUnreferencedProtectedMethod', 'PhanUnreferencedPublicMethod'],
        'tests/Service/IdentitiesStoreTest.php' => ['PhanAccessMethodInternal', 'PhanRedefinedExtendedClass', 'PhanUndeclaredStaticMethod', 'PhanUnreferencedClass', 'PhanUnreferencedPublicMethod'],
        'tests/Service/MonitorTest.php' => ['PhanAccessClassConstantInternal', 'PhanAccessMethodInternal', 'PhanImpossibleIntersectionType', 'PhanRedefinedClassReference', 'PhanRedefinedExtendedClass', 'PhanTypeMismatchArgumentProbablyReal', 'PhanUndeclaredMethod', 'PhanUndeclaredStaticMethod', 'PhanUnreferencedClass', 'PhanUnreferencedPrivateProperty', 'PhanUnreferencedProtectedMethod', 'PhanUnreferencedPublicMethod', 'PhanUnusedVariable'],
        'tests/Util/ConsoleTest.php' => ['PhanAccessMethodInternal', 'PhanRedefinedClassReference', 'PhanRedefinedExtendedClass', 'PhanUndeclaredMethod', 'PhanUndeclaredStaticMethod', 'PhanUnreferencedClass', 'PhanUnreferencedPublicMethod'],
        'tests/Util/EmailStatusAnalyzerTest.php' => ['PhanRedefinedExtendedClass', 'PhanUndeclaredMethod', 'PhanUndeclaredStaticMethod', 'PhanUnreferencedClass', 'PhanUnreferencedPublicMethod'],
        'tests/Util/IdentityGuesserTest.php' => ['PhanAccessClassConstantInternal', 'PhanAccessMethodInternal', 'PhanRedefinedExtendedClass', 'PhanUndeclaredStaticMethod', 'PhanUnreferencedClass', 'PhanUnreferencedProtectedMethod', 'PhanUnreferencedPublicMethod'],
    ],
    // 'directory_suppressions' => ['src/directory_name' => ['PhanIssueName1', 'PhanIssueName2']] can be manually added if needed.
    // (directory_suppressions will currently be ignored by subsequent calls to --save-baseline, but may be preserved in future Phan releases)
];
