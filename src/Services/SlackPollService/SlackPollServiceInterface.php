<?php
/**
 * Created by PhpStorm.
 * User: mmu
 * Date: 2019-01-25
 * Time: 19:34
 */

namespace App\Services\SlackPollService;


use App\Entity\Poll;

interface SlackPollServiceInterface
{
    function createPoll($question, array $answers) : Poll;

    function votePoll($pollId, $answerId, $userId) : void;

    function getPoll($pollId) : Poll;
}