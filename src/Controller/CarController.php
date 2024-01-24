<?php

namespace App\Controller;

use App\Entity\Car;
use App\Repository\CarRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CarController extends AbstractController
{
    private $em;
    private $entityManager;

    public function __construct(EntityManagerInterface $em, ManagerRegistry $entityManager)
    {
        $this->em = $em;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/api/cars", name="car_list", methods={"GET"})
     */
    public function list(CarRepository $carRepository): Response
    {
        $cars = $carRepository->findAll();
        foreach ($cars as $key => $car) {
            if (!$car->isEnabled() && !empty($car->getReservations())) {
                unset($cars[$key]);
            }
        }

        return $this->json($cars);
    }

    /**
     * @Route("/api/cars/{id}", name="car_details", methods={"GET"})
     */
    public function details(Car $car): Response
    {
        return $this->json($car, 200, ['groups' => 'car:read']);
    }

    /**
     * @Route("/api/cars", name="car_create", methods={"POST"})
     */
    public function createCar(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        $car = new Car();
        $car->setModel($data['model']);
        $car->setBrand($data['brand']);
        $car->setYear($data['year']);

        $this->em->persist($car);
        $this->em->flush();

        return $this->json($car, 201);
    }
}