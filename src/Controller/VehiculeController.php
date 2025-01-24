<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Entity\Vehicule;
use App\Form\VehiculeType;
use App\Repository\VehiculeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Monolog\DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/vehicule')]
final class VehiculeController extends AbstractController
{
    #[Route(name: 'app_vehicule_index', methods: ['GET'])]
    public function index(VehiculeRepository $vehiculeRepository): Response
    {
        return $this->render('vehicule/index.html.twig', [
            'vehicules' => $vehiculeRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_vehicule_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $vehicule = new Vehicule();
        $form = $this->createForm(VehiculeType::class, $vehicule);

        $form->remove('immatricule');
        $form->remove('statut');

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $vehicule->setStatut(1);
            $vehicule->setImg("1.jpeg");
            $vehicule->setImmatricule( $this->matricule($vehicule->getMarque()) );

            $entityManager->persist($vehicule);
            $entityManager->flush();

            return $this->redirectToRoute('app_vehicule_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('vehicule/new.html.twig', [
            'vehicule' => $vehicule,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_vehicule_show', methods: ['GET'])]
    public function show(Vehicule $vehicule, EntityManagerInterface $entityManager): Response
    {
        $hasReservation = $entityManager->getRepository(Reservation::class)->findOneBy([
            // 'client' => $user,
            'vehicule' => $vehicule
        ]);
        dump($hasReservation);
        return $this->render('vehicule/show.html.twig', [
            'vehicule' => $vehicule,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_vehicule_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Vehicule $vehicule, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(VehiculeType::class, $vehicule);
        $form->handleRequest($request);
        $endReservation = $entityManager->getRepository(Reservation::class)->findOneBy([
            'vehicule' => $vehicule
        ]);
        $endReservation->getDateFin();
        $dateToday = new \DateTimeImmutable();


        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_vehicule_index', [], Response::HTTP_SEE_OTHER);
            // return $this->redirectToRoute('app_vehicule_index', ["date_fin"=>$endReservation, "dateToday"=>$dateToday ], Response::HTTP_SEE_OTHER);
        }
        dump( $endReservation, $dateToday);

        return $this->render('vehicule/edit.html.twig', [
            'vehicule' => $vehicule,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_vehicule_delete', methods: ['POST'])]
    public function delete(Request $request, Vehicule $vehicule, EntityManagerInterface $entityManager): Response
    {
        $hasReservation = $entityManager->getRepository(Reservation::class)->findOneBy([
            // 'client' => $user,
            'vehicule' => $vehicule
        ]);
        dump( $hasReservation);
        if ($this->isCsrfTokenValid('delete'.$vehicule->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($vehicule);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_vehicule_index', [], Response::HTTP_SEE_OTHER);
    }

    private function matricule($marque){
        $str = substr($marque, 0, 2) . "-";

        for($i=0; $i<4; $i++){
            $str .= chr( rand(0, 25) + 65 );
        }

        $str .= '-';

        for($i=0; $i<3; $i++){
            $str .= rand(0, 9);
        }

        return $str;
    }
}
