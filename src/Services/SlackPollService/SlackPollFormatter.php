<?php
/**
 * Created by PhpStorm.
 * User: mmu
 * Date: 2019-01-25
 * Time: 20:13
 */

namespace App\Services\SlackPollService;



use App\Entity\Poll;
use Symfony\Component\HttpFoundation\JsonResponse;

class SlackPollFormatter implements SlackPollFormatterInterface
{

    public function formatPoll(Poll $poll): JsonResponse
    {
        $attachment = [
            "callback_id" => $poll->getUuid(),
            "title" => "",
            "attachment_type" => "default",
            "fallback" => "Your slack client does not support voting",
            "actions" => [],
        ];

        $message = '*' . $poll->getQuestion() . '* ' . PHP_EOL;

        $valueCount = 0;
        foreach ($poll->getPollData() as $key => $value) {
           $valueCount++;
           $count = count($value['participants']);
           $message .= $this->getSlackEmojiString($valueCount) . ' ' . $value['answer'] . " `" . $count ."`" .PHP_EOL . $this->formatParticipants($value['participants']) . PHP_EOL;
        }

        $messageAttachment = [
            'text' => $message,
        ];

        $valueCount = 0;
        foreach ($poll->getPollData() as $key => $value) {
            $valueCount++;
            $attachment['actions'][] = [
                "name" => "vote-option",
                "text" => $this->getSlackEmojiString($valueCount),
                "type" => "button",
                "value" => $key,
            ];
        }

        $response = [
            "response_type" => "in_channel",
            "replace_original" => true,
            "attachments" => [$messageAttachment, $attachment],
        ];

        return new JsonResponse($response);
    }

    public function getSlackEmojiString(int $number) {
        $numbers = [];
        $numbers[0] = ':zero:';
        $numbers[1] = ':one:';
        $numbers[2] = ':two:';
        $numbers[3] = ':three:';
        $numbers[4] = ':four:';
        $numbers[5] = ':five:';
        $numbers[6] = ':six:';
        $numbers[7] = ':seven:';
        $numbers[8] = ':eight:';
        $numbers[9] = ':nine:';
        return $numbers[$number];
    }

    public function formatParticipants(array $participants) : string {
        $participantsList = '';
        foreach ($participants as $participant) {
            $participantsList .= '<@' . $participant . '> ';
        }
        return $participantsList;
    }
}
