<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Form\CommentaireType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Reservation;
use App\Entity\Vehicule;
use App\Form\ReservationType;
use Symfony\Component\HttpFoundation\Request;

#[Route('/client')]
final class CompteController extends AbstractController
{

    

    #[Route('/vehicule/{id}', name: 'app_client_vehicule')]
    public function client(Vehicule $vehicule, Request $request, EntityManagerInterface $manager): Response
    {
        $reserver = new Reservation;
        $comment = new Commentaire;
        
        $user = $this->getUser();
        $hasReservation = $manager->getRepository(Reservation::class)->findOneBy([
            'client' => $user,
            'vehicule' => $vehicule
        ]);
        $totalReservations = $manager->getRepository(Reservation::class)->count([
            'vehicule' => $vehicule
        ]);

        $form = $this->createForm(ReservationType::class, $reserver);
        $formComment = $this->createForm(CommentaireType::class, $comment);

        $form->remove('prix');
        $form->remove('vehicule');
        $form->remove('client');

        $form->handleRequest($request);

        if( $form->isSubmitted() ){
            $reserver->setPrix( $request->get('prixReservation') );
            $reserver->setClient($this->getUser());
            $reserver->setVehicule($vehicule);
            $vehicule->setStatut(false);
            $manager->persist($reserver);
            $manager->flush();
        }

        $formComment->handleRequest($request);

        if( $formComment->isSubmitted() ){
            $comment->setClient($user);
            $comment->setVehicule($vehicule);
            $comment->setDateComment(new \DateTimeImmutable('now'));

            $manager->persist($comment);
            $manager->flush();
        }


        return $this->render('home/reserver.html.twig', [
            "vehicule" => $vehicule,
            "form" => $form,
            "commentaires" => $vehicule->getCommentaires(),
            "formComment" => $formComment,
            "hasReservation" => $hasReservation,
            "totalReservations" => $totalReservations
        ]);
    }
}
