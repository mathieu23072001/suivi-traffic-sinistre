<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\UtilisateurType;
use App\Repository\ProfilRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;



/**
 * Controller used to manage blog contents in the backend.
 *
 * @Route("/usager")
 * 
 */
class UsagerController extends AbstractController
{
    #[Route('/register', name: 'app_usager_register')]
    public function index(Request $request, ProfilRepository $profilRepository, UserPasswordHasherInterface $passwordHasher): Response
    {
        $utilisateur = new Utilisateur();
        $form = $this->createForm(UtilisateurType::class, $utilisateur);
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();

        if ($form->isSubmitted() && $form->isValid()) {
            $password= $form->get('password')->getData();
            $rPassword = $form->get('repeatPassword')->getData();

if ($password == $rPassword) {
    $profil= $profilRepository->find(1);
    $utilisateur->addProfil($profil);
    $hashedPassword = $passwordHasher->hashPassword(
        $utilisateur,
        $password
    );
    $utilisateur->setPassword($hashedPassword);
    $em->persist($utilisateur);
    $em->flush();
    $this->addFlash('success', 'Abonnement effectué');
    return $this->redirectToRoute("app_acceuil");
}
else {
    $this->addFlash('error', 'Les mots de passes ne sont pas les memes. Retapez');
    return $this->redirectToRoute("app_usager_register");
}
        }
        return $this->render('usager/register.html.twig', [
            'controller_name' => 'UsagerController',
            'variable' => false,
            'form' => $form->createView(), 
             
        ]);
    }
}
