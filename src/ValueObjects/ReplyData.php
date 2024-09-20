<?php

namespace Luminar\Component\Mailer\ValueObjects;

class ReplyData
{
    /**
     * @var array $replies
     */
    protected array $replies = [];

    /**
     * @param Reply $reply
     * @return void
     */
    public function addReply(Reply $reply): void
    {
        $this->replies[] = $reply;
    }

    /**
     * @return array
     */
    public function getReplies(): array
    {
        return $this->replies;
    }
}