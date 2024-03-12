<?php

namespace App\Controller;

use App\Entity\Car;
use App\Service\CarService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CarController extends AbstractController
{
    private $carService;

    public function __construct(CarService $carService)
    {
        $this->carService = $carService;
    }

    /**
     * @Route("/api/cars", name="car_list", methods={"GET"})
     */
    public function list(): Response
    {
        $cars = $this->carService->getAllCars();

        return $this->json($cars);
    }

    /**
     * @Route("/api/cars/{id}", name="car_details", methods={"GET"})
     */
    public function details(Car $car): JsonResponse
    {
        return $this->json($car, 200, ['groups' => 'car:read']);
    }

    /**
     * @Route("/api/cars", name="car_create", methods={"POST"})
     */
    public function createCar(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $car = $this->carService->createCar($data);

        return $this->json($car, 201);
    }
}