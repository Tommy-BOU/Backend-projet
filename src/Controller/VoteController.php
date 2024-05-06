<?php

namespace App\Controller;

use App\Entity\Film;
use App\Entity\Comment;
use App\Entity\FilmVote;
use App\Entity\CommentVote;
use App\Repository\FilmVoteRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CommentVoteRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api')]
class VoteController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em, private FilmVoteRepository $filmVoteRepo, private CommentVoteRepository $commentVoteRepo)
    {
    }

    #[Route('/vote/film', name: 'app_vote_film', methods: ['POST'])]
    public function voteFilm(Request $request, SerializerInterface $serializer): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $filmVote = $this->filmVoteRepo->findOneBy(['film' => $data["id"], 'user' => $this->getUser()]);

        if (!$filmVote) {
            $user = $this->getUser();
            $filmVote = new FilmVote();
            $filmVote->setFilm($this->em->getReference(Film::class, $data["id"]));
            $filmVote->setUser($user);
            $filmVote->setValue($data["value"]);
            $this->em->persist($filmVote);
        } else if ($data["value"] == $filmVote->getValue()) {
            $this->em->remove($filmVote);
        } else {
            $filmVote->setValue($data["value"]);
        }

        $this->em->flush();

        $filmVote = $serializer->serialize($filmVote, 'json', ['groups' => 'vote']);
        return $this->json($filmVote);
    }

    #[Route('/vote/comment', name: 'app_vote_comment', methods: ['POST'])]
    public function voteComment(Request $request, SerializerInterface $serializer): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $commentVote = $this->commentVoteRepo->findOneBy(['comment' => $data["id"], 'user' => $this->getUser()]);

        if (!$commentVote) {
            $user = $this->getUser();
            $commentVote = new CommentVote();
            $commentVote->setcomment($this->em->getReference(Comment::class, $data["id"]));
            $commentVote->setUser($user);
            $commentVote->setValue($data["value"]);
            $this->em->persist($commentVote);
        } else if ($data["value"] == $commentVote->getValue()) {
            $this->em->remove($commentVote);
        } else {
            $commentVote->setValue($data["value"]);
        }

        $this->em->flush();

        $commentVote = $serializer->serialize($commentVote, 'json', ['groups' => 'vote']);
        return $this->json($commentVote);
    }

    #[Route('/vote/film/{id}', name: 'get_film_votes', methods: ['GET'])]
    public function getFilmVotes(int $id, SerializerInterface $serializer): JsonResponse
    {
        $votes = $this->filmVoteRepo->findBy(['film' => $id]);

        $jsonGroups = $serializer->serialize($votes, 'json', ['groups' => 'vote']);

        return $this->json($jsonGroups);
    }

    #[Route('/vote/comment/{id}', name: 'get_comment_votes', methods: ['GET'])]
    public function getCommentVotes(int $id, SerializerInterface $serializer): JsonResponse
    {
        $votes = $this->commentVoteRepo->findBy(['comment' => $id]);

        $jsonGroups = $serializer->serialize($votes, 'json', ['groups' => 'vote']);

        return $this->json($jsonGroups);
    }
    // #[Route('/vote', name: 'app_vote')]
    // public function index(): JsonResponse
    // {
    //     return $this->json([
    //         'message' => 'Welcome to your new controller!',
    //         'path' => 'src/Controller/VoteController.php',
    //     ]);
    // }
}
