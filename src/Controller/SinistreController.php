<?php

namespace App\Controller;

use App\Entity\Comments;
use App\Remote\InfoUtileInterface;
use App\Repository\CommentsRepository;
use DateInterval;
use App\Entity\Image;
use App\Entity\Sinistre;
use App\Form\SinistreType;
use App\Entity\Declaration;
use App\Service\ImageService;
use App\Entity\InformationUtile;
use App\Remote\MercureInterface;
use App\Service\SinistreService;
use App\Remote\SinistreInterface;
use App\Form\InformationUtileType;
use App\Service\DeclarationService;
use App\Entity\PositionGeographique;
use App\Remote\DeclarationInterface;
use App\Remote\UtilisateurInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SinistreController extends AbstractController
{

    private DeclarationInterface $declarationService;
    private UtilisateurInterface $utilisateurService;
    private MercureInterface $mercureService;
    private SinistreInterface $sinistreService;
    private InfoUtileInterface $infoUtileService;
    private CommentsRepository $commentsRepository;

    public function __construct(CommentsRepository $commentsRepository, MercureInterface $mercureService, DeclarationInterface $declarationService, UtilisateurInterface $utilisateurService, SinistreInterface $sinistreService, InfoUtileInterface $infoUtileService)
    {
        $this->declarationService = $declarationService;
        $this->utilisateurService = $utilisateurService;
        $this->mercureService = $mercureService;
        $this->sinistreService = $sinistreService;
        $this->infoUtileService = $infoUtileService;
        $this->commentsRepository = $commentsRepository;
    }

    #[Route('/sinistre', name: 'app_sinistre')]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $declarations = $this->declarationService->getAll();
        return $this->render('sinistre/index.html.twig', [
            'declarations' => $declarations,
        ]);
    }


    #[Route('/sinistre/cam', name: 'app_sinistre_cam')]
    public function cam(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $sinistres = $this->sinistreService->getAll();
        return $this->render('sinistre/cam.html.twig', [
            'sinistres' => $sinistres,
        ]);
    }
    #[Route('/sinistre/commentaire/delete/{id}', name: 'app_sinistre_commentaire_delete')]
    public function deleteCommentaire( Request $request, Comments $comment): Response
    {
        $id = $comment->getDeclarations()->getId();
        $this->commentsRepository->remove($comment);
        return $this->redirectToRoute('app_sinistre_details',['id'=>$id]);
    }

    #[Route('/sinistre/reply/delete/{id}', name: 'app_sinistre_reply_delete')]
    public function deleteReply( Request $request, Comments $reply): Response
    {
        $id = $reply->getParent()->getDeclarations()->getId();
        $this->commentsRepository->remove($reply);
        return $this->redirectToRoute('app_sinistre_details',['id'=>$id]);
    }
    #[Route('/sinistre/commentaire/details/{id}', name: 'app_sinistre_commentaire')]
    public function saveCommentaire( Request $request, Declaration $declaration): Response
    {
        $user = $this->getUser();
        if($request->getMethod() == "POST"){
            $contenuCommenaire = $request->request->get('comment');
            $comment = new Comments();
//            $comment->setDeclarations($declaration);
            $comment->setUtilisateurs($user);
            $comment->setContent($contenuCommenaire);
            $comment->setRgpd(true);
            $declaration->addComment($comment);
            $this->declarationService->modifierDeclaration($declaration);
//            $this->commentsRepository->save($comment);
        }
        return $this->redirectToRoute('app_sinistre_details',['id'=>$declaration->getId()]);
    }
    #[Route('/sinistre/reply/details/{id}', name: 'app_sinistre_reply')]
    public function saveReply( Request $request, Comments $comments): Response
    {
        $user = $this->getUser();
        if($request->getMethod() == "POST"){
            $contenuCommenaire = $request->request->get('reply');
            $reply = new Comments();
//            $reply->setDeclarations($comments->getDeclarations());
            $reply->setUtilisateurs($user);
            $reply->setContent($contenuCommenaire);
            $reply->setRgpd(true);
            $reply->setParent($comments);
            $comments->addReply($reply);
            $this->commentsRepository->save($comments);
        }
        return $this->redirectToRoute('app_sinistre_details',['id'=>$comments->getDeclarations()->getId()]);
    }
    #[Route('/sinistre/details/{id}', name: 'app_sinistre_details')]
    public function details( Request $request, Declaration $declaration): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();



        if($this->utilisateurService->isAdmin($user)){
            return $this->render('sinistre/details_admin.html.twig', [
                'a' => $declaration,
            ]);
        }
        return $this->render('sinistre/details_abonne.html.twig', [
            'a' => $declaration,
        ]);

    }

    #[Route('/sinistre/desactive/{id}', name: 'app_sinistre_desactive')]
    public function desactiverSinistre(Request $request, Declaration $declaration): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $declaration->setPublished(false);
        $this->declarationService->modifierDeclaration($declaration);
        $this->mercureService->publishUnSinistre();
        return  $this->redirectToRoute('app_sinistre');
    }

    #[Route('/sinistre/actvie/{id}', name: 'app_sinistre_active')]
    public function activerSinistre(Request $request, Declaration $declaration): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $declaration->setPublished(true);
        $declaration->setDatePublication(new \DateTime());

        $interval = new DateInterval('P1D');
        $dateFin = ($declaration->getDatePublication()->add($interval));
        $declaration->setDateFinPublication($dateFin);

        $this->declarationService->modifierDeclaration($declaration);
        $this->mercureService->publishUnSinistre();
        return $this->redirectToRoute('app_sinistre');
    }

    #[Route('/sinistre/add', name: 'app_sinistre_add')]
    public function add(SluggerInterface $slugger, Request $request, ImageService $imageService): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $typeDeclaration = $request->query->get('typeDeclaration');
        $user= $this->getUser();
        if($typeDeclaration == "sini"){
            $sinistre = new Sinistre();
            $sinistres = $this->sinistreService->getAllForAbonne($this->getUser());
            $form = $this->createForm(SinistreType::class, $sinistre);
        }else{
            $sinistre = new InformationUtile();
            $sinistres = $this->infoUtileService->getAllForAbonne($this->getUser());
            $form = $this->createForm(InformationUtileType::class, $sinistre);
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $images = $form->get('images')->getData();
            $lat = $form->get('latitude')->getData();
            $lon = $form->get('longitude')->getData();
            $lieu = $form->get('lieu')->getData();

            if($typeDeclaration == "sini") {
                $typeSinistre = $form->get('typeSinistre')->getData()->getLibelle();
                $libelle = strtolower($typeSinistre) . " " . "à" . " " . strtolower($lieu);
            }else{
                $libelle = strtolower("Information ") . " " . "sur " . " " . strtolower($lieu);
            }
            $sinistre->setLibelle($libelle);
            $sinistre->setLatitude($lat);
            $sinistre->setLongitude($lon);
            $sinistre->setUtilisateur($user);
//            $sinistre->setPublished(false);
            $sinistre->setPublished(true);
            $sinistre->setDatePublication(new \DateTime());

            $interval = new DateInterval('P1D');
            $dateFin = ($sinistre->getDatePublication()->add($interval));
            $sinistre->setDateFinPublication($dateFin);
//            $em = $this->getDoctrine()->getManager();

            foreach($images as $image){
                $folder = 'images';

                $fichier = $imageService->add($image, $folder, 300, 300);

                $img = new Image();
                $img->setFileName($fichier);

                $sinistre->addImage($img);


            }
            if($typeDeclaration == "sini") {
                $this->sinistreService->saveSinistre($sinistre);
            }else{
                $this->infoUtileService->saveDeclaration($sinistre);
            }

            $this->addFlash('success', 'enregistrement effectué');
            if($this->utilisateurService->isAbonne($user)){
                return $this->redirectToRoute("app_sinistre_add");
            }
            return $this->redirectToRoute("app_sinistre_add");

        }
        if($this->utilisateurService->isAdmin($user)){
        return $this->render('sinistre/admin_add.html.twig', [
            'form'=>$form->createView(),
            'sinistres' => $sinistres,
            'typeDeclaration' => $typeDeclaration,
        ]);
        }
        return $this->render('sinistre/abonne_add.html.twig', [
            'form'=>$form->createView(),
            'sinistres' => $sinistres,
            'typeDeclaration' => $typeDeclaration,
        ]);
    }


    #[Route('/sinistre/remove/{id}', name: 'app_sinistre_remove')]
    public function remove($id): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $sinistre = $this->getDoctrine()->getRepository(Sinistre::class);
        $sinistre = $sinistre->find($id);
        $this->sinistreService->removeSinistre($sinistre);
        $this->addFlash('success', 'suppression effectué');
        return $this->redirectToRoute('app_sinistre_add');

    }


    #[Route('/sinistre/edit/{id}', name: 'app_sinistre_edit')]
    public function edit($id, Sinistre $sinistre,Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $form = $this->createForm(SinistreType::class, $sinistre);
        $form->handleRequest($request);
        $sinistres = $this->sinistreService->getAll();

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
            $this->addFlash('success', 'Modification effectué');

            return $this->redirectToRoute('app_sinistre_add');
        }
        return $this->render('admin_add.html.twig', [
            'form' => $form->createView(),
            'sinistres' => $sinistres,
        ]);

    }




    #[Route('/like/sinistre/{id}', name: 'like.sinistre', methods: ['GET'])]

    public function like(Declaration $sinistre, EntityManagerInterface $manager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        if ($sinistre->isLikedByUser($user)) {
            $sinistre->removeLike($user);
            $manager->flush();

            return $this->json([
                'message' => 'Le like a été supprimé.',
                'nbLike' => $sinistre->howManyLikes()
            ]);
        }

        if ($sinistre->isDislikedByUser($user)) {
            
            $sinistre->removeDislike($user);
            $sinistre->addLike($user);
            $manager->flush();

            return $this->json([
                'message' => 'Le dislike a été supprimé et le like ajouté.',
                'nbLike' => $sinistre->howManyLikes()
            ]);
        }


        $sinistre->addLike($user);
        $manager->flush();

        return $this->json([
            'message' => 'Le like a été ajouté.',
            'nbLike' => $sinistre->howManyLikes()
        ]);
    }


    #[Route('/dislike/sinistre/{id}', name: 'dislike.sinistre', methods: ['GET'])]

    public function dislike(Declaration $sinistre, EntityManagerInterface $manager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        if ($sinistre->isDislikedByUser($user)) {
            $sinistre->removeDislike($user);
            $manager->flush();

            return $this->json([
                'message' => 'Le dislike a été supprimé.',
                'nbDislike' => $sinistre->howManyDislikes()
            ]);
        }

         if ($sinistre->isLikedByUser($user)) {
            $sinistre->removeLike($user);
            $sinistre->addDislike($user);
            $manager->flush();

            return $this->json([
                'message' => 'Le dislike a été supprimé et le like ajouté.',
                'nbDislike' => $sinistre->howManyDislikes()
            ]);
        }

        $sinistre->addDislike($user);
        $manager->flush();

        return $this->json([
            'message' => 'Le dislike a été ajouté.',
            'nbDislike' => $sinistre->howManyDislikes()
        ]);
    }



}






