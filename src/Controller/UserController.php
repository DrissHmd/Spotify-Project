<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class UserController extends AbstractController
{
    #[Route('/', name: 'app_default')]
    public function default(): Response
    {
        return $this->redirectToRoute('app_search_track');
    }

    #[Route('/login', name: 'app_user_login')]
    public function login(AuthenticationUtils $authenticationUtils, Request $request, UserRepository $userRepository, Security $security): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_search_track');
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        $form = $this->createFormBuilder()->add(
            'email',
            TextType::class,
        )->add(
            'password',
            PasswordType::class,
        )->setMethod('GET')->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $user = $userRepository->findOneByEmail($data['email']);
            if($user){
               if (password_verify($data['password'], $user->getPassword())) {
                   $security->login($user);
                   return $this->redirectToRoute('app_search_track');
               }
            }
        }

        return $this->render('user/login.html.twig', [
            'form' => $form->createView(),
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route('/logout', name: 'app_user_logout')]
    public function logout(Security $security): Response
    {
        $security->logout();
        return $this->redirectToRoute('app_search_track');
    }

    #[Route('/register', name: 'app_user_register')]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_search_track');
        }

        $user = new User();
        $form = $this->createFormBuilder()->add(
            'email',
            TextType::class,
        )->add(
            'password',
            PasswordType::class,
        )->setMethod('GET')->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $form->get('password')->getData()
            );
            $user->setPassword($hashedPassword);
            $user->setEmail($form->get('email')->getData());

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_login');
        }

        return $this->render('user/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
