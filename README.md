Développement d'une API de gestion de location de voitures avec Symfon

Getting Started
These instructions will get you a copy of the project up and running on your local machine for development and testing purposes. See deployment for notes on how to deploy the project on a live system.

Prerequisites:
PHP 7.4 or higher
Symfony 5.x
Doctrine ORM
JWT Authentication Bundle
Installing
A step by step series of examples that tell you how to get a development environment running:

Clone the repository
Install dependencies with composer install
Set up the database and configure the connection in .env
Run migrations with php bin/console doctrine:migrations:migrate
-APIs:
Reservation API
Endpoint: /api/reservations
Method: POST
Description: Creates a new reservation.
Parameters: startDate, endDate, userId, carId
Endpoint: /api/users/{id}/reservations
Method: GET
Description: Gets all reservations for a specific user.
Parameters: id (the ID of the user)
Endpoint: /api/reservations/{id}
Method: PUT
Description: Updates a specific reservation.
Parameters: id (the ID of the reservation), startDate, endDate, userId, carId
Endpoint: /api/reservations/{id}
Method: DELETE
Description: Deletes a specific reservation.
Parameters: id (the ID of the reservation)
User API
Endpoint: /api/register
Method: POST
Description: Registers a new user.
Parameters: firstName, lastName, email, password, role, userLogin
Endpoint: /api/register/edit
Method: POST
Description: Edits an existing user.
Parameters: firstName, lastName, email, password, role, userLogin
Endpoint: /api/register/editUser/{id}
Method: POST
Description: Edits an existing user.
Parameters: id (the ID of the user), firstName, lastName, email, password, role, userLogin
Endpoint: /api/register/editUserEmployee/{id}
Method: POST
Description: Edits an existing user.
Parameters: id (the ID of the user), password
Endpoint: /api/login
Method: POST
Description: Logs in a user and returns a JWT token.
Parameters: username, password
Endpoint: /api/session
Method: GET
Description: Returns the current session user.
Endpoint: /api/username
Method: GET
Description: Adds usernames to all users.
Endpoint: /api/logout
Method: GET
Description: Logs out a user.
Entities
Reservation
Represents a reservation made by a user for a car.

-Properties:

id: The unique identifier of the reservation.
startDate: The start date of the reservation.
endDate: The end date of the reservation.
reservUser: The user who made the reservation.
reseCar: The car that was reserved.
User
Represents a user of the application.

-Properties:

id: The unique identifier of the user.
username: The username of the user.
password: The hashed password of the user.
roles: The roles assigned to the user.
email: The email of the user.
firstName: The first name of the user.
lastName: The last name of the user.
userLogin: The login of the user.
Car
Represents a car that can be reserved.

-Properties:

id: The unique identifier of the car.
model: The model of the car.
brand: The brand of the car.
year: The year of the car.
enabled: Whether the car is enabled or not.
createdAt: The date and time when the car was created.
reservations: The reservations made for this car.
