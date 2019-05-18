<?php
/**
 * Created by PhpStorm.
 * User: mmu
 * Date: 2019-01-25
 * Time: 19:34.
 */

namespace App\Services\SlackPollService;

use App\Entity\Poll;

interface SlackPollServiceInterface
{
    public function createPoll($question, array $answers) : Poll;

    public function votePoll($pollId, $answerId, $userId) : Poll;
}
