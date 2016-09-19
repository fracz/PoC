<?php
/**
 * Created by PhpStorm.
 * User: szymon
 * Date: 16.09.16
 * Time: 13:15
 */

namespace AppBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use AppBundle\Repository\UserCredentialsRepository;
use AppBundle\Service\UserCredentialsFactory;

class AddUserCredentialsCommand extends Command
{
    /**
     * @var UserCredentialsRepository
     */
    private $userCredentialsRepository;
    /**
     * @var UserCredentialsFactory
     */
    private $userCredentialsFactory;
    /**
     * @var array
     */
    private $questions;

    /**
     * @var array
     */
    private $results;

    /**
     * AddUserCredentialsCommand constructor.
     * @param UserCredentialsRepository $userCredentialsRepository
     * @param UserCredentialsFactory $userCredentialsFactory
     * @param array $questions
     */
    public function __construct(
        UserCredentialsRepository $userCredentialsRepository,
        UserCredentialsFactory $userCredentialsFactory,
        array $questions
    ) {
        parent::__construct(null);

        $this->userCredentialsRepository = $userCredentialsRepository;
        $this->userCredentialsFactory = $userCredentialsFactory;
        $this->questions = $questions;
    }

    protected function configure()
    {
        $this
            ->setName('poc:add-user-credentials')
            ->setDescription('Add user credentials');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');

        foreach ($this->questions as $name => $question) {
            $this->results[$name] = $helper->ask($input, $output, $question);
        }

        $output->write('Podsumowanie', true);
        print_r($this->results);

        $confirmation = new ConfirmationQuestion("Czy chcesz utworzyć konto\n?", false);
        if ($helper->ask($input, $output, $confirmation)) {
            try {
                $userCredentials = $this->userCredentialsFactory->create($this->results['username'], $this->results['password']);
                $this->userCredentialsRepository->add($userCredentials);
                $output->write("<info>Pomyślnie utworzenie konta w systemie</info>");
            } catch (\Exception $exception) {
                print_r($exception->getMessage());
            }
        }
    }
}