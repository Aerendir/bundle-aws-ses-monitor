<?php

/*
 * This file is part of the AWS SES Monitor Bundle.
 *
 * @author Adamo Aerendir Crespi <hello@aerendir.me>
 * @author Audrius Karabanovas <audrius@karabanovas.net>
 */

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Tests\Model;

use SerendipityHQ\Bundle\AwsSesMonitorBundle\Model\Bounce;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Model\Complaint;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Model\Delivery;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Model\MailMessage;

/**
 * Tests the MailMessage entity.
 *
 * @author Adamo Aerendir Crespi <hello@aerendir.me>
 */
class MailMessageTest extends \PHPUnit_Framework_TestCase
{
    public function testTopic()
    {
        $test = [
            'messageId'        => 'test-message-id',
            'sentOn'           => $this->createMock(\DateTime::class),
            'sentFrom'         => 'test@example.com',
            'sourceArn'        => 'test-source-arn',
            'sendingAccountId' => 'test-sending-account-id',
            'headers'          => 'test-headers',
            'commonHeaders'    => 'test-common-headers'
        ];

        $mockBounce    = $this->createMock(Bounce::class);
        $mockComplaint = $this->createMock(Complaint::class);
        $mockDelivery  = $this->createMock(Delivery::class);

        $resource = new MailMessage();
        $resource->addBounce($mockBounce)
            ->addComplaint($mockComplaint)
            ->addDelivery($mockDelivery)
            ->setMessageId($test['messageId'])
            ->setSentOn($test['sentOn'])
            ->setSentFrom($test['sentFrom'])
            ->setSourceArn($test['sourceArn'])
            ->setSendingAccountId($test['sendingAccountId'])
            ->setHeaders($test['headers'])
            ->setCommonHeaders($test['commonHeaders']);

        $this->assertNull($resource->getId());
        $this->assertSame($test['messageId'], $resource->getMessageId());
        $this->assertSame($test['sentOn'], $resource->getSentOn());
        $this->assertSame($test['sentFrom'], $resource->getSentFrom());
        $this->assertSame($test['sourceArn'], $resource->getSourceArn());
        $this->assertSame($test['sendingAccountId'], $resource->getSendingAccountId());
        $this->assertSame($test['headers'], $resource->getHeaders());
        $this->assertSame($test['commonHeaders'], $resource->getCommonHeaders());
        $this->assertSame(1, $resource->getBounces()->count());
        $this->assertSame(1, $resource->getComplaints()->count());
        $this->assertSame(1, $resource->getDeliveries()->count());
        $this->assertSame($mockBounce, $resource->getBounces()->first());
        $this->assertSame($mockComplaint, $resource->getComplaints()->first());
        $this->assertSame($mockDelivery, $resource->getDeliveries()->first());
    }
}
