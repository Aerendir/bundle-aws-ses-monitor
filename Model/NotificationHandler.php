<?php
namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Model;

use Aws\Sns\MessageValidator\Message;
use Aws\Sns\MessageValidator\MessageValidator;
use Doctrine\Common\Persistence\ObjectRepository;
use Symfony\Component\HttpFoundation\Request;

class NotificationHandler implements BouncerHandlerInterface
{
    const HEADER_TYPE = 'Notification';
    const MESSAGE_TYPE_SUBSCRIPTION_SUCCESS = 'AmazonSnsSubscriptionSucceeded';
    const MESSAGE_TYPE_BOUNCE = 'Bounce';
    /**
     * @var BounceRepositoryInterface
     */
    private $repo;

    /**
     * @param ObjectRepository $repo
     */
    public function __construct(ObjectRepository $repo)
    {
        $this->repo = $repo;
    }

    /**
     * @param Request $request
     * @return int
     */
    public function handleRequest(Request $request)
    {
        if (!$request->isMethod('POST')) {
            return 405;
        }

        try {
            $data = json_decode($request->getContent(), true);
            $message = Message::fromArray($data);
            $validator = new MessageValidator();
            $validator->validate($message);
        } catch (\Exception $e) {
            return 404; // not valid message, we return 404
        }

        if (isset($data['Message'])) {
            $message = json_decode($data['Message'], true);
            if (!is_null($message)) {

                if (isset($message['notificationType']) && $message['notificationType'] == self::MESSAGE_TYPE_SUBSCRIPTION_SUCCESS) {
                    return 200;
                }

                if (isset($message['notificationType']) && $message['notificationType'] == self::MESSAGE_TYPE_BOUNCE)
                {
                    foreach ($message['bounce']['bouncedRecipients'] as $bounceRecipient) {
                        $email = $bounceRecipient['emailAddress'];
                        $bounce = $this->repo->findBounceByEmail($email);
                        if ($bounce instanceof Bounce) {
                            $bounce->incrementBounceCounter();
                            $bounce->setLastTimeBounce(new \DateTime());
                            $bounce->setPermanent(($message['bounce']['bounceType']=='Permanent'));
                        } else {
                            $bounce = new Bounce($email, new \DateTime(), 1, ($message['bounce']['bounceType']=='Permanent'));
                        }

                        $this->repo->save($bounce);
                    }

                    return 200;
                }
            }
        }

        return 404;
    }
}
