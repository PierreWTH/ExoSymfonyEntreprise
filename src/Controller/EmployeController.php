<?php

namespace App\Controller;

use App\Entity\Employe;
use App\Form\EmployeType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class EmployeController extends AbstractController
{
    #[Route('/employe', name: 'app_employe')]
    public function index(ManagerRegistry $doctrine): Response
    {   // Recuperer les employés de la BDD. 
        // le find by opere comme un select * from employe where id = 2 ORDER BY prenom ASC
        $employes = $doctrine->getRepository(Employe::class)->findBy([], ["prenom" => "ASC"]);
        return $this->render('employe/index.html.twig', [
            'controller_name' => 'EmployeController',
            'employes' => $employes
        ]);
    }

    // On peut avoir plusieurs routes pour la meme méthode
    #[Route('/employe/add', name: 'add_employe')]
    #[Route('/employe/{id}/edit', name: 'edit_employe')]
        public function add(ManagerRegistry $doctrine, Employe $employe = null, Request $request): Response
        {   
            if (!$employe){
                $employe = new Employe();
            }

            $form = $this->createForm(EmployeType::class, $employe);
            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid())
            {
                $employe = $form->getData(); // On récupère les données du form
                $entityManager = $doctrine->getManager(); // On recupére le manager de doctrine (bdd)
                $entityManager->persist($employe); // Equivalent du prepare en PDO
                $entityManager->flush(); // Equivalent du Execute

                return $this->redirectToRoute('app_employe');
            }

            // Vue pour afficher le formulaire d'ajout
            return $this->render('employe/add.html.twig', [
                'formAddEmploye' => $form->createView(),
                'edit' =>$employe->getId()
            ]);

        }
    
    #[Route('/employe/{id}/delete', name: 'delete_employe')]
    public function delete (ManagerRegistry $doctrine, Employe $employe)
    {   
        $entityManager = $doctrine->getManager();
        $entityManager->remove($employe);
        $entityManager->flush();

        return $this->redirectToRoute('app_employe');
    }



    #[Route('/employe/{id}', name: 'show_employe')]
    public function show(Employe $employe): Response
    {
        return $this->render('employe/show.html.twig', [
            'employe' => $employe
        ]);
    }
}
