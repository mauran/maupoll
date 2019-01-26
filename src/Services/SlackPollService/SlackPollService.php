<?php
/**
 * Created by PhpStorm.
 * User: mmu
 * Date: 2019-01-25
 * Time: 19:54
 */

namespace App\Services\SlackPollService;



use App\Entity\Poll;
use App\Repository\PollRepository;
use Ramsey\Uuid\Uuid;


class SlackPollService implements SlackPollServiceInterface
{
    private $repository;


    public function __construct(PollRepository $repository)
    {
        $this->repository = $repository;
    }


    function createPoll($question, array $answers): Poll
    {
        $poll = new Poll();
        $poll->setQuestion($question);
        $pollData = [];
        foreach ($answers as $answer) {
            $id = Uuid::uuid4()->toString();
            $pollData[$id] = ['answer' => $answer, 'participants' => []];
        }
        $poll->setPollData($pollData);
        return $poll;
    }

    function votePoll($pollId, $answerId, $userId): void
    {
        // TODO: Implement votePoll() method.
    }

    function getPoll($pollId): Poll
    {
        // TODO: Implement getPoll() method.
    }
}