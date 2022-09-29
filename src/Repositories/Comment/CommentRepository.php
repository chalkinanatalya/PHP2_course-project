<?php

namespace Project\Repositories\Comment;

use Project\Connection\ConnectorInterface;
use Project\Connection\DataBaseConnector;
use Project\Blog\Comment\Comment;
use PDO;
use Psr\Log\LoggerInterface;
use Project\Exceptions\CommentNotFoundException;

class CommentRepository implements CommentRepositoryInterface
{
    private PDO $connection;

    public function __construct(
        private LoggerInterface $logger,
        private ?ConnectorInterface $connector = null
    )
    {
        $this->connector = $connector ?? new DataBaseConnector();
        $this->connection = $this->connector->getConnection();
    }

    public function save(Comment $comment): void
    {
        $authorId = $comment->getAuthorId();
        $postId = $comment->getPostId();

        $statement = $this->connection->prepare(
            'INSERT INTO comment (post_id, author_id, text)
            VALUES (:post_id, :author_id, :text)'
        );
        $statement->execute(
            [
                ':post_id' => $postId,
                ':author_id' => $authorId,
                ':text' => $comment->getText(),
            ]
        );
        

        $this->logger->info("Comment to $postId by $authorId created");
    }

    public function get(int $id): Comment
    {
        $statement = $this->connection->prepare(
            "select * from comment where id = :commentId"
        );

        $statement->execute([
            'commentId' => $id
        ]);

        $commentObj = $statement->fetch(PDO::FETCH_OBJ);

        if(!$commentObj)
        {
            $warning = "Comment with id: $id not found";
            $this->logger->warning($warning);
            throw new CommentNotFoundException($warning);
        }

        $comment = new Comment($commentObj->post_id, $commentObj->author_id, $commentObj->text);

        $comment->setId($commentObj->id);

        return $comment;

    }
}