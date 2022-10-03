<?php
namespace Project\Console\Data;
use Project\Blog\Comment\Comment;
use Project\Blog\Post\Post;
use Project\Blog\User\User;
use Project\Repositories\Comment\CommentRepositoryInterface;
use Project\Repositories\Post\PostRepositoryInterface;
use Project\Repositories\User\UserRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PopulateDB extends Command
{
    public function __construct(
        private \Faker\Generator $faker,
        private UserRepositoryInterface $userRepository,
        private PostRepositoryInterface $postRepository,
        private CommentRepositoryInterface $commentRepository,
    ) {
        parent::__construct();
    }
    protected function configure(): void
    {
        $this
        ->setName('fake-data:populate-db')
        ->setDescription('Populates DB with fake data')
        ->addArgument(
            'users',
            InputArgument::REQUIRED,
            'Amount of users to create'
        )
        ->addArgument(
            'posts',
            InputArgument::REQUIRED,
            'Amount of posts to create'
        );
    }
    
    protected function execute(
        InputInterface $input,
        OutputInterface $output,
    ): int {
        $usersCount = $input->getArgument('users');
        $postsCount = $input->getArgument('posts');
        
        $users = [];
        for ($i = 0; $i < $usersCount; $i++) {
            $user = $this->createFakeUser();
            $users[] = $user;
            $output->writeln('User created: ' . $user->getEmail());
        }

        $posts = [];
        foreach ($users as $user) {
            for ($i = 0; $i < $postsCount; $i++) {
                $post = $this->createFakePost($user);
                $posts[] = $post;
                $output->writeln('Post created: ' . $post->getTitle());
            }
        }

        foreach ($posts as $post) {
            foreach($users as $user) {
                $comment = $this->createFakeComment($post, $user);
                $output->writeln('Comment created: ' . $comment->getText());
            }
        }
        return Command::SUCCESS;
    }
    private function createFakeUser(): User
    {
        $user = User::createFrom(
            $this->faker->email,
            $this->faker->password,
            $this->faker->firstName,
            $this->faker->lastName
        );

        $this->userRepository->save($user);
        return $this->userRepository->getByEmail($user->getEmail());
    }
    private function createFakePost(User $author): Post
    {
        $authorId = $author->getId();
        $title = $this->faker->sentence(6, true);
        $text = $this->faker->realText;
        $post = new Post(
            $authorId,
            $title,
            $text
        );

        $this->postRepository->save($post);
        return $this->postRepository->getByData($post);
    }
    private function createFakeComment(Post $post, User $author): Comment
    {
        $comment = new Comment(
            $post->getId(),
            $author->getId(),
            $this->faker->realText
        );

        $this->commentRepository->save($comment);
        return $comment;
    }
}