<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Entity\User;
use App\Entity\Car;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReservationController extends AbstractController
{
    private $em;
    private $entityManager;

    public function __construct(EntityManagerInterface $em, ManagerRegistry $entityManager)
    {
        $this->em = $em;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/api/reservations", name="reservation_create", methods={"POST"})
     */
    public function create(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        $startDate = new \DateTime($data['startDate']);
        $endDate = new \DateTime($data['endDate']);

        $reservation = new Reservation();
        $reservation->setStartDate($startDate);
        $reservation->setEndDate($endDate);

        $userRepository = $this->getDoctrine()->getRepository(User::class);
        $user = $userRepository->find($data['userId']);
        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }
        $reservation->setReservUser($user);
        

        $carRepository = $this->getDoctrine()->getRepository(Car::class);
        $car = $carRepository->find($data['carId']);
        if (!$car) {
            throw $this->createNotFoundException('Car not found');
        }
        $reservation->setReseCar($car);
        
        $this->em->persist($reservation);
        $this->em->flush();

        $responseData = [
            'id' => $reservation->getId(),
            'startDate' => $reservation->getStartDate()->format('Y-m-d H:i:s'),
            'endDate' => $reservation->getEndDate()->format('Y-m-d H:i:s'),
            'userId' => $reservation->getReservUser()->getId(),
            'carId' => $reservation->getReseCar()->getId()
        ];
    
        return $this->json($responseData, 201);
    }

    /**
     * @Route("/api/users/{id}/reservations", name="user_reservations", methods={"GET"})
     */
    public function userReservations(int $id): Response
    {
        $userRepository = $this->getDoctrine()->getRepository(User::class);
        $user = $userRepository->find($id);
        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        $reservations = $user->getReservations();
        $responseData = [];
        foreach ($reservations as $reservation) {
            $responseData[] = [
                'id' => $reservation->getId(),
                'startDate' => $reservation->getStartDate()->format('Y-m-d H:i:s'),
                'endDate' => $reservation->getEndDate()->format('Y-m-d H:i:s'),
                'user' => $reservation->getReservUser()->getEmail(),
                'cars' => $reservation->getReseCar()->getId()
            ];
        }
    
        return $this->json($responseData, 201);

        //return $this->json($reservations, 201, [], ['groups' => 'reservation']);
    }

    /**
     * @Route("/api/reservations/{id}", name="reservation_update", methods={"PUT"})
     */
    public function update(int $id, Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        $reservationRepository = $this->getDoctrine()->getRepository(Reservation::class);
        $reservation = $reservationRepository->find($id);
        if (!$reservation) {
            throw $this->createNotFoundException('Reservation not found');
        }

        if (isset($data['startDate'])) {
            $startDate = new \DateTime($data['startDate']);
            $reservation->setStartDate($startDate);
        }

        if (isset($data['endDate'])) {
            $endDate = new \DateTime($data['endDate']);
            $reservation->setEndDate($endDate);
        }

        if (isset($data['userId'])) {
            $userRepository = $this->getDoctrine()->getRepository(User::class);
            $user = $userRepository->find($data['userId']);
            if (!$user) {
                throw $this->createNotFoundException('User not found');
            }
            $reservation->setReservUser($user);
        }

        if (isset($data['carId'])) {
            $carRepository = $this->getDoctrine()->getRepository(Car::class);
            $car = $carRepository->find($data['carId']);
            if (!$car) {
                throw $this->createNotFoundException('Car not found');
            }
            $reservation->setReseCar($car);
        }
        //dd($reservation);

        $this->em->persist($reservation);
        $this->em->flush();
        
        $responseData = [
            'id' => $reservation->getId(),
            'startDate' => $reservation->getStartDate()->format('Y-m-d H:i:s'),
            'endDate' => $reservation->getEndDate()->format('Y-m-d H:i:s'),
            'userId' => $reservation->getReservUser()->getId(),
            'carId' => $reservation->getReseCar()->getId()
        ];
    
        

        return $this->json($responseData, 200);
    }

    /**
     * @Route("/api/reservations/{id}", name="reservation_delete", methods={"DELETE"})
     */
    public function delete(int $id): Response
    {
        $reservationRepository = $this->getDoctrine()->getRepository(Reservation::class);
        $reservation = $reservationRepository->find($id);
        if (!$reservation) {
            throw $this->createNotFoundException('Reservation not found');
        }

        $this->em->remove($reservation);
        $this->em->flush();

        return $this->json(['message' => 'Reservation deleted successfully'], 200);
    }
}
