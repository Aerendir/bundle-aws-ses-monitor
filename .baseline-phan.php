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
    // PhanRedefinedClassReference : 180+ occurrences
    // PhanAccessMethodInternal : 150+ occurrences
    // PhanUnreferencedPublicMethod : 40+ occurrences
    // PhanAccessClassConstantInternal : 15+ occurrences
    // PhanTypeArraySuspiciousNullable : 15+ occurrences
    // PhanUnreferencedPublicClassConstant : 15+ occurrences
    // PhanRedefinedExtendedClass : 6 occurrences
    // PhanUnreferencedClass : 6 occurrences
    // PhanUnreferencedClosure : 5 occurrences
    // PhanDeprecatedImplicitNullableParam : 4 occurrences
    // PhanReadOnlyPrivateProperty : 4 occurrences
    // PhanUndeclaredMethod : 3 occurrences
    // PhanUnusedPublicNoOverrideMethodParameter : 3 occurrences
    // ConstReferenceConstNotFound : 1 occurrence
    // PhanRedefinedInheritedInterface : 1 occurrence
    // PhanTypeMismatchDeclaredReturn : 1 occurrence
    // PhanUnusedVariableCaughtException : 1 occurrence

    // Currently, file_suppressions and directory_suppressions are the only supported suppressions
    'file_suppressions' => [
        'src/Command/ConfigureCommand.php' => ['ConstReferenceConstNotFound', 'PhanAccessMethodInternal', 'PhanRedefinedClassReference', 'PhanRedefinedExtendedClass', 'PhanTypeArraySuspiciousNullable', 'PhanUnreferencedClass'],
        'src/Command/DebugCommand.php' => ['PhanAccessClassConstantInternal', 'PhanAccessMethodInternal', 'PhanRedefinedClassReference', 'PhanRedefinedExtendedClass', 'PhanUnreferencedClass'],
        'src/Command/SesSendTestEmailsCommand.php' => ['PhanRedefinedClassReference', 'PhanRedefinedExtendedClass', 'PhanUnreferencedClass'],
        'src/Controller/EndpointController.php' => ['PhanRedefinedClassReference', 'PhanRedefinedExtendedClass', 'PhanUnreferencedClass', 'PhanUnreferencedPublicMethod'],
        'src/DependencyInjection/Configuration.php' => ['PhanAccessMethodInternal', 'PhanRedefinedClassReference', 'PhanRedefinedInheritedInterface', 'PhanUndeclaredMethod', 'PhanUnreferencedClosure'],
        'src/DependencyInjection/SHQAwsSesMonitorExtension.php' => ['PhanRedefinedClassReference', 'PhanRedefinedExtendedClass', 'PhanUnreferencedClass'],
        'src/Entity/Bounce.php' => ['PhanReadOnlyPrivateProperty', 'PhanUnreferencedPublicClassConstant', 'PhanUnreferencedPublicMethod'],
        'src/Entity/Complaint.php' => ['PhanReadOnlyPrivateProperty', 'PhanUnreferencedPublicClassConstant', 'PhanUnreferencedPublicMethod'],
        'src/Entity/Delivery.php' => ['PhanReadOnlyPrivateProperty', 'PhanUnreferencedPublicMethod'],
        'src/Entity/EmailStatus.php' => ['PhanUnreferencedClosure', 'PhanUnreferencedPublicMethod', 'PhanUnusedPublicNoOverrideMethodParameter'],
        'src/Entity/MailMessage.php' => ['PhanUnreferencedPublicMethod'],
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
        'src/SHQAwsSesMonitorBundle.php' => ['PhanRedefinedClassReference', 'PhanRedefinedExtendedClass', 'PhanUnreferencedClass'],
        'src/Service/Monitor.php' => ['PhanAccessClassConstantInternal', 'PhanAccessMethodInternal', 'PhanDeprecatedImplicitNullableParam', 'PhanRedefinedClassReference', 'PhanUnreferencedPublicMethod', 'PhanUnusedVariableCaughtException'],
        'src/Util/Console.php' => ['PhanRedefinedClassReference', 'PhanTypeMismatchDeclaredReturn', 'PhanUndeclaredMethod'],
        'src/Util/EmailStatusAnalyzer.php' => ['PhanAccessMethodInternal'],
    ],
    // 'directory_suppressions' => ['src/directory_name' => ['PhanIssueName1', 'PhanIssueName2']] can be manually added if needed.
    // (directory_suppressions will currently be ignored by subsequent calls to --save-baseline, but may be preserved in future Phan releases)
];
