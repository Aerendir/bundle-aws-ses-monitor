<?php

/*
 * This file is part of the SHQAwsSesBundle.
 *
 * Copyright Adamo Aerendir Crespi 2015 - 2017.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    Adamo Aerendir Crespi <hello@aerendir.me>
 * @copyright Copyright (C) 2015 - 2017 Aerendir. All rights reserved.
 * @license   MIT License.
 */

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Helper;

use SerendipityHQ\Bundle\AwsSesMonitorBundle\Manager\SesManager;
use Symfony\Component\Console\Question\ChoiceQuestion;

/**
 * Helper methods for the SubscribeCommand.
 */
class SubscribeHelper
{
    /** @var SesManager $sesManager */
    private $sesManager;

    /**
     * @param SesManager $sesManager
     */
    public function __construct(SesManager $sesManager)
    {
        $this->sesManager = $sesManager;
    }

    /**
     * @return ChoiceQuestion
     */
    public function createIdentityQuestion(): ChoiceQuestion
    {
        $result     = $this->sesManager->listIdentities();
        $identities = $result->get('Identities');
        $question   = (new ChoiceQuestion(
            'Please select identities to hook to: (comma separated numbers, default: all)',
            $identities,
            implode(',', range(0, count($identities) - 1, 1))
        ))->setMultiselect(true);

        return $question;
    }
}
