<?php
/**
 * Created by PhpStorm.
 * User: mmu
 * Date: 2019-01-25
 * Time: 19:47
 */

namespace App\Services\SlackPollService;



use App\Entity\Poll;
use Symfony\Component\HttpFoundation\JsonResponse;

interface SlackPollFormatterInterface
{
    function formatPoll(Poll $poll): JsonResponse;

    function formatUpdatedPoll(array $original, Poll $poll) : JsonResponse;
}