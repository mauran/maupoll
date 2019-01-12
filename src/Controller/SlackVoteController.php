<?php

namespace App\Controller;


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
    const ATTACHMENT_COLOR = '#3498db';

    /**
     * @Route("/command", name="slack_command")
     */
    public function command(Request $request, Client $redis)
    {
        $text = $request->request->get('text');
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

        $id = uniqid('poll');

        $poll = [
            'id' => $id,
            'question' => $question,
            'creator_name' => $request->request->get('user_name'),
            'creator_id' => $request->request->get('user_id')
        ];

        $attachments = [
            "callback_id" => $id,
            "title" => "",
            "attachment_type" => "default",
            "fallback" => "Your slack client does not support voting",
            "actions" => [],
            "color" => self::ATTACHMENT_COLOR
        ];

        $message = "*" . $question . "*" . PHP_EOL;

        foreach ($answers as $key =>  $answer) {
            $key++;
            $answer = str_replace('"', '', $answer);

            $poll['answers'][$key] = [
                'text' => $answer,
                'voters' => []
            ];

            $attachments['actions'][] = [
                "name" => "vote-option",
                "text" => $this->getEmojiForNumber($key),
                "type" => "button",
                "value" => $key,
                "color" => self::ATTACHMENT_COLOR
            ];
            $message .= $this->getEmojiForNumber($key) . ' ' . $answer . " `0`" .PHP_EOL . PHP_EOL;
        }

        $redis->set($id, json_encode($poll));
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
    public function action(Request $request, Client $redis)
    {
        $payload = json_decode($request->request->get('payload'), true);
        $pollId = $payload['callback_id'];
        $poll = json_decode($redis->get($pollId), true);
        $vote = $payload['actions'][0]['value'];
        $userId = $payload['user']['id'];

        $redis->set($pollId, json_encode($poll));
        $originalMessage = $payload['original_message'];
        $message = "*" . $poll['question'] . "*" . PHP_EOL;

        foreach ($poll['answers'] as $key => $option) {
            $poll['answers'][$key]['voters'] = array_diff($option['voters'], [$userId]);
        }
        $poll['answers'][$vote]['voters'][] = $userId;
        /** @var PollOption $option */
        foreach ($poll['answers'] as $key =>  $option) {
            $users = '';
            foreach ($option['voters'] as $voter) {
                $users .= '<@' . $voter . '>';
            }
            $message .= $this->getEmojiForNumber($key) . ' ' . $option['text'] . ' `' . count($option['voters']) .'` '. PHP_EOL.  $users . PHP_EOL;
        }

        $originalMessage['attachments'][0]['text'] = $message;
        $originalMessage['attachments'][0]['fallback'] = $message;
        return new JsonResponse($originalMessage);
    }

    private function getEmojiForNumber($index) {
        $emojis = [':zero:', ':one:', ':two:', ':three:', ':four:', ':five:', ':six:', ':seven:', ':eight:', ':nine:'];
        $numbers = range(0, 9);
        return str_replace($numbers, $emojis, $index);
    }
}
