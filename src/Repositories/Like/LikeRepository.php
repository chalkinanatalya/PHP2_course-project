<?php

namespace Project\Repositories\Like;

use Project\Connection\ConnectorInterface;
use Project\Connection\DataBaseConnector;
use Project\Blog\Like\Like;
use PDO;
use Psr\Log\LoggerInterface;
use Project\Exceptions\LikeNotFoundException;


class LikeRepository implements LikeRepositoryInterface
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

    public function save(Like $like): void
    {
        $authorId = $like->getAuthorId();
        $postId = $like->getPostId();

        $statement = $this->connection->prepare(
            'INSERT INTO like (post_id, author_id)
            VALUES (:post_id, :author_id)'
        );

        $statement->execute(
            [
                ':post_id' => $postId,
                ':author_id' => $authorId,
            ]
        );

        $this->logger->info("Like to $postId by $authorId created");
    }

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
            $warning = "Like with post_id: $postId by author with id: $authorId not found";
            $this->logger->warning($warning);
            throw new LikeNotFoundException($warning);
        }

        $like = new Like($likeObj->post_id, $likeObj->author_id);

        $like->setId($likeObj->id);

        return $like;
    }
}