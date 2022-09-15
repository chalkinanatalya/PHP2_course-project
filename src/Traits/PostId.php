<?php

namespace Project\Traits;

trait PostId
{
    private ?int $postId = null;

    public function getPostId(): ?int
    {
        return $this->postId;
    }

    public function setPostId(?int $postId): self
    {
        $this->postId = $postId;

        return $this;
    }
}