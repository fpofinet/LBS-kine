<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Projet;
use App\Form\UserType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{
     /**
     * @Route("/momcompte", name="app_user")
     */
    public function index(ManagerRegistry $doctrine)
    {
        $datas=$doctrine->getRepository(Projet::class)->findAll();
        $user=$doctrine->getRepository(User::class)->find($this->getUser());
        $projets= new ArrayCollection();
        foreach($datas as $item){
            foreach($item->getMembres() as $membre){
                if($membre->getId() == $user->getId()){
                    $projets->add($item);
                }
            }
        }
        return $this->render('user/index.html.twig',[
            "projets" =>$projets
        ]);
    }
    
     /**
     * @Route("/user/contact", name="contact_user")
     */
    public function contactUser():Response
    {
        return $this->render('user/contact.html.twig',[
            
        ]);
    }

    /**
     * @Route("/admin", name="admin")
     */
    public function admin(ManagerRegistry $doctrine): Response
    {
        $users= $doctrine->getRepository(User::class)->findAll();
        return $this->render('', [
            'users' => $users,
        ]);
    }
    /**
     * @Route("/signin",name="new_user")
     */
    public function addUser(ManagerRegistry $doctrine,Request $request,UserPasswordHasherInterface $encoder){
        
        $user = new User();
        $form=$this->createForm(UserType::class,$user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $fichier = $form->get('imageProfil')->getData();
            if ($fichier) {
                $safeName = md5(uniqid()) . '.' . $fichier->guessExtension();
                try {
                    $fichier->move(
                        $this->getParameter('images_directory'),
                        $safeName
                    );
                } catch (FileException $e) {
                   
                }
                $user->setImageProfil($safeName);
            }
            $user->setCreatedAt(new \DateTimeImmutable);
            $hash= $encoder->hashPassword($user,$user->getPassword());
            $user->setPassword($hash);
            $doctrine->getManager()->persist($user);
            $doctrine->getmanager()->flush();
            return $this->redirectToRoute("app_home");
        }
       return $this->renderForm("user/form.html.twig",[
        "form"=> $form
       ]);
    }

    /**
     * @Route("/account/update/{id}",name="update_user")
     */
    public function updateUser(User $user=null,ManagerRegistry $doctrine,Request $request){
        if($user==null){
            $user = new User();
        }
        $form=$this->createForm(UserType::class,$user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $doctrine->getManager()->persist($user);
            $doctrine->getmanager()->flush();
            return $this->redirectToRoute("app_user");
        }
       return $this->renderForm("user/form.html.twig",[
        "form"=> $form
       ]);
    }

     /**
     * @Route("/user/delete/{id}",name="delete_user")
     */
    public function deleteUser(User $user=null,ManagerRegistry $doctrine,Request $request){
        if($user!=null){
            $doctrine->getManager()->remove($user);
            $doctrine->getmanager()->flush();
            return $this->redirectToRoute("app_user");
        }
       
       return $this->renderForm("user/index.html.twig");
    }
}
