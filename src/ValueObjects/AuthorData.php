<?php

namespace Luminar\Component\Mailer\ValueObjects;

class AuthorData
{
    /**
     * @var string $authorName
     */
    protected string $authorName;

    /**
     * @return string
     */
    public function getAuthorName(): string
    {
        return $this->authorName;
    }

    /**
     * @param string $authorName
     */
    public function setAuthorName(string $authorName): void
    {
        $this->authorName = $authorName;
    }
}