<?php

namespace Project\Repositories\Post;

use Project\Connection\ConnectorInterface;
use Project\Connection\DataBaseConnector;
use Project\Exceptions\PostNotFoundException;
use Project\Blog\Post;
use PDO;

class PostRepository implements PostRepositoryInterface
{
    private PDO $connection;

    public function __construct(private ?ConnectorInterface $connector = null)
    {
        $this->connector = $connector ?? new DataBaseConnector();
        $this->connection = $this->connector->getConnection();
    }

    public function save(Post $post): void
    {
        $statement = $this->connection->prepare(
            'INSERT INTO post (author_id, title, text)
            VALUES (:author_id, :title, :text)'
        );

        $statement->execute(
            [
                ':author_id' => $post->getAuthorId(),
                ':title' => $post->getTitle(),
                ':text' => $post->getText(),
            ]
        );
    }
    /**
     * @throws PostNotFoundException
     * @throws \Exception
     */
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
            throw new PostNotFoundException("Post with id: $id not found");
        }

        $post = new Post($postObj->author_id, $postObj->title, $postObj->text);

        $post
            ->setId($postObj->id);

        return $post;
    }
    /**
     * @throws PostNotFoundException
     * @throws \Exception
     */
    public function delete(int $id): void
    {
        $statement = $this->connection->prepare(
            "DELETE FROM post WHERE id = :postId"
        );

        $statement->execute([
            'postId' => $id
        ]);

        if (!$statement->execute())
        {
            throw new PostNotFoundException("Post with id: $id not found");
        }
    }
}