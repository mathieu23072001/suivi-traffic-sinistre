<?php

namespace App\Controller;

use App\Entity\Profil;
use App\Form\ProfilType;
use App\Entity\Utilisateur;
use App\Remote\ProfilInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProfilController extends AbstractController
{

    private ProfilInterface $profilService;

    public function __construct(ProfilInterface $profilService)
    {
        $this->profilService = $profilService;
    }


    #[Route('/Profil/ ', name: 'app_profil')]
    public function register(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $profils = $this->profilService->getAll();
        $profil = new Profil();
        $form = $this->createForm(ProfilType::class, $profil);
        $form->handleRequest($request);
        $form1 = $this->createForm(ProfilType::class, $profil);
        $form1->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $this->profilService->saveProfil($profil);

            $this->addFlash('success', 'enregistrement effectué');
            return $this->redirectToRoute("app_profil");

        }
       
        return $this->render('profil/index.html.twig', [
            'form' => $form->createView(),
            'form1' => $form1->createView(),
            'profils' => $profils,
        ]);
    }

    

    #[Route('/Profil/remove/{id}', name: 'app_profil_remove')]
    public function remove($id): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $profil = $this->getDoctrine()->getRepository(Profil::class);
        $profil = $profil->find($id);
        $this->profilService->removeProfil($profil);
        $this->addFlash('success', 'suppression effectué');
         return $this->redirectToRoute('app_profil');
        
    }


#[Route('/Profil/edit/{id}',name: 'app_profil_edit')]
public function modificationAction(Request $request,$id)
{
  $id = $request->request->get('id');
  $code = $request->request->get('code');
  $libelle = $request->request->get('libelle');
  $profil = $this->getDoctrine()->getRepository(Profil::class);
  $profil = $profil->find($id);

  $profil->setCode($code);
  $profil->setLibelle($libelle);

  $this->profilService->saveProfil($profil);
  $this->addFlash('success', 'modification effectuée');
  return $this->redirectToRoute('app_profil');

  
}


}
