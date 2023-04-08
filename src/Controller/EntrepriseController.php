<?php

namespace App\Controller;

use App\Entity\Entreprise;
use App\Form\EntrepriseType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class EntrepriseController extends AbstractController
{
    #[Route('/entreprise', name: 'app_entreprise')]
    public function index(ManagerRegistry $doctrine): Response
    {   
        // Récupère les entreprises de la bdd
        $entreprise = $doctrine->getRepository(Entreprise::class)->findBy([], ["raisonSociale" => "ASC"]);
        return $this->render('entreprise/index.html.twig', [
            'entreprises'=> $entreprise
        ]);
    }


    #[Route('/entreprise/add', name: 'add_entreprise')]
    #[Route('/entreprise/{id}/edit', name: 'edit_entreprise')]
        public function add(ManagerRegistry $doctrine, Entreprise $entreprise = null, Request $request): Response
        {   
            if (!$entreprise){
                $entreprise = new Entreprise();
            }

            $form = $this->createForm(EntrepriseType::class, $entreprise);
            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid())
            {
                $entreprise = $form->getData(); // On récupère les données du form
                $entityManager = $doctrine->getManager(); // On recupére le manager de doctrine (bdd)
                $entityManager->persist($entreprise); // Equivalent du prepare en PDO
                $entityManager->flush(); // Equivalent du Execute

                return $this->redirectToRoute('app_entreprise');
            }

            // Vue pour afficher le formulaire d'ajout
            return $this->render('entreprise/add.html.twig', [
                'formAddEntreprise' => $form->createView(),
                'edit' =>$entreprise->getId()
            ]);

        }

    #[Route('/entreprise/{id}/delete', name: 'delete_entreprise')]
    public function delete (ManagerRegistry $doctrine, Entreprise $entreprise)
    {   
        $entityManager = $doctrine->getManager();
        $entityManager->remove($entreprise);
        $entityManager->flush();

        return $this->redirectToRoute('app_entreprise');
    }

    #[Route('/entreprise/{id}', name: 'show_entreprise')]
    public function show(Entreprise $entreprise): Response
    {
        return $this->render('entreprise/show.html.twig', [
            'entreprise' => $entreprise
        ]);
    }
    
        
}
