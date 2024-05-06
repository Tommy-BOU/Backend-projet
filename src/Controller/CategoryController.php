<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api')]
class CategoryController extends AbstractController
{
    public function __construct(private CategoryRepository $catRepo, private EntityManagerInterface $em)
    {
    }
    #[Route('/categories', name: 'get_all_categs', methods: ['GET'])]
    public function getAllCategories(SerializerInterface $serializer): JsonResponse
    {
        $categories = $this->catRepo->findAll();
        $jsonGroups = $serializer->serialize($categories, 'json', ['groups' => 'category']);

        return $this->json($jsonGroups);
    }

    #[Route('/categories/{id}', name: 'get_one_categ', methods: ['GET'])]
    public function getOneCategory(int $id, SerializerInterface $serializer): JsonResponse
    {
        $category = $this->catRepo->find($id);
        $jsonGroups = $serializer->serialize($category, 'json', ['groups' => 'category']);

        return $this->json($jsonGroups);
    }

    #[Route('/categories', name: 'add_categ', methods: ['POST'])]
    public function addCategory(Request $request): JsonResponse
    {

        $data = json_decode($request->getContent(), true);

        $category = new Category();

        $category->setLabel($data['label']);

        $this->em->persist($category);

        $this->em->flush();


        return new JsonResponse(null, 204);
    }

    #[Route('/categories/{id}', name: 'update_categ', methods: ['PUT'])]
    public function updateCategory(int $id, Request $request): JsonResponse
    {


        $category = $this->catRepo->find($id);

        $data = json_decode($request->getContent(), true);

        $category->setLabel($data['label']);

        $this->em->flush();


        return new JsonResponse(null, 204);
    }

    #[Route('/categories/{id}', name: 'delete_categ', methods: ['DELETE'])]
    public function deleteCategory(int $id): JsonResponse
    {
        $category = $this->catRepo->find($id);

        $this->em->remove($category);
        $this->em->flush();

        return new JsonResponse(null, 204);
    }
}
