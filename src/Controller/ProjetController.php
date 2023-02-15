<?php

namespace App\Controller;

use App\Entity\Projet;
use App\Entity\Fichiers;
use App\Entity\User;
use App\Form\AddImageType;
use App\Form\AddMembreType;
use App\Form\ProjetType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class ProjetController extends AbstractController
{
    /**
     * @Route("/projet", name="app_projet")
     */
    public function index(ManagerRegistry $doctrine): Response
    {
        $projets=$doctrine->getRepository(Projet::class)->findAll();
        return $this->render('projet/index.html.twig', [
            "projets"=>$projets
        ]);
    }

    /**
     * @Route("/projet/new", name="new_projet")
     */
    public function addProjet(ManagerRegistry $doctrine,Request $request):Response
    {
        $projet = new Projet();
        $form=$this->createForm(ProjetType::class,$projet);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $brandImage=$form->get('brandImage')->getData();
            $resume=$form->get('resume')->getData();

            if($brandImage){
                $safeName = md5(uniqid()) . '.' . $brandImage->guessExtension();
                try {
                    $brandImage->move(
                        $this->getParameter('images_directory'),
                        $safeName
                    );
                } catch (FileException $e) {
                   
                }
                $projet->setBrandImage($safeName);
            }

            if($resume){
                $safe = md5(uniqid()) . '.' . $resume->guessExtension();
                try {
                    $resume->move(
                        $this->getParameter('images_directory'),
                        $safe
                    );
                } catch (FileException $e) {
                   
                }
                $projet->setResume($safe);
            }
            $projet->setChefProjet($this->getUser());
            $projet->setCreatedAt(new \DateTimeImmutable);
            $doctrine->getManager()->persist($projet);
            $doctrine->getmanager()->flush();
            return $this->redirectToRoute("app_projet");
        }

        return $this->renderForm('projet/form.html.twig', [
            "form"=> $form
        ]);
    }

     /**
     * @Route("/projet/{id}/nouveau-membre",name="add_membre")
     */
    public function ajouterMembre(Projet $projet=null,Request $request,ManagerRegistry $doctrine):Response
    {
        if($projet ==null){
            return $this->redirectToRoute("app_projet");
        }
        $form=$this->createForm(AddMembreType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            //dd($form->get('membre')->getData());
            $user=$doctrine->getRepository(User::class)->find($form->get('membre')->getData());
            //dd($user);
            $projet->addMembre($user);
            $doctrine->getManager()->persist($projet);
            $doctrine->getmanager()->flush();
            return $this->redirectToRoute("details_projet",["id"=>$projet->getId()]);
        }
        return $this->renderForm('projet/addMembre.html.twig', [
            "form"=> $form
        ]);
    }

    /**
     * @Route("/projet/{id}/images",name="add_image")
     */
    public function ajouterImage(Projet $projet=null,ManagerRegistry $doctrine,Request $request):Response
    {
        if($projet ==null){
            return $this->redirectToRoute("app_projet");
        }
        $form=$this->createForm(AddImageType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $elts = $form->get('images')->getData();
            if($elts){
                foreach($elts as $elt){
                    $file = md5(uniqid()) . '.' . $elt->guessExtension();
                    $elt->move(
                        $this->getParameter('images_directory'),
                        $file
                    );
                    $fichier = new Fichiers();
                    $fichier->setNom($file);
                    $fichier->setType("image");
                    $fichier->setCreatedAt(new \DateTimeImmutable);
                    $projet->addFichier($fichier);
                }
            }
            $doctrine->getManager()->persist($projet);
            $doctrine->getmanager()->flush();
            return $this->redirectToRoute("details_projet",["id"=>$projet->getId()]);

        }

        return $this->render('projet/formImage.html.twig', [
            'form'=>$form->createView(),
            'projet'=>$projet
        ]);
    }

    /**
     * @Route("/projet/{id}/infos", name="details_projet")
     */
    
     public function details(Projet $projet=null,ManagerRegistry $doctrine):Response
     {
        if($projet== null){
            return $this->redirectToRoute("app_projet");
        } else{
            return $this->render('projet/infos.html.twig', [
                "projet" =>$projet
            ]);
        } 
     }
}
