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
        $text = $request->get('text');
        preg_match_all('/"(?:\\\\.|[^\\\\"])*"|\S+/', $text, $answers);
        $answers = $answers[0];
        if(count($answers) < 3) {
            return new Response("There must be at least 1 answer");
        }
        if(count($answers) > 5) {
            return new Response("Max 5 answers");
        }
        $question = array_shift($answers);
        $question = str_replace('"', '', $question);

        $poll = $slackPollService->createPoll($question, $answers);
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
