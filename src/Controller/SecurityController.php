<?php
namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SecurityController extends AbstractController
{
    private UserPasswordHasherInterface $passwordHasher;

    // Utilisation du bon service ici : UserPasswordHasherInterface
    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Si l'utilisateur est déjà connecté, redirige-le vers une page protégée
        if ($this->getUser()) {
            return $this->redirectToRoute('app_article_index');
        }

        // Récupérer l'erreur de connexion si elle existe
        $error = $authenticationUtils->getLastAuthenticationError();
        // Récupérer le dernier nom d'utilisateur saisi
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        // Cette méthode sera interceptée par Symfony et gérera la déconnexion
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher, UserRepository $userRepository): Response
    {
        // Créer une nouvelle instance de l'utilisateur
        $user = new User();
    
        // Créer le formulaire d'inscription
        $form = $this->createForm(RegistrationFormType::class, $user, [
            'csrf_protection' => false,
        ]);
                $form->handleRequest($request); // Traiter les données envoyées par le formulaire
    
        // Si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer le mot de passe en clair et le hacher
            $hashedPassword = $passwordHasher->hashPassword($user, $user->getPlainPassword());
            $user->setPassword($hashedPassword);
    
            // Récupérer les rôles
            $roles = $form->get('roles')->getData();
            $user->setRoles($roles);
    
            // Enregistrer l'utilisateur dans la base de données
            $userRepository->save($user, true);
    
            // Rediriger vers la page de connexion
            return $this->redirectToRoute('app_login');
        }
    
        // Si le formulaire n'est pas soumis ou n'est pas valide, afficher le formulaire
        return $this->render('security/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
        
}
