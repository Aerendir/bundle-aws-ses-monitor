<?php

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;

use Doctrine\ORM\EntityManagerInterface;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Entity\Topic;

class SnsRegisterTopic extends Command
{

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Register a SNS topic')
             ->setName('aws:sns:register-topic')
             ->addOption('name', null, InputOption::VALUE_REQUIRED, 'Topic name.')
             ->addOption('arn', null, InputOption::VALUE_REQUIRED, 'Topic ARN.');

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getOption('name');
        $arn =  $input->getOption('arn');
        $repository = $this->entityManager->getRepository(Topic::class);

        if ($name && $arn) {
            if (! ($topic = $repository->findOneBy(['name' => $name]))) {
                $topic = new Topic($name, $arn);
            } else {
                $output->warning('Can not update existing topic ' . $name);
                return;
                $topic->setName($name); // needs setters
                $topic->setArn($arn);
            }

            $this->entityManager->persist($topic);
            $this->entityManager->flush();
        }

        $topics = $repository->findAll();
        $table = new Table($output);
        $table
            ->setHeaders(['ID', 'Name', 'Arn'])
            ->setRows(array_map(function($t) {
                return [
                    $t->getId(),
                    $t->getName(),
                    $t->getArn()
                ];
            }, $topics));
        $table->render();
    }
}
