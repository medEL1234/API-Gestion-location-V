<?php

namespace App\Controller;

use App\Service\ReservationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReservationController extends AbstractController
{
    private $reservationService;

    public function __construct(ReservationService $reservationService)
    {
        $this->reservationService = $reservationService;
    }

    /**
     * @Route("/api/reservations", name="reservation_create", methods={"POST"})
     */
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $reservation = $this->reservationService->createReservation($data);

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
    public function userReservations(int $id): JsonResponse
    {
        $responseData = $this->reservationService->getUserReservations($id);

        return $this->json($responseData, 200);
    }

    /**
     * @Route("/api/reservations/{id}", name="reservation_update", methods={"PUT"})
     */
    public function update(int $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $reservation = $this->reservationService->updateReservation($id, $data);

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
    public function delete(int $id): JsonResponse
    {
        $this->reservationService->deleteReservation($id);

        return $this->json(['message' => 'Reservation deleted successfully'], 200);
    }
}