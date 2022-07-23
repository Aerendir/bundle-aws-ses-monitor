<?php

declare(strict_types=1);

/*
 * This file is part of the Serendipity HQ Aws Ses Bundle.
 *
 * Copyright (c) Adamo Aerendir Crespi <aerendir@serendipityhq.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Tests\Helper;

use Aws\Sns\Message;
use Aws\Sns\MessageValidator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Helper\MessageHelper;
use Symfony\Component\HttpFoundation\Request;

final class MessageHelperTest extends TestCase
{
    /** @var string */
    private const DATA = '{"Type":"Notification","MessageId":"06639ad4-ee6a-5401-8006-c9173fd8f941","TopicArn":"arn:aws:sns:eu-west-1:159321217982:tbme-dev-ses-bounces-topic","Message":"{\"notificationType\":\"Bounce\",\"bounce\":{\"bounceType\":\"Permanent\",\"bounceSubType\":\"Suppressed\",\"bouncedRecipients\":[{\"emailAddress\":\"suppressionlist@simulator.amazonses.com\",\"action\":\"failed\",\"status\":\"5.1.1\",\"diagnosticCode\":\"Amazon SES has suppressed sending to this address because it has a recent history of bouncing as an invalid address. For more information about how to remove an address from the suppression list, see the Amazon SES Developer Guide: http://docs.aws.amazon.com/ses/latest/DeveloperGuide/remove-from-suppressionlist.html \"}],\"timestamp\":\"2018-07-14T10:24:50.776Z\",\"feedbackId\":\"010201649852581b-c3ef3128-b920-4dfa-aaf9-b9af96e9e0ed-000000\",\"reportingMTA\":\"dns; amazonses.com\"},\"mail\":{\"timestamp\":\"2018-07-14T10:24:48.000Z\",\"source\":\"ciao@trustback.me\",\"sourceArn\":\"arn:aws:ses:eu-west-1:159321217982:identity/trustback.me\",\"sourceIp\":\"93.149.148.51\",\"sendingAccountId\":\"159321217982\",\"messageId\":\"0102016498525570-510337dc-eb7d-4acf-8ac8-07ae4b0417f1-000000\",\"destination\":[\"suppressionlist@simulator.amazonses.com\"]}}","Timestamp":"2018-07-14T10:24:50.794Z","SignatureVersion":"1","Signature":"aIAZOurxPd5k5YvXe1KmXfAgcZjJKFLBCKvstx82vuOj6Fz+t17jIIdW7oHVgzGQpby64kK9i82UcQvBUwT9b3Qgd69IxFcFf5lEMsYmHvQ2ohSuc+Ed1hKWSQUO4sZvrIaIWirgV1lKZ48BIzjjFphEoRvI5l9UHItU36Bj7Kx/I1FNQ6i0l+/bEM6RVGN8xvM/h8bG3o5kbYMhtoAmnUKQ067lV2STIXII/fMnDEqCW5fxfB3TykpNUC4RsrsLqcJlKBEkvPx1e8K6nwNXggC6iTnr3H5cV98dOwK6w/6WDMMcZIDFWXlKXXxdSyIn04N12GhStk67VodLwZ6XAA==","SigningCertURL":"https://sns.eu-west-1.amazonaws.com/SimpleNotificationService-eaea6120e66ea12e88dcd8bcbddca752.pem","UnsubscribeURL":"https://sns.eu-west-1.amazonaws.com/?Action=Unsubscribe&SubscriptionArn=arn:aws:sns:eu-west-1:159321217982:tbme-dev-ses-bounces-topic:1df5a19c-a852-41c1-829c-c4fa528422f9"}';

    /** @var MessageValidator&MockObject $messageValidator */
    private $messageValidator;

    private MessageHelper $messageHelper;

    protected function setUp(): void
    {
        $this->messageValidator = $this->createMock(MessageValidator::class);
        $this->messageHelper    = new MessageHelper($this->messageValidator);
    }

    /**
     * Tests the creation of a Message from the Request.
     */
    public function testMessageHelper(): Message
    {
        $expected = [
            'Type'             => 'Notification',
            'MessageId'        => '06639ad4-ee6a-5401-8006-c9173fd8f941',
            'TopicArn'         => 'arn:aws:sns:eu-west-1:159321217982:tbme-dev-ses-bounces-topic',
            'Message'          => '{"notificationType":"Bounce","bounce":{"bounceType":"Permanent","bounceSubType":"Suppressed","bouncedRecipients":[{"emailAddress":"suppressionlist@simulator.amazonses.com","action":"failed","status":"5.1.1","diagnosticCode":"Amazon SES has suppressed sending to this address because it has a recent history of bouncing as an invalid address. For more information about how to remove an address from the suppression list, see the Amazon SES Developer Guide: http://docs.aws.amazon.com/ses/latest/DeveloperGuide/remove-from-suppressionlist.html "}],"timestamp":"2018-07-14T10:24:50.776Z","feedbackId":"010201649852581b-c3ef3128-b920-4dfa-aaf9-b9af96e9e0ed-000000","reportingMTA":"dns; amazonses.com"},"mail":{"timestamp":"2018-07-14T10:24:48.000Z","source":"ciao@trustback.me","sourceArn":"arn:aws:ses:eu-west-1:159321217982:identity/trustback.me","sourceIp":"93.149.148.51","sendingAccountId":"159321217982","messageId":"0102016498525570-510337dc-eb7d-4acf-8ac8-07ae4b0417f1-000000","destination":["suppressionlist@simulator.amazonses.com"]}}',
            'Timestamp'        => '2018-07-14T10:24:50.794Z',
            'SignatureVersion' => '1',
            'Signature'        => 'aIAZOurxPd5k5YvXe1KmXfAgcZjJKFLBCKvstx82vuOj6Fz+t17jIIdW7oHVgzGQpby64kK9i82UcQvBUwT9b3Qgd69IxFcFf5lEMsYmHvQ2ohSuc+Ed1hKWSQUO4sZvrIaIWirgV1lKZ48BIzjjFphEoRvI5l9UHItU36Bj7Kx/I1FNQ6i0l+/bEM6RVGN8xvM/h8bG3o5kbYMhtoAmnUKQ067lV2STIXII/fMnDEqCW5fxfB3TykpNUC4RsrsLqcJlKBEkvPx1e8K6nwNXggC6iTnr3H5cV98dOwK6w/6WDMMcZIDFWXlKXXxdSyIn04N12GhStk67VodLwZ6XAA==',
            'SigningCertURL'   => 'https://sns.eu-west-1.amazonaws.com/SimpleNotificationService-eaea6120e66ea12e88dcd8bcbddca752.pem',
            'UnsubscribeURL'   => 'https://sns.eu-west-1.amazonaws.com/?Action=Unsubscribe&SubscriptionArn=arn:aws:sns:eu-west-1:159321217982:tbme-dev-ses-bounces-topic:1df5a19c-a852-41c1-829c-c4fa528422f9',
        ];
        $mockRequest = $this->createMock(Request::class);
        $mockRequest->method('getContent')->willReturn(self::DATA);

        $message = $this->messageHelper->buildMessageFromRequest($mockRequest);

        self::assertSame($expected, $message->toArray());

        return $message;
    }

    /**
     * @depends testMessageHelper
     */
    public function testValidateNotification(): void
    {
        $this->messageValidator->method('isValid')->willReturn(true);
        $message = \func_get_arg(0);

        $result = $this->messageHelper->validateNotification($message);

        self::assertTrue($result);
    }

    /**
     * @depends testMessageHelper
     */
    public function testExtractMessageData(): void
    {
        $expected = [
            'notificationType' => 'Bounce',
            'bounce'           => [
                'bounceType'        => 'Permanent',
                'bounceSubType'     => 'Suppressed',
                'bouncedRecipients' => [
                    [
                        'emailAddress'   => 'suppressionlist@simulator.amazonses.com',
                        'action'         => 'failed',
                        'status'         => '5.1.1',
                        'diagnosticCode' => 'Amazon SES has suppressed sending to this address because it has a recent history of bouncing as an invalid address. For more information about how to remove an address from the suppression list, see the Amazon SES Developer Guide: http://docs.aws.amazon.com/ses/latest/DeveloperGuide/remove-from-suppressionlist.html ',
                    ],
                ],
                'timestamp'    => '2018-07-14T10:24:50.776Z',
                'feedbackId'   => '010201649852581b-c3ef3128-b920-4dfa-aaf9-b9af96e9e0ed-000000',
                'reportingMTA' => 'dns; amazonses.com',
            ],
            'mail' => [
                'timestamp'        => '2018-07-14T10:24:48.000Z',
                'source'           => 'ciao@trustback.me',
                'sourceArn'        => 'arn:aws:ses:eu-west-1:159321217982:identity/trustback.me',
                'sourceIp'         => '93.149.148.51',
                'sendingAccountId' => '159321217982',
                'messageId'        => '0102016498525570-510337dc-eb7d-4acf-8ac8-07ae4b0417f1-000000',
                'destination'      => [
                    'suppressionlist@simulator.amazonses.com',
                ],
            ],
        ];
        $message = \func_get_arg(0);

        $result = $this->messageHelper->extractMessageData($message);

        self::assertSame($expected, $result);
    }
}
