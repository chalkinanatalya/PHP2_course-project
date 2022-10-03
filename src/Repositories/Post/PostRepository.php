<?php

namespace Project\Repositories\Post;

use PDOException;
use Project\Connection\ConnectorInterface;
use Project\Connection\DataBaseConnector;
use Project\Exceptions\PostNotFoundException;
use Project\Blog\Post\Post;
use PDO;
use Psr\Log\LoggerInterface;

class PostRepository implements PostRepositoryInterface
{
    private PDO $connection;

    public function __construct(
        private LoggerInterface $logger,
        private ?ConnectorInterface $connector = null,
    )
    {
        $this->connector = $connector ?? new DataBaseConnector();
        $this->connection = $this->connector->getConnection();
    }

    public function save(Post $post): void
    {
        $authorId = $post->getAuthorId();
        $title = $post->getTitle();

        $statement = $this->connection->prepare(
            'INSERT INTO post (author_id, title, text)
            VALUES (:author_id, :title, :text)'
        );
        $statement->execute(
            [
                ':author_id' => $authorId,
                ':title' => $title,
                ':text' => $post->getText(),
            ]
        );

        $this->logger->info("Post $authorId, $title created");
    }

    public function get(int $id): Post
    {
        $statement = $this->connection->prepare(
            "select * from post where id = :postId"
        );

        $statement->execute([
            'postId' => $id
        ]);

        $postObj = $statement->fetch(PDO::FETCH_OBJ);
        if(!$postObj)
        {
            $warning = "Post with id: $id not found";
            $this->logger->warning($warning);
            throw new PostNotFoundException($warning);
        }

        $post = new Post($postObj->author_id, $postObj->title, $postObj->text);
        $post
            ->setId($postObj->id);

        return $post;
    }
    public function getByData(object $post): Post
    {
        $statement = $this->connection->prepare(
            "SELECT * FROM post WHERE author_id = :author_id AND title = :title AND text = :text"
        );
        $statement->execute([
            'author_id' => $post->getAuthorId(),
            'title' => $post->getTitle(),
            'text' => $post->getText(),
        ]);

        $postObj = $statement->fetch(PDO::FETCH_OBJ);

        if(!$postObj)
        {
            $warning = "Post with not found";
            $this->logger->warning($warning);
            throw new PostNotFoundException($warning);
        }

        return $this->mapUser($postObj);

    }
    public function mapUser(object $postObj): Post
    {
        $post = new Post($postObj->author_id, $postObj->title, $postObj->text);
        $post->setId($postObj->id);
        return $post;
    }

    /**
     * @throws PostNotFoundException
     * @throws \Exception
     */
    public function delete( $id): void
    {
        try {
            $statement = $this->connection->prepare('DELETE FROM post WHERE id = ?');
            $statement->execute([(string)$id]);
        } catch (PDOException $e) {
            throw new PostNotFoundException(
                $e->getMessage(), (int)$e->getCode(), $e
            );
        }
    }
    
}