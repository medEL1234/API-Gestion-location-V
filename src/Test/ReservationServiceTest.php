<?php

namespace App\Tests\Service;

use App\Entity\Reservation;
use App\Entity\User;
use App\Entity\Car;
use App\Repository\ReservationRepository;
use App\Service\ReservationService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class ReservationServiceTest extends TestCase
{
    private $entityManager;
    private $reservationRepository;
    private $reservationService;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->reservationRepository = $this->createMock(ReservationRepository::class);

        $this->reservationService = new ReservationService($this->entityManager, $this->reservationRepository);
    }

    public function testCreateReservation(): void
    {
        $user = new User();
        $user->setId(1);

        $car = new Car();
        $car->setId(1);

        $data = [
            'startDate' => '2024-03-10 09:00:00',
            'endDate' => '2024-03-10 17:00:00',
            'userId' => 1,
            'carId' => 1
        ];

        $expectedReservation = new Reservation();
        $expectedReservation->setStartDate(new \DateTime('2024-03-10 09:00:00'));
        $expectedReservation->setEndDate(new \DateTime('2024-03-10 17:00:00'));
        $expectedReservation->setReservUser($user);
        $expectedReservation->setReseCar($car);

        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($expectedReservation);

        $this->entityManager->expects($this->once())
            ->method('flush');

        $actualReservation = $this->reservationService->createReservation($data);

        $this->assertEquals($expectedReservation, $actualReservation);
    }

        public function testUpdateReservation(): void 
    {
        
        $reservationId = 1;
        $updatedData = [
            'startDate' => '2024-03-15 10:00:00',
            'endDate' => '2024-03-15 18:00:00',
            'userId' => 2,
            'carId' => 2
        ];

        
        $originalReservation = new Reservation();
        $originalReservation->setId($reservationId);
        $originalReservation->setStartDate(new \DateTime('2024-03-10 09:00:00'));
        $originalReservation->setEndDate(new \DateTime('2024-03-10 17:00:00'));

        
        $this->reservationRepository->expects($this->once())
            ->method('find')
            ->with($reservationId)
            ->willReturn($originalReservation);

        $this->reservationRepository->expects($this->once())
            ->method('saveReservation');

        
        $reservationService = new ReservationService($this->entityManager, $this->reservationRepository);
        $updatedReservation = $reservationService->updateReservation($reservationId, $updatedData);

        
        $this->assertInstanceOf(Reservation::class, $updatedReservation);
        $this->assertEquals($reservationId, $updatedReservation->getId());
        $this->assertEquals(new \DateTime($updatedData['startDate']), $updatedReservation->getStartDate());
        $this->assertEquals(new \DateTime($updatedData['endDate']), $updatedReservation->getEndDate());
        $this->assertEquals($updatedData['userId'], $updatedReservation->getReservUser()->getId());
        $this->assertEquals($updatedData['carId'], $updatedReservation->getReseCar()->getId());
    }

}