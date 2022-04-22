<?php

/*
 * This file is part of the Serendipity HQ Aws Ses Bundle.
 *
 * Copyright (c) Adamo Aerendir Crespi <aerendir@serendipityhq.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use function Safe\preg_replace;
use function Safe\sprintf;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\DependencyInjection\Configuration;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Manager\SesManager;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Manager\SnsManager;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Service\Monitor;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\SnsTypes;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Util\Console;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\ConsoleSectionOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * {@inheritdoc}
 *
 * @ codeCoverageIgnore This command basically calls AWS and uses other classes already tested, so it is not testable.
 */
final class ConfigureCommand extends Command
{
    /**
     * @var string
     */
    private const FORCE = 'force';

    /**
     * @var string
     */
    private const TOPIC = 'topic';

    /**
     * @var string
     */
    protected static $defaultName = 'aws:ses:configure';

    private string $env;

    private EntityManagerInterface $entityManager;

    private array $actionsToTakeNow = [];

    private Monitor $monitor;

    private SesManager $sesManager;

    private SnsManager $snsManager;

    private SymfonyStyle $ioWriter;

    private Console $console;

    private ConsoleSectionOutput $sectionTitle;

    private ConsoleSectionOutput $sectionBody;

    private array $allowedIdentities;

    private array $skippedIdentities;

    /** The topics to create */
    private array $scheduledTopics = [];

    public function __construct(
        string $env,
        EntityManagerInterface $entityManager,
        Monitor $monitor,
        SesManager $sesManager,
        SnsManager $snsManager,
        Console $console
    ) {
        $this->env           = $env;
        $this->entityManager = $entityManager;
        $this->monitor       = $monitor;
        $this->sesManager    = $sesManager;
        $this->snsManager    = $snsManager;
        $this->console       = $console;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this->setDescription('Configures the identities on AWS SES and their topics.')
            ->addOption(self::FORCE, null, InputOption::VALUE_NONE, 'Forces the configuration of production identities, too.')
            ->addOption('full-log', null, InputOption::VALUE_NONE, 'Shows logs line by line, without simply changing the current one.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->console->enableFullLog((bool) $input->getOption('full-log'));
        $this->ioWriter = $this->console->createWriter($input, $output);

        $this->ioWriter->title('Configure AWS SES and SNS');
        $this->ioWriter->writeln(sprintf('Starting to configure identities for environment <comment>%s</comment>', $this->env));

        if (false === $this->canProceed($input)) {
            return 0;
        }

        $this->initializeConfiguration($output);
        $this->configureIdentities((bool) $input->getOption(self::FORCE));
        $this->configureTopics();
        $this->configureSubscriptions();

        $this->entityManager->flush();

        $this->console->clear($this->sectionBody);
        $this->console->clear($this->sectionTitle);

        $this->ioWriter->success('AWS SES is now configured.');

        $this->logActionsToTake();
        $this->logSkippedIdentities();

        return 0;
    }

    private function canProceed(InputInterface $input): bool
    {
        // Be sure the dev wants to configure production identities from dev env
        if ('prod' !== $this->env && $input->getOption(self::FORCE)) {
            $this->ioWriter->caution(
                <<<EOF
The --force option is very risky:

The --force option will change the endpoints of notifications used in production to the ones pointing to your development machine.\n
This will cause loss of notifications and less accurate filtering on production, as notifications will be sent by Amazon to your computer and not to your production servers.\n
You should use this option with extreme caution as it may lead to penalizations by Amazon SES and maybe to the suspension of your account if you send undesired emails, also if only by mistake.\n
EOF
            );

            if (false === $this->ioWriter->confirm('Please, confirm you want to force the configuration of live identities at your own risk', false)) {
                $this->ioWriter->success('Ok, for now your production servers are safe!');

                return false;
            }
        }

        return true;
    }

    private function initializeConfiguration(OutputInterface $output): void
    {
        $this->sectionTitle = $this->console->createSection($output);
        $this->sectionBody  = $this->console->createSection($output);
        $this->monitor->retrieve($this->sectionTitle, $this->sectionBody);
    }

    private function configureIdentities(bool $force): void
    {
        // Get identities to process
        $identities              = $this->monitor->getConfiguredIdentitiesList($force);
        $this->allowedIdentities = $identities['allowed'];
        $this->skippedIdentities = $identities['skipped'];

        // 1. Create identities
        foreach ($this->allowedIdentities as $identity) {
            $this->configureIdentity($identity);
        }
    }

    private function configureIdentity(string $identity): void
    {
        $this->console->overwrite(sprintf('Configuring identity <comment>%s</comment>', $identity), $this->sectionTitle);

        $this->checkIdentityVerificationStatus($identity);
        $this->checkIdentityDkimConfiguration($identity);
        $this->checkIdentityFromDomain($identity);
        $this->checkIdentityNotificationTopic($identity, 'bounces');
        $this->checkIdentityNotificationTopic($identity, 'complaints');
        $this->checkIdentityNotificationTopic($identity, 'deliveries');

        // Clear the outputs
        $this->console->clear($this->sectionBody);
    }

    private function checkIdentityVerificationStatus(string $identity): void
    {
        // If the identity is not still verified (or doesn't exist at all)...
        $this->console->overwrite('Checking Identity verification status:', $this->sectionBody);
        $log = sprintf('Identity <comment>%s</comment> is already verified: skipping...', $identity);
        if (false === $this->monitor->liveIdentityIsVerified($identity)) {
            $this->console->overwrite(sprintf('Identity <comment>%s</comment> is not still verified: requesting verification...', $identity), $this->sectionBody);
            // Guess if this is an Email identity or a domain identity
            $verificationToken = null;
            $this->monitor->getIdentityGuesser()->isEmailIdentity($identity)
                ? $this->sesManager->verifyEmailIdentity($identity)
                : $verificationToken = $this->sesManager->verifyDomainIdentity($identity);

            // Add the action to take
            $this->monitor->getIdentityGuesser()->isEmailIdentity($identity)
                ? $this->addActionToTake(
                $identity,
                sprintf("A request to verify the email identity <comment>%s</comment> was just sent from amazon to the email address: check the email's inbox and click the confirmation link.", $identity)
            )
                : $this->addActionToTake(
                $identity,
                sprintf(
                    "A request to verify the domain identity <comment>%s</comment> was just sent to amazon.\n"
                    . "You now need to add a TXT record to the DNS settings of your domain.\n"
                    . "Here the details:\n\n"
                    . "Name: <comment>_amazonses.%s</comment>\n"
                    . "Type: <comment>TXT</comment>\n"
                    . "Value: <comment>%s</comment>\n\n"
                    . 'NOTE: If your DNS provider does not allow underscores in record names, you can omit "_amazonses" from the record name.'
                    . "To help you easily identify this record within your domain's DNS settings, you can optionally prefix the record value with \"amazonses\"."
                    . "As Amazon SES searches for the TXT record, the domain's verification status is \"Pending\".\n"
                    . "When Amazon SES detects the record, the domain's verification status changes to \"Success\".\n"
                    . "If Amazon SES is unable to detect the record within 72 hours, the domain's verification status changes to \"Failed\".\n"
                    . 'In that case, if you still want to verify the domain, you must restart the verification process from the beginning.',
                    $identity,
                    $identity,
                    $verificationToken
                )
            );

            $log = sprintf('Verification requested for identity <comment>%s</comment>', $identity);
        }

        $this->console->overwrite($log, $this->sectionBody);
    }

    private function checkIdentityDkimConfiguration(string $identity): void
    {
        // (Requires a verified identity) If the remote configuration is different than the local configuration...
        $this->console->overwrite('Checking DKIM configuration...', $this->sectionBody);
        $log = sprintf('Identity <comment>%s</comment> is not still verified: cannot synch dkim enabling', $identity);
        if ($this->monitor->liveIdentityIsVerified($identity)) {
            $log = sprintf('DKIM enabling for identity <comment>%s</comment> is already in synch: skipping...', $identity);
            if (false === $this->monitor->dkimEnabledIsInSync($identity)) {
                // Synch them
                $this->console->overwrite(sprintf('DKIM enabling for identity <comment>%s</comment> is not in synch: synching...', $identity), $this->sectionBody);

                /** @var bool $dkimEnabled */
                $dkimEnabled = $this->monitor->getConfiguredIdentity($identity, 'dkim');

                /** @var array|null $tokens Tokens may be null if DKIM is not enabled or if this is an email Identity and its Domain Identity is not still verified. */
                $tokens = $this->monitor->getLiveIdentity($identity, 'dkim')['tokens'] ?? null;

                // If there are no DKIM tokens, the action to take depends on the kind of this Identity
                if (null === $tokens) {
                    // If this is a Domain identity, we can ask AWS SES to generate DKIM tokens to use to verify it
                    if (true === $this->monitor->getIdentityGuesser()->isDomainIdentity($identity)) {
                        $this->sesManager->configureDkim($identity, $dkimEnabled);
                        $this->addActionToTake($identity, 'The command asked Amazon SES to activate DKIM verification. Amazon is generating DKIM tokens. run again the command "bin/console aws:ses:configure" to get them or check the AWS SES console.');

                        return;
                    }

                    // If this is an email Identity, the absence of tokens means the domain to which it belongs to has not DKIM enabled: it must be enabled
                    $this->addActionToTake($identity, 'This is an Email Identity: to activate DKIM verification you need to first activate it for the domain Identity this email belongs to.');

                    return;
                }

                if (false === $this->monitor->liveIdentityDkimIsVerified($identity)) {
                    $this->addActionToTake(
                        $identity,
                        sprintf(
                            "To enable DKIM signing for identity <comment>%s</comment>, the records below must be entered in your DNS settings.\n"
                            . "AWS will automatically detect the presence of these records, and allow DKIM signing at that time.\n"
                            . 'Note that verification of these settings may take up to 72 hours.'
                            . "\n"
                            . $this->buildDkimDnsString($identity, $tokens[0]) . "\n"
                            . $this->buildDkimDnsString($identity, $tokens[1]) . "\n"
                            . $this->buildDkimDnsString($identity, $tokens[2]) . "\n",
                            $identity
                        )
                    );
                }

                $log = sprintf('DKIM enabling for identity <comment>%s</comment> is now in synch', $identity);
            }
        }

        $this->console->overwrite($log, $this->sectionBody);
    }

    private function checkIdentityFromDomain(string $identity): void
    {
        // If the remote configuration is different than the local configuration...
        $this->console->overwrite('Checking "from domain"...', $this->sectionBody);
        $log = sprintf('The "from domain" of identity <comment>%s</comment> is already in synch: skipping...', $identity);
        if (false === $this->monitor->fromDomainIsInSync($identity)) {
            $this->console->overwrite(sprintf('The "from domain" of identity <comment>%s</comment> is not in synch: synching...', $identity), $this->sectionBody);

            $log = sprintf('The "from domain" of identity <comment>%s</comment> cannot be synched as its domain identity is not verified.', $identity);
            if ($this->monitor->fromDomainCanBeSynched($identity)) {
                /** @var string $domain */
                $domain = $this->monitor->getConfiguredIdentity($identity, 'from_domain');

                /** @var string $onMxFailure */
                $onMxFailure = $this->monitor->getConfiguredIdentity($identity, 'on_mx_failure');

                $this->sesManager->configureFromDomain($identity, $domain, $onMxFailure);
                $log = sprintf('The "from domain" of identity <comment>%s</comment> is now in synch', $identity);
            }
        }

        $this->console->overwrite($log, $this->sectionBody);
    }

    private function checkIdentityNotificationTopic(string $identity, string $type): void
    {
        $this->console->overwrite(sprintf('Checking notification topic for <comment>%s</comment> of identity <comment>%s</comment>...', $type, $identity), $this->sectionBody);
        $log = sprintf('Topic for notification of <comment>%s</comment> of identity <comment>%s</comment> is already set: skipping...', $type, $identity);
        if ($this->monitor->requiresTopicConfiguration($identity, $type)) {
            $this->console->overwrite(sprintf('Topic for notification of <comment>%s</comment> of identity <comment>%s</comment> is not set: scheduling it...', $type, $identity), $this->sectionBody);
            $topicName               = $this->monitor->getConfiguredIdentity($identity, $type)[self::TOPIC];
            $this->scheduledTopics[] = $this->normalizeTopicName($topicName);
            $log                     = sprintf('Topic <comment>%s</comment> for notification of <comment>%s</comment> of identity <comment>%s</comment> scheduled...', $topicName, $type, $identity);
        }

        $this->console->overwrite($log, $this->sectionBody);
    }

    /**
     * Configures the topics scheduled by self::checkIdentityNotificationTopic().
     */
    private function configureTopics(): void
    {
        // 2. Create topics
        $topics = \array_unique($this->scheduledTopics);
        foreach ($topics as $topic) {
            $this->configureTopic($topic);
        }
    }

    private function configureTopic(string $topic): void
    {
        $this->console->overwrite(sprintf('Configuring topic <comment>%s</comment>', $topic), $this->sectionTitle);

        if ($this->monitor->liveTopicExists($this->normalizeTopicName($topic))) {
            $this->console->overwrite(sprintf('Topic <comment>%s</comment> already exists: skipping...', $topic), $this->sectionBody);
            $this->scheduledTopics[$topic] = $this->monitor->getLiveTopic($topic)['arn'];

            return;
        }

        $this->console->overwrite(sprintf('Creating topic <comment>%s</comment>...', $topic), $this->sectionBody);
        $topicEntity                   = $this->snsManager->createTopic($topic);
        $this->scheduledTopics[$topic] = $topicEntity->getArn();
        $this->entityManager->persist($topicEntity);
        $this->console->overwrite(sprintf('Topic <comment>%s</comment> created: <comment>%s</comment>', $topic, $topicEntity->getArn()), $this->sectionBody);

        $this->console->clear($this->sectionTitle);
    }

    /**
     * Configures the subscriptions to topics of Identities.
     */
    private function configureSubscriptions(): void
    {
        // 3. Setting topics in identities
        foreach ($this->allowedIdentities as $identity) {
            $this->configureSubscription($identity);
        }
    }

    private function configureSubscription(string $identity): void
    {
        $this->console->overwrite(sprintf('Configuring subscriptions of identity <comment>%s</comment>', $identity), $this->sectionTitle);

        $this->subscribeIdentityToTopic($identity, SnsTypes::MESSAGE_TYPE_BOUNCE);
        $this->subscribeIdentityToTopic($identity, SnsTypes::MESSAGE_TYPE_COMPLAINT);
        $this->subscribeIdentityToTopic($identity, SnsTypes::MESSAGE_TYPE_DELIVERY);

        /** @var bool $configuredFeedbackForwarding */
        $configuredFeedbackForwarding = $this->monitor->getConfiguredIdentity($identity, 'feedback_forwarding');

        /** @var bool|null $liveFeedbackForwarding */
        $liveFeedbackForwarding = $this->monitor->getLiveIdentity($identity, 'notifications')['forwarding_enabled'] ?? null;

        if ($configuredFeedbackForwarding !== $liveFeedbackForwarding) {
            // Synch them
            $this->console->overwrite('Configuring feedback forwarding...', $this->sectionBody);
            $this->sesManager->configureFeedbackForwarding($identity, $configuredFeedbackForwarding);
        }

        $this->console->clear($this->sectionBody);
        $this->console->clear($this->sectionTitle);
    }

    private function subscribeIdentityToTopic(string $identity, string $messageType): void
    {
        static $lastCall = 0;
        switch ($messageType) {
            case SnsTypes::MESSAGE_TYPE_BOUNCE:
                $notificationType = 'bounces';
                $topicName        = $this->getTopicName($identity, $notificationType);

                break;
            case SnsTypes::MESSAGE_TYPE_COMPLAINT:
                $notificationType = 'complaints';
                $topicName        = $this->getTopicName($identity, $notificationType);

                break;
            case SnsTypes::MESSAGE_TYPE_DELIVERY:
                $notificationType = 'deliveries';
                $topicName        = $this->getTopicName($identity, $notificationType);

                break;
            default:
                throw new \RuntimeException('Unrecognized message type. This should never happen: investigate further!');
        }

        $this->console->overwrite(sprintf('Checking subscription to topic <comment>%s</comment> for notifications of <comment>%s</comment>...', $topicName, $notificationType), $this->sectionBody);

        $log = sprintf('Identity <comment>%s</comment> is already subscribed to topic <comment>%s</comment> for notifications of <comment>%s</comment>: skipping', $identity, $topicName, $notificationType);
        if ($this->monitor->identityRequiresTopicSubscription($identity, $notificationType)) {
            $this->console->overwrite(sprintf('Identity <comment>%s</comment> is not yet subscribed to topic <comment>%s</comment> for notifications of <comment>%s</comment>: subscribing...', $identity, $topicName, $notificationType), $this->sectionBody);

            // Wait 1 second to avoid throttling errors
            $elapsed = \microtime(true) - $lastCall;
            while ($elapsed < 0.1000000) {
                $wait = 0.1000000 - $elapsed;
                \usleep((int) $wait * 10000000);
                $elapsed = \microtime(true) - $lastCall;
            }

            $this->sesManager->setTopic($identity, $messageType, $this->scheduledTopics[$topicName]);
            $lastCall = \microtime(true);
            $log      = sprintf('Identity <comment>%s</comment> subscribed to topic <comment>%s</comment> for notifications of <comment>%s</comment>', $identity, $topicName, $notificationType);
        }

        $this->console->overwrite($log, $this->sectionBody);

        $this->console->overwrite(sprintf('Checking endpoint in subscription to topic <comment>%s</comment> for notifications of <comment>%s</comment>...', $topicName, $notificationType), $this->sectionBody);
        $currentEndpoint = $this->snsManager->getEndpointUrl();
        $log             = sprintf('Endpoint <comment>%s</comment> in subscription to topic is correct.', $currentEndpoint);
        if (false === $this->monitor->subscriptionEndpointIsInSynch($this->scheduledTopics[$topicName], $currentEndpoint)) {
            $this->console->overwrite('Endpoint is not in synch: synching it...', $this->sectionBody);
            $this->console->overwrite(sprintf("Subscribing App's endpoint to the topic <comment>%s</comment>", $topicName), $this->sectionBody);
            $this->snsManager->setEndpoint($this->scheduledTopics[$topicName]);
            $log = sprintf('Endpoint set to current <comment>%s</comment>: now in synch.', $currentEndpoint);
        }

        $this->console->overwrite($log, $this->sectionBody);
    }

    private function addActionToTake(string $identity, string $action): void
    {
        $this->actionsToTakeNow[$identity][] = $action;
    }

    private function normalizeTopicName(string $topicName): string
    {
        $topicName = preg_replace('#[^A-Za-z0-9-_]#', '_', $topicName);

        return \strtolower($topicName);
    }

    private function buildDkimDnsString(string $identity, string $token): string
    {
        if ($this->monitor->getIdentityGuesser()->isEmailIdentity($identity)) {
            $parts    = $this->monitor->getIdentityGuesser()->getEmailParts($identity);
            $identity = $parts['domain'];
        }

        return sprintf('Name: <comment>%s._domainkey.%s</comment>; Type: <comment>CNAME</comment>; Value: <comment>%s.dkim.amazonses.com</comment>;', $token, $identity, $token);
    }

    private function getTopicName(string $identity, string $type): string
    {
        $topicName = $this->monitor->getConfiguredIdentity($identity, $type)[self::TOPIC];

        if (Configuration::USE_DOMAIN === $topicName) {
            $parts     = $this->monitor->getIdentityGuesser()->getEmailParts($identity);
            $topicName = $this->monitor->getConfiguredIdentity($parts['domain'], $type)[self::TOPIC];
        }

        return $this->normalizeTopicName($topicName);
    }

    /**
     * If there are actions to take, logs them to the console.
     */
    private function logActionsToTake(): void
    {
        if (false === empty($this->actionsToTakeNow)) {
            $this->ioWriter->warning('There are pending actions.');
            $this->ioWriter->writeln('You have to take the actions listed below, then run again the command <comment>bin/console aws:ses:configure</comment> to complete the configuration.');

            foreach (\array_keys($this->actionsToTakeNow) as $identity) {
                $this->ioWriter->warning(sprintf('Actions to take to complete configuration of identity "%s":', $identity));
                foreach ($this->actionsToTakeNow[$identity] as $action) {
                    $this->ioWriter->writeln($action);
                }
            }

            $this->ioWriter->warning('Please, after you have taken the actions listed above, run again the command "bin/console aws:ses:configure" to complete the configuration!');
        }
    }

    /**
     * If there are skipped identities, logs them to the console.
     */
    private function logSkippedIdentities(): void
    {
        if (false === empty($this->skippedIdentities)) {
            $this->ioWriter->warning('There are skipped entities:');

            foreach ($this->skippedIdentities as $identity) {
                $this->ioWriter->writeln('   ' . $identity);
            }
        }
    }
}
