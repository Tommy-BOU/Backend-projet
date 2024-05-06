<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api')]
class SecurityController extends AbstractController
{

    private UserPasswordHasherInterface $passwordHasher;
    private EntityManagerInterface $em;
    private UserRepository $userRepo;
    private JWTTokenManagerInterface $JWTManager;

    public function __construct(UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $em, UserRepository $userRepo, JWTTokenManagerInterface $JWTManager)
    {
        $this->passwordHasher = $passwordHasher;
        $this->em = $em;
        $this->userRepo = $userRepo;
        $this->JWTManager = $JWTManager;
    }

    #[Route('/users', name: 'get_all_users', methods: ['GET'])]
    public function getAllUsers(SerializerInterface $serializer): JsonResponse
    {
        $users = $this->userRepo->findAll();
        $jsonUsers = $serializer->serialize($users, 'json', ['groups' => 'user']);

        return $this->json($jsonUsers);
    }


    #[Route('/register', name: 'app_register', methods: ['POST'])]
    public function register(Request $request): JsonResponse
    {
        // Récupérer les données envoyés par l'utilisateur depuis vueJS  
        $data = json_decode($request->getContent(), true);

        // Créer un nouvel utilisateur 
        $user = new User();
        $user->setEmail($data['email']);
        $user->setUsername($data['username']);
        $user->setPassword(
            $this->passwordHasher->hashPassword(
                $user,
                $data['password']
            )
        );
        $user->setRoles(['ROLE_USER']);

        $this->em->persist($user);
        $this->em->flush();

        return $this->json([
            'id' => $user->getId(),
            'username' => $user->getUsername(),
            'email' => $user->getEmail(),
            'password' => $user->getPassword(),
            'role' => $user->getRoles(),
        ], JsonResponse::HTTP_CREATED);
    }


    #[Route('/login', name: 'app_login', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Vérifier si l'utilisateur existe en BDD 
        $user = $this->userRepo->findOneBy(['userName' => $data['userName']]);

        // Comparaison des infos utilisateurs avec les infos de la BDD 
        if (!$user || !$this->passwordHasher->isPasswordValid($user, $data['password'])) {
            return new JsonResponse(['error' => 'Nom d\'utilisateur ou mot de passe incorrect'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        // Créer un JWT pour authentifier le user 
        $token = $this->JWTManager->create($user);

        $res = new JsonResponse([
            'message' => 'Connection réussie'
        ]);

        // Envoyer le JWT dans un Cookie -> C'est plus sécure que de le stocker dans le localStorage car non accéssible via le JS -> se protéger conter les failles XSS(injection de js)
        $res->headers->setCookie(new Cookie('BEARER', $token, time() + 3600, '/', null, true, true));
        return $res;
    }

    #[Route('/logout', name: 'app_logout', methods: ['POST'])]
    public function logout(): JsonResponse
    {
        $res = new JsonResponse([
            'message' => 'Déconnection réussie'
        ]);
        // Supprimer le JWT à la deconnection
        $res->headers->clearCookie('BEARER');
        return $res;
    }


    #[Route('/roles', name: 'get_roles', methods: ['GET'])]
    public function getRole(): JsonResponse
    {
        $user = $this->getUser();
        return $this->json($user->getRoles());
    }

    #[Route('/users/{id}', name: 'modify_user', methods: ['PUT'])]
    public function modifyUser(int $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $user = $this->userRepo->find($id);

        $user->setEmail($data['email'])
        ->setUsername($data['userName'])
        ->setRoles($data['roles']);

        $this->em->flush();


        return new JsonResponse(null, 204);

    }

    #[Route('/users/{id}', name: 'delete_user', methods: ['DELETE'])]
    public function deleteUser(int $id): JsonResponse
    {
        $user = $this->userRepo->find($id);

        $this->em->remove($user);
        $this->em->flush();

        return new JsonResponse(null, 204);
    }
}
