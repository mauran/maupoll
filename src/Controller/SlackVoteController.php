<?php

namespace App\Controller;


use App\Services\SlackPollService\SlackPollFormatterInterface;
use App\Services\SlackPollService\SlackPollServiceInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Predis\Client;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class SlackVoteController
 * @package App\Controller
 * @Route("/slack")
 */
class SlackVoteController extends AbstractController
{
    /**
     * @Route("/command", name="slack_command")
     */
    public function command(Request $request, SlackPollServiceInterface $slackPollService, SlackPollFormatterInterface $formatter, ObjectManager $objectManager)
    {
        $poll = $slackPollService->createPoll('HEJ', ['hest', 'hest', 'pis']);
        $objectManager->persist($poll);
        $objectManager->flush();
        return $formatter->formatPoll($poll);
    }

    /**
     * @Route("/action")
     */
    public function action(Request $request, SlackPollServiceInterface $slackPollService, SlackPollFormatterInterface $formatter)
    {
        $slackPollService->votePoll('kdo', 'ddd', 'ddd');
        $poll = $slackPollService->getPoll('pollid');
        return $formatter->formatPoll($poll);
    }
}
