<?php

namespace App\Service;

use App\Entity\Reservation;
use App\Entity\User;
use App\Entity\Car;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;

class ReservationService
{
    private $entityManager;
    private $reservationRepository;

    public function __construct(EntityManagerInterface $entityManager, ReservationRepository $reservationRepository)
    {
        $this->entityManager = $entityManager;
        $this->reservationRepository = $reservationRepository;
    }

    public function createReservation(array $data): Reservation
    {
        $startDate = new \DateTime($data['startDate']);
        $endDate = new \DateTime($data['endDate']);

        $reservation = new Reservation();
        $reservation->setStartDate($startDate);
        $reservation->setEndDate($endDate);

        $userRepository = $this->entityManager->getRepository(User::class);
        $user = $userRepository->find($data['userId']);
        if (!$user) {
            throw new \Exception('User not found');
        }
        $reservation->setReservUser($user);

        $carRepository = $this->entityManager->getRepository(Car::class);
        $car = $carRepository->find($data['carId']);
        if (!$car) {
            throw new \Exception('Car not found');
        }
        $reservation->setReseCar($car);

        $this->entityManager->persist($reservation);
        $this->entityManager->flush();

        return $reservation;
    }

    public function getUserReservations(int $userId): array
    {
        $user = $this->entityManager->getRepository(User::class)->find($userId);
        if (!$user) {
            throw new \Exception('User not found');
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

        return $responseData;
    }

    public function updateReservation(int $id, array $data): Reservation
    {
        $reservation = $this->entityManager->getRepository(Reservation::class)->find($id);
        if (!$reservation) {
            throw new \Exception('Reservation not found');
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
            $user = $this->entityManager->getRepository(User::class)->find($data['userId']);
            if (!$user) {
                throw new \Exception('User not found');
            }
            $reservation->setReservUser($user);
        }

        if (isset($data['carId'])) {
            $car = $this->entityManager->getRepository(Car::class)->find($data['carId']);
            if (!$car) {
                throw new \Exception('Car not found');
            }
            $reservation->setReseCar($car);
        }

        $this->entityManager->persist($reservation);
        $this->entityManager->flush();

        return $reservation;
    }

    public function deleteReservation(int $id): void
    {
        $reservation = $this->entityManager->getRepository(Reservation::class)->find($id);
        if (!$reservation) {
            throw new \Exception('Reservation not found');
        }

        $this->entityManager->remove($reservation);
        $this->entityManager->flush();
    }
}