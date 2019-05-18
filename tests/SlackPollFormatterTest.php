<?php
/**
 * Created by PhpStorm.
 * User: mmu
 * Date: 2019-01-25
 * Time: 20:06.
 */

namespace App\Tests;

use App\Services\SlackPollService\SlackPollFormatter;
use PHPUnit\Framework\TestCase;

class SlackPollFormatterTest extends TestCase
{
    public function testGetSlackEmojiString_ReturnsCorrectEmojiString_WhenGivenValidNumberTwo()
    {
        // Arrange
        $sut = new SlackPollFormatter();
        $expected = ':two:';
        // Act
        $actual = $sut->getSlackEmojiString(2);
        // Assert
        $this->assertEquals($expected, $actual);
    }
}
