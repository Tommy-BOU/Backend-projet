<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Repository\FilmRepository;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api')]
class CommentController extends AbstractController
{

    public function __construct(private EntityManagerInterface $em, private FilmRepository $filmRepo, private CommentRepository $commentRepo)
    {
    }

    #[Route('/comment', name: 'get_all_comments', methods: ['GET'])]
    public function getAllComments(SerializerInterface $serializer): JsonResponse
    {
        $comments = $this->commentRepo->findAll();
        $jsonComments = $serializer->serialize($comments, 'json', ['groups' => 'comment']);

        return $this->json($jsonComments);
    }

    #[Route('/comment', name: 'post_comment', methods: ['POST'])]
    public function postComment(Request $request, SerializerInterface $serializer): JsonResponse
    {
        // Récupérer les données envoyées par le front sous forme de tableau associatif (true)
        $data = json_decode($request->getContent(), true);
        $film = $this->filmRepo->find($data['filmId']);
        // Créer une nouvelle instance de Posts avec les données envoyés par le front  
        $comment = new Comment();
        $comment->setContent($data['content'])
        ->setDatePosted(new \DateTime())
        ->setUser($this->getUser())
        ->setFilm($film);

        $this->em->persist($comment);
        $this->em->flush();

        $comment = $serializer->serialize($comment, 'json', ['groups' => 'comment']);
        return $this->json($comment);
    }

    #[Route('/comment/film/{id}', name: 'get_film_comments', methods: ['GET'])]
    public function getCommentsByFilm(int $id, SerializerInterface $serializer): JsonResponse
    {
        $comments = $this->commentRepo->findCommentsByFilm($id);

        $jsonGroups = $serializer->serialize($comments, 'json', ['groups' => 'comment']);

        return $this->json($jsonGroups);
    }

    #[Route('/comment/{id}', name: 'modify_comment', methods: ['PUT'])]
    public function modifyComment(int $id, Request $request): JsonResponse
    {
        $comment = $this->commentRepo->find($id);

        $data = json_decode($request->getContent(), true);

        $comment->setContent($data['content']);

        $this->em->flush();


        return new JsonResponse(null, 204);
    }

    #[Route('/comment/{id}', name: 'delete_comment', methods: ['DELETE'])]
    public function deleteComment(int $id): JsonResponse
    {
        $comment = $this->commentRepo->find($id);

        $this->em->remove($comment);
        $this->em->flush();

        return new JsonResponse(null, 204);
    }
}
