<?php

namespace App\Controller;

use App\Entity\Poll;
use App\Entity\PollOption;
use App\Entity\PollUserOption;
use Doctrine\Common\Persistence\ObjectManager;
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
    const ATTACHMENT_COLOR = '#3498db';

    /**
     * @Route("/command", name="slack_command")
     */
    public function command(Request $request, ObjectManager $objectManager)
    {

        $username = $request->request->get('user_name');
        $text = $request->request->get('text');
        $question = explode('?', $text);
        if(count($question) === 0) {
            return "There was no question here?";
        }
        $question = $question[0] . '?';
        $answers = str_replace($question, '', $text);
        preg_match_all('/"(?:\\\\.|[^\\\\"])*"|\S+/', $answers, $answers);
        $answers = $answers[0];

        if(count($answers) > 5) {
            return new Response('Max 5 answers allowed');
        }

        $poll = new Poll();
        $poll->setQuestion($question);
        $poll->setCreator($username);
        $objectManager->persist($poll);
        $objectManager->flush();

        $attachments = [
            "callback_id" => $poll->getId(),
            "title" => "",
            "attachment_type" => "default",
            "fallback" => "Your slack client does not support voting",
            "actions" => [],
            "color" => self::ATTACHMENT_COLOR
        ];

        $message = "*" . $poll->getQuestion() . "*" . PHP_EOL;

        foreach ($answers as $key =>  $answer) {
            $key++;
            $pollOption = new PollOption();
            $answer = str_replace('"', '', $answer);
            $pollOption->setAnswer($answer);
            $pollOption->setPoll($poll);
            $objectManager->persist($pollOption);
            $objectManager->flush();

            $attachments['actions'][] = [
                "name" => "vote-option",
                "text" => $this->getEmojiForNumber($key),
                "type" => "button",
                "value" => $pollOption->getId(),
                "color" => self::ATTACHMENT_COLOR
            ];

            $message .= $this->getEmojiForNumber($key) . ' ' . $answer . " `0`" .PHP_EOL . PHP_EOL;
        }

        $message .= PHP_EOL;

        $messageAttachment = [

            'text' => $message,
            'color' => self::ATTACHMENT_COLOR
        ];

        $response = [
            "response_type" => "in_channel",
            "replace_original" => true,
            "attachments" => [$messageAttachment, $attachments],
        ];

        return new JsonResponse($response);
    }

    /**
     * @Route("/action")
     */
    public function action(Request $request, ObjectManager $em)
    {

        $payload = json_decode($request->request->get('payload'), true);
        $pollId = $payload['callback_id'];
        $vote = $payload['actions'][0]['value'];
        $user = $payload['user']['name'];
        /** @var PollOption $option */
        $option = $em->getRepository(PollOption::class)->find($vote);

        /** @var PollUserOption[] $alreadyVoted */

        $removeVoteAction = false;
        $alreadyVoted = $em->getRepository(PollUserOption::class)->getActiveVotesForUser($user, $pollId);
        foreach ($alreadyVoted as $alreadyVote) {
            if($alreadyVote->getPollOptionId()->getId() === (int)$vote) {
                $em->remove($alreadyVote);
                $removeVoteAction = true;
            }
        }

        $em->flush();

        if(!$removeVoteAction) {
            $userOption = new PollUserOption();
            $userOption->setUser($user);
            $userOption->setPollOptionId($option);
            $em->persist($userOption);
            $em->flush();
        }

        $originalMessage = $payload['original_message'];

        /** @var Poll $poll */
        $poll = $em->getRepository(Poll::class)->find($pollId);

        $message = "*" . $poll->getQuestion() . "*" . PHP_EOL;

        /** @var PollOption $option */
        foreach ($poll->getOptions() as $key =>  $option) {
            $key++;
            $users = '';
            $voters = array_map(function(PollUserOption $voter) {
                return $voter->getUser();
            }, $option->getVoters()->toArray());

            foreach ($option->getVoters() as $voter) {
                $users .= $voter->getUser() . ', ';
            }
            $message .= $this->getEmojiForNumber($key) . ' ' . $option->getAnswer() . ' `' . count($option->getVoters()) .'` '. PHP_EOL. implode(', ', $voters) . PHP_EOL;
        }

        $originalMessage['attachments'][0]['text'] = $message;
        $originalMessage['attachments'][0]['fallback'] = $message;
        return new JsonResponse($originalMessage);
    }

    private function getEmojiForNumber($index) {
        $number = [];
        $number[0] = ':zero:';
        $number[1] = ':one:';
        $number[2] = ':two:';
        $number[3] = ':three:';
        $number[4] = ':four:';
        $number[5] = ':five:';
        $number[6] = ':six:';
        $number[7] = ':seven:';
        $number[8] = ':eight:';
        $number[9] = ':nine:';
        return $number[$index];
    }
}
