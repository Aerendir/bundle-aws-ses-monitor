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
    // PhanRedefinedClassReference : 170+ occurrences
    // PhanAccessMethodInternal : 150+ occurrences
    // PhanDeprecatedFunction : 55+ occurrences
    // PhanUnreferencedPublicMethod : 40+ occurrences
    // PhanAccessClassConstantInternal : 15+ occurrences
    // PhanTypeArraySuspiciousNullable : 15+ occurrences
    // PhanUnreferencedPublicClassConstant : 15+ occurrences
    // PhanRedefinedExtendedClass : 6 occurrences
    // PhanUnreferencedClass : 6 occurrences
    // PhanReadOnlyPrivateProperty : 4 occurrences
    // PhanUndeclaredMethod : 3 occurrences
    // PhanUnusedClosureParameter : 3 occurrences
    // PhanUndeclaredConstantOfClass : 2 occurrences
    // PhanUnreferencedClosure : 2 occurrences
    // ConstReferenceConstNotFound : 1 occurrence
    // PhanRedefinedInheritedInterface : 1 occurrence
    // PhanTypeMismatchDeclaredReturn : 1 occurrence
    // PhanUnusedVariableCaughtException : 1 occurrence

    // Currently, file_suppressions and directory_suppressions are the only supported suppressions
    'file_suppressions' => [
        'src/Command/ConfigureCommand.php' => ['ConstReferenceConstNotFound', 'PhanAccessMethodInternal', 'PhanDeprecatedFunction', 'PhanRedefinedClassReference', 'PhanRedefinedExtendedClass', 'PhanTypeArraySuspiciousNullable', 'PhanUnreferencedClass'],
        'src/Command/DebugCommand.php' => ['PhanAccessClassConstantInternal', 'PhanAccessMethodInternal', 'PhanDeprecatedFunction', 'PhanRedefinedClassReference', 'PhanRedefinedExtendedClass', 'PhanUnreferencedClass'],
        'src/Command/SesSendTestEmailsCommand.php' => ['PhanDeprecatedFunction', 'PhanRedefinedClassReference', 'PhanRedefinedExtendedClass', 'PhanUnreferencedClass'],
        'src/Controller/EndpointController.php' => ['PhanRedefinedClassReference', 'PhanRedefinedExtendedClass', 'PhanUnreferencedClass', 'PhanUnreferencedPublicMethod'],
        'src/DependencyInjection/Configuration.php' => ['PhanAccessMethodInternal', 'PhanDeprecatedFunction', 'PhanRedefinedClassReference', 'PhanRedefinedInheritedInterface', 'PhanUndeclaredMethod', 'PhanUnreferencedClosure'],
        'src/DependencyInjection/SHQAwsSesMonitorExtension.php' => ['PhanDeprecatedFunction', 'PhanRedefinedClassReference', 'PhanRedefinedExtendedClass', 'PhanUnreferencedClass'],
        'src/Entity/Bounce.php' => ['PhanReadOnlyPrivateProperty', 'PhanUnreferencedPublicClassConstant', 'PhanUnreferencedPublicMethod'],
        'src/Entity/Complaint.php' => ['PhanReadOnlyPrivateProperty', 'PhanUnreferencedPublicClassConstant', 'PhanUnreferencedPublicMethod'],
        'src/Entity/Delivery.php' => ['PhanReadOnlyPrivateProperty', 'PhanUnreferencedPublicMethod'],
        'src/Entity/EmailStatus.php' => ['PhanUnreferencedPublicMethod', 'PhanUnusedClosureParameter'],
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
        'src/Service/Monitor.php' => ['PhanAccessClassConstantInternal', 'PhanAccessMethodInternal', 'PhanDeprecatedFunction', 'PhanRedefinedClassReference', 'PhanUndeclaredConstantOfClass', 'PhanUnreferencedPublicMethod', 'PhanUnusedVariableCaughtException'],
        'src/Util/Console.php' => ['PhanRedefinedClassReference', 'PhanTypeMismatchDeclaredReturn', 'PhanUndeclaredMethod'],
        'src/Util/EmailStatusAnalyzer.php' => ['PhanAccessMethodInternal'],
        'src/Util/IdentityGuesser.php' => ['PhanDeprecatedFunction'],
    ],
    // 'directory_suppressions' => ['src/directory_name' => ['PhanIssueName1', 'PhanIssueName2']] can be manually added if needed.
    // (directory_suppressions will currently be ignored by subsequent calls to --save-baseline, but may be preserved in future Phan releases)
];
