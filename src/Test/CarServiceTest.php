<?php

namespace App\Tests\Service;

use App\Entity\Car;
use App\Entity\Reservation;
use App\Repository\CarRepository;
use App\Repository\ReservationRepository;
use App\Service\CarService;
use App\Service\ReservationService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class ServiceTest extends TestCase
{
    private $entityManager;
    private $carRepository;
    private $reservationRepository;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->carRepository = $this->createMock(CarRepository::class);
        $this->reservationRepository = $this->createMock(ReservationRepository::class);
    }

    public function testGetAllCars(): void
    {
        $cars = [new Car(), new Car(), new Car()];

        $this->carRepository->expects($this->once())
            ->method('findAll')
            ->willReturn($cars);

        $carService = new CarService($this->entityManager, $this->carRepository);
        $result = $carService->getAllCars();

        $this->assertEquals($cars, $result);
    }

    public function testGetCarDetails(): void
    {
        
        $carId = 1;
        $carModel = 'Test Model';
        $carBrand = 'Test Brand';
        $carYear = 2022;
        $carEnabled = true;
    
        
        $car = new Car();
        $car->setId($carId);
        $car->setModel($carModel);
        $car->setBrand($carBrand);
        $car->setYear($carYear);
        $car->setEnabled($carEnabled);
    
        
        $this->carRepository->expects($this->once())
            ->method('find')
            ->with($carId)
            ->willReturn($car);
    
        
        $carService = new CarService($this->entityManager, $this->carRepository);
        $retrievedCar = $carService->getCarById($carId);
    
        
        $this->assertInstanceOf(Car::class, $retrievedCar);
        $this->assertEquals($carId, $retrievedCar->getId());
        $this->assertEquals($carModel, $retrievedCar->getModel());
        $this->assertEquals($carBrand, $retrievedCar->getBrand());
        $this->assertEquals($carYear, $retrievedCar->getYear());
        $this->assertEquals($carEnabled, $retrievedCar->isEnabled());
    }
    

    public function testCreateReservation(): void
    {
        $reservationData = [
            'startDate' => '2024-03-10 09:00:00',
            'endDate' => '2024-03-10 17:00:00',
            'userId' => 1,
            'carId' => 1
        ];

        $reservation = new Reservation();

        // Mock behavior of repositories and EntityManager
        $this->reservationRepository->expects($this->once())
            ->method('saveReservation')
            ->willReturn($reservation);

        $reservationService = new ReservationService($this->entityManager, $this->reservationRepository);
        $result = $reservationService->createReservation($reservationData);

        $this->assertInstanceOf(Reservation::class, $result);
    }

    
}