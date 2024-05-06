<?php

namespace App\Controller;

use App\Entity\Film;
use App\Entity\FilmCategory;
use App\Repository\CategoryRepository;
use App\Repository\FilmRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\FilmCategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api')]
class FilmController extends AbstractController
{
    private $filmRepo;
    private $em;
    private $filmCategoryRepo;

    public function __construct(FilmRepository $filmRepo, EntityManagerInterface $em, FilmCategoryRepository $filmCategoryRepo, private CategoryRepository $categRepo)
    {
        $this->filmRepo = $filmRepo;
        $this->em = $em;
        $this->filmCategoryRepo = $filmCategoryRepo;
    }

    #[Route('/films', name: 'get_all_films', methods: ['GET'])]
    public function getAllFilms(SerializerInterface $serializer): JsonResponse
    {
        $films = $this->filmRepo->findFilmsAndCategs();
        // $films = $this->filmRepo->findAll();
        $jsonGroups = $serializer->serialize($films, 'json', ['groups' => 'film']);
        return $this->json($jsonGroups);
    }

    #[Route('/films/{id}', name: 'get_one_film', methods: ['GET'])]
    public function getOneFilm(int $id, SerializerInterface $serializer): JsonResponse
    {
        $film = $this->filmRepo->findFilmAndCategs($id);

        $jsonGroups = $serializer->serialize($film, 'json', ['groups' => 'film']);
        return $this->json($jsonGroups);
    }

    #[Route('/films/category/{id}', name: 'get_films_by_categ', methods: ['GET'])]
    public function getFilmsByCateg(int $id, SerializerInterface $serializer): JsonResponse
    {
        // $films = $this->filmCategoryRepo->findFilmByCateg($id);
        $films = $this->filmRepo->findFilmByCateg($id);

        $jsonGroups = $serializer->serialize($films, 'json', ['groups' => 'film']);
        return $this->json($jsonGroups);
    }

    #[Route('/films/{id}', name: 'delete_film', methods: ['DELETE'])]
    public function deleteFilm(int $id): JsonResponse
    {
        $film = $this->filmRepo->find($id);

        $this->em->remove($film);
        $this->em->flush();

        return new JsonResponse(null, 204);
    }

    #[Route('/films', name: 'new_film', methods: ['POST'])]
    public function newFilm(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $film = new Film();

        $film->setTitle($data['title'])
            ->setSummary($data['summary'])
            ->setReleaseYear($data['releaseYear'])
            ->setRealisator($data['realisator']);

        foreach ($data['filmCategories'] as $categData) {
            $categ = $this->categRepo->find($categData['id']);
            $filmCateg = new FilmCategory();
            $filmCateg->setFilm($film)
                ->setCategory($categ);
            $film->addFilmCategory($filmCateg);

            $this->em->persist($filmCateg);
        }

        $this->em->persist($film);
        $this->em->flush();

        return new JsonResponse(null, 204);
    }

    #[Route('/films/{id}', name: 'update_film', methods: ['PUT'])]
    public function updateFilm(int $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $film = $this->filmRepo->find($id);

        $film->setTitle($data['title'])
            ->setSummary($data['summary'])
            ->setReleaseYear($data['releaseYear'])
            ->setRealisator($data['realisator']);

        foreach ($film->getFilmCategories() as $categ) {
            $film->removeFilmCategory($categ);
        }

        foreach ($data['filmCategories'] as $categData) {
            $categ = $this->categRepo->find($categData['id']);
            $filmCateg = new FilmCategory();
            $filmCateg->setFilm($film)
                ->setCategory($categ);
            $film->addFilmCategory($filmCateg);

            $this->em->persist($filmCateg);
        }

        $this->em->flush();


        return new JsonResponse(null, 204);
    }
}
