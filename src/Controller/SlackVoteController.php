<?php

namespace App\Controller;


use App\Services\SlackPollService\SlackPollFormatterInterface;
use App\Services\SlackPollService\SlackPollServiceInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Monolog\Logger;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
    public function command(Request $request, SlackPollServiceInterface $slackPollService, SlackPollFormatterInterface $formatter, ObjectManager $objectManager, Logger $logger)
    {
        $text = $request->get('text');
        $logger->addAlert($text);
        // Strip nasty mac quotes
        $text = str_replace('“', '', $text);
        $text = str_replace('”', '', $text);

        preg_match_all('/"(?:\\\\.|[^\\\\"])*"|\S+/', $text, $answers);
        $answers = $answers[0];
        if(count($answers) < 3) {
            return new Response("There must be at least 2 answers");
        }

        if(count($answers) > 5) {
            return new Response("Max 5 answers");
        }

        // Remove quotes
        foreach ($answers as $key => $value) {
            $answers[$key] = str_replace('"', '', $value);
        }

        $question = array_shift($answers);

        $poll = $slackPollService->createPoll($question, $answers);
        $objectManager->persist($poll);
        $objectManager->flush();
        return $formatter->formatPoll($poll);
    }

    /**
     * @Route("/action")
     */
    public function action(Request $request, SlackPollServiceInterface $slackPollService, SlackPollFormatterInterface $formatter, ObjectManager $objectManager)
    {
        $payload = json_decode($request->request->get('payload'), true);
        $pollId = $payload['callback_id'];
        $vote = $payload['actions'][0]['value'];
        $userId = $payload['user']['id'];
        $poll = $slackPollService->votePoll($pollId, $vote, $userId);
        $objectManager->persist($poll);
        $objectManager->flush();
        return $formatter->formatPoll($poll);
    }
}
