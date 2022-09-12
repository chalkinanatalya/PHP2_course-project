<?php

namespace Project\Repositories\Comment;

use Project\Connection\ConnectorInterface;
use Project\Connection\DataBaseConnector;
use Project\Exceptions\CommentNotFoundException;
use Project\Comment\Comment;
use PDO;


class CommentRepository implements CommentRepositoryInterface
{
    private PDO $connection;

    public function __construct(private ?ConnectorInterface $connector = null)
    {
        $this->connector = $connector ?? new DataBaseConnector();
        $this->connection = $this->connector->getConnection();
    }

    public function save(Comment $comment): void
    {
        $statement = $this->connection->prepare(
            'INSERT INTO post (post_id, author_id, text)
            VALUES (:post_id, :author_id, :text)'
        );

        $statement->execute(
            [
                ':post_id' => $comment->getPostId(),
                ':author_id' => $comment->getAuthorId(),
                ':text' => $comment->getText(),
            ]
        );
    }

    /**
     * @throws CommentNotFoundException
     * @throws \Exception
     */
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
            throw new CommentNotFoundException("Comment with id: $id not found");
        }

        $comment = new Comment($commentObj->post_id, $commentObj->author_id, $commentObj->text);

        $comment->setId($commentObj->id);

        return $comment;

    }
}