<?php

namespace Project\Repositories\Like;

use Project\Connection\ConnectorInterface;
use Project\Connection\DataBaseConnector;
use Project\Exceptions\LikeNotFoundException;
use Project\Blog\Like\Like;
use PDO;


class LikeRepository implements LikeRepositoryInterface
{
    private PDO $connection;

    public function __construct(private ?ConnectorInterface $connector = null)
    {
        $this->connector = $connector ?? new DataBaseConnector();
        $this->connection = $this->connector->getConnection();
    }

    public function save(Like $like): void
    {
        $statement = $this->connection->prepare(
            'INSERT INTO like (post_id, author_id)
            VALUES (:post_id, :author_id)'
        );

        $statement->execute(
            [
                ':post_id' => $like->getPostId(),
                ':author_id' => $like->getAuthorId(),
            ]
        );
    }

    /**
     * @throws LikeNotFoundException
     * @throws \Exception
     */
    public function get(int $postId, int $authorId): Like
    {
        $statement = $this->connection->prepare(
            "SELECT * FROM like WHERE post_id = :postId AND author_id = :authorId"
        );

        $statement->execute([
            'postId' => $postId,
            'authorId' => $authorId
        ]);

        $likeObj = $statement->fetch(PDO::FETCH_OBJ);

        if(!$likeObj)
        {
            throw new LikeNotFoundException("Like with post_id: $postId by author with id: $authorId not found");
        }

        $like = new Like($likeObj->post_id, $likeObj->author_id);

        $like->setId($likeObj->id);

        return $like;
    }
}