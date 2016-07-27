<?php

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Repository;

use SerendipityHQ\Bundle\AwsSesMonitorBundle\Model\Mail;

/**
 * Repository to manage a Mail.
 */
interface MailRepositoryInterface
{
    /**
     * @param string $messageId
     *
     * @return Mail|null|object
     */
    public function findOneByMessageId($messageId);
}
