<?php

namespace App\Service;

use App\Entity\Car;
use App\Repository\CarRepository;
use Doctrine\ORM\EntityManagerInterface;

class CarService
{
    private $entityManager;
    private $carRepository;

    public function __construct(EntityManagerInterface $entityManager, CarRepository $carRepository)
    {
        $this->entityManager = $entityManager;
        $this->carRepository = $carRepository;
    }

    public function getAllCars(): array
    {
        $cars = $this->carRepository->findAll();

        // Filter out disabled cars with reservations
        $cars = array_filter($cars, function (Car $car) {
            return $car->isEnabled() || empty($car->getReservations());
        });

        return $cars;
    }

    public function getCarDetails(Car $car): Car
    {
        return $car;
    }

    public function createCar(array $data): Car
    {
        $car = new Car();
        $car->setModel($data['model']);
        $car->setBrand($data['brand']);
        $car->setYear($data['year']);

        $this->entityManager->persist($car);
        $this->entityManager->flush();

        return $car;
    }
}