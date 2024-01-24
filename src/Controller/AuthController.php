<?php


namespace App\Controller;


use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use App\Entity\RefreshToken;


class AuthController extends ApiController
{
    private $em;
    private $entityManager;
    public function __construct(EntityManagerInterface $em,ManagerRegistry $entityManager)
    {
        $this->em = $em;
        $this->entityManager = $entityManager;
    }


    /**
     * @Route("/api/register", name="register", methods={"POST"})
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @return JsonResponse
     */
    public function register(Request $request, UserPasswordEncoderInterface $encoder,SerializerInterface $serializer): JsonResponse
    {
        $request = $this->transformJsonBody($request);
        $firstName = $request->get('firstName');
        $lastName = $request->get('lastName');
        $username = $request->get('email');
        $password = $request->get('password');
        $email = $request->get('email');
        $role = $request->get('role');
        $userLogin = $request->get('userLogin');

        if (empty($username) || empty($password) || empty($email)|| empty($userLogin)) {
            return $this->respondValidationError("Invalid Username or Password or Email or userLogin");
        }
        $users=$this->em->getRepository(User::class)->findOneBy(['email' => $email]);
        if ($users) {
            return $this->respondValidationError("Invalid Email Already used");
        }
        $users=$this->em->getRepository(User::class)->findOneBy(['userLogin' => $userLogin]);
        if ($users) {
            return $this->respondValidationError("Invalid userLogin Already used");
        }

        $user = new User($username);
        $user->setPassword($encoder->encodePassword($user, $password));
        $user->setEmail($email);
        $user->setUserLogin($userLogin);
        $user->setFirstName($firstName);
        $user->setLastName($lastName);
        $user->setRoles(['ROLE_USER']);
        if($role == "SUPER_ADMIN")
            $user->setRoles(['ROLE_SUPER_ADMIN']);
        
        //$user->setUsername($username);
        $this->em->persist($user);
        $this->em-> flush();
        $jsonContent = $serializer->serialize($user, 'json',['groups' => ['read_user']]);
        return new JsonResponse($jsonContent , 201, [], true);
    }

    /**
     * @Route("/api/register/edit", name="edit_user", methods={"POST"})
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @return JsonResponse
     */
    public function editUser(Request $request, UserPasswordEncoderInterface $encoder,SerializerInterface $serializer): JsonResponse
    {
        $request = $this->transformJsonBody($request);
        $firstName = $request->get('firstName');
        $lastName = $request->get('lastName');
        $password = $request->get('password');
        $email = $request->get('email');
        $role = $request->get('role');
        $userLogin = $request->get('userLogin');

        if (empty($email)) {
            return $this->respondValidationError("Invalid Email");
        }
        $user=$this->em->getRepository(User::class)->findOneBy(['email' => $email]);
        if (!$user) {
            return $this->respondValidationError("Email not exist");
        }
        if($password)
            $user->setPassword($encoder->encodePassword($user, $password));
        if($firstName)
            $user->setFirstName($firstName);
        if($lastName)
            $user->setLastName($lastName);
        if($userLogin)
            $user->setUserLogin($userLogin);
        if($role == "SUPER_ADMIN")
            $user->setRoles(['ROLE_SUPER_ADMIN']);
        $this->em->persist($user);
        $this->em-> flush();
        $jsonContent = $serializer->serialize($user, 'json',['groups' => ['read_user']]);
        return new JsonResponse($jsonContent , 201, [], true);
    }

    /**
     * @Route("/api/register/editUser/{id}", name="edit_user_Automatique", methods={"POST"})
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @return JsonResponse
     */
    public function editUserAutomatique(Request $request, UserPasswordEncoderInterface $encoder,SerializerInterface $serializer,$id): JsonResponse
    {
        $request = $this->transformJsonBody($request);
        $firstName = $request->get('firstName');
        $lastName = $request->get('lastName');
        $password = $request->get('password');
        $email = $request->get('email');
        $role = $request->get('role');
        $userLogin = $request->get('userLogin');
        
        
        $user = $this->em->getRepository(User::class)->find($id);
        if (!$user) {
            return $this->respondValidationError("user not exist");
        }
        // verfier email exist 
        if($user->getEmail() != $email){
            $users = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
            if ($users) {
                
                return new JsonResponse(["errors" => "Invalid Email Already used"],400);
            }
            if($email)
            $user->setEmail($email);
        }

        if($user->getUserLogin() != $userLogin){
            $users = $this->entityManager->getRepository(User::class)->findOneBy(['userLogin' => $userLogin]);
            if ($users) {
                return new JsonResponse(["errors" => "Invalid userLogin Already used"],400);
            }
            if($userLogin)
            $user->setUserLogin($userLogin);
        }

        if($password)
            $user->setPassword($encoder->encodePassword($user, $password));
        if($firstName)
            $user->setFirstName($firstName);
        if($lastName)
            $user->setLastName($lastName);
        
        if($role == "SUPER_ADMIN")
            $user->setRoles(['ROLE_SUPER_ADMIN']);
        $this->em->persist($user);
        $this->em-> flush();
        $jsonContent = $serializer->serialize($user, 'json',['groups' => ['read_user']]);
        return new JsonResponse($jsonContent , 201, [], true);
    }

    /**
     * @Route("/api/register/editUserEmployee/{id}", name="edit_user_Automatique_Employee", methods={"POST"})
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @return JsonResponse
     */
    public function editUserEmployeeAutomatique(Request $request, UserPasswordEncoderInterface $encoder,SerializerInterface $serializer,$id): JsonResponse
    {
        $request = $this->transformJsonBody($request);
        $password = $request->get('password');
        
        
        $user = $this->em->getRepository(User::class)->find($id);
        if (!$user) {
            return $this->respondValidationError("user not exist");
        }
      
        if($password)
            $user->setPassword($encoder->encodePassword($user, $password));
        $this->em->persist($user);
        $this->em-> flush();
        $jsonContent = $serializer->serialize($user, 'json',['groups' => ['read_user']]);
        return new JsonResponse($jsonContent , 201, [], true);
    }

    /**
     * @Route("/api/login", name="login-check", methods={"POST"})
     * @param UserInterface $user
     * @param JWTTokenManagerInterface $JWTManager
     * @return JsonResponse
     */
    public function getTokenUser(UserInterface $user, JWTTokenManagerInterface $JWTManager): JsonResponse
    {
        return new JsonResponse(['token' => $JWTManager->create($user)]);
    }

    /**
     * @Route("/api/session", name="get_user", methods={"GET"})
     */
    public function getUsers(SerializerInterface $serializer,Request $request) {
        $token = $request->headers->get('Authorization');
        $tokenParts = explode(".", $token);  
        $tokenHeader = base64_decode($tokenParts[0]);
        $tokenPayload = base64_decode($tokenParts[1]);
        $jwtHeader = json_decode($tokenHeader);
        $jwtPayload = json_decode($tokenPayload);
        $users = $this->entityManager->getRepository(User::class)->findOneBy(["email"=>$jwtPayload->username]);
        $productSerialized = $serializer->serialize($users,'json',['groups' => 'read_user']);
        return new Response($productSerialized);
    }

    /**
     * @Route("/api/username", name="post_user_username", methods={"GET"})
     */
    public function addUsernames(SerializerInterface $serializer,Request $request) {
        
        $users = $this->entityManager->getRepository(User::class)->findAll();
        $userLogin = 120001;
        foreach($users as $user){
            $user->setUserLogin($userLogin);
            
            $this->em->persist($user);
            $this->em-> flush();
            $userLogin += 1;
        }
        $productSerialized = $serializer->serialize($users,'json',['groups' => 'read_user']);
        return new Response($productSerialized);
    }

    /**
    * @Route("/api/logout", name="app_logout", methods={"GET"})
    */
    public function logout(Request $request)
    {  
        $token = $request->headers->get('Authorization');
        $tokenParts = explode(".", $token);  
        $tokenHeader = base64_decode($tokenParts[0]);
        $tokenPayload = base64_decode($tokenParts[1]);
        $jwtHeader = json_decode($tokenHeader);
        $jwtPayload = json_decode($tokenPayload);
        $username = $jwtPayload->username;
        $RefreshToken = $this->entityManager->getRepository(RefreshToken::class)->findOneBy(["username" => $username],['id' => 'DESC']);
        $this->em->remove($RefreshToken);
        $this->em-> flush();
        return new JsonResponse("is logged out");
    }

      /**
     * @Route("/api/registerAutomatic", name="register_automatic", methods={"POST"})
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @return JsonResponse
     */
    public function registerAutomatic(Request $request, UserPasswordEncoderInterface $encoder): JsonResponse
    {
        try {
            $request = $this->transformJsonBody($request);
            $firstName = $request->get('firstName');
            $lastName = $request->get('lastName');
    
            if (empty($firstName) || empty($lastName)) {
                return $this->respondValidationError("Invalid firstName or lastName");
            }
    
            // Create unique email and userLogin
            $emailDomain = $_ENV['EMAIL_DOMAIN'];
            [$userLogin, $email] = $this->generateUniqueUserLoginAndEmail($firstName, $lastName, $emailDomain);
    
            // Create the new user with the unique email and userLogin
            $user = new User($userLogin);
            $password = $_ENV['STATIC_PASSWORD']; 
            $user->setPassword($encoder->encodePassword($user, $password));
            $user->setEmail($email);
            $user->setUserLogin($userLogin);
            $user->setFirstName($firstName);
            $user->setLastName($lastName);
            $user->setRoles(['ROLE_USER']); 
    
            $this->em->persist($user);
            $this->em->flush();
    
            // Return the id, email, and userLogin of the created user
            return $this->json([
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'userLogin' => $user->getUserLogin(),
                'message' => sprintf('User %s successfully created with email %s', $user->getUsername(), $user->getEmail())
            ],200);
        } catch (\Exception $e) {
            // Handle the case where the email does not exist
            return new JsonResponse(["message" => "An error occurred: " . $e->getMessage()], 400);
        }
    }

  
    private function generateUniqueUserLoginAndEmail(string $firstName, string $lastName, string $emailDomain): array
    {
        $firstName = str_replace(' ', '-', $firstName);
        $lastName = implode('-', explode(' ', $lastName));

        $baseUserLogin = $firstName . '.' . $lastName;
        $userLogin = $baseUserLogin;
        $email = $userLogin . $emailDomain;

        // Check if the userLogin or email already exists
        $existingUserLogin = $this->em->getRepository(User::class)->findOneBy(['userLogin' => $userLogin]);
        $existingEmail = $this->em->getRepository(User::class)->findOneBy(['email' => $email]);

        while ($existingUserLogin || $existingEmail) {
            // If the userLogin or email exists, increment the number
            $parts = explode('_', $userLogin);
            $baseUserLogin = reset($parts); // Take the part before the underscore
            $number = end($parts);
            if (is_numeric($number)) {
                $userLogin = $baseUserLogin . '_' . str_pad(++$number, 3, '0', STR_PAD_LEFT);
            } else {
                // If no number found, append _001
                $userLogin = $baseUserLogin . '_001';
            }
            $email = $userLogin . $emailDomain;

            // Check again if the new userLogin or email exists
            $existingUserLogin = $this->em->getRepository(User::class)->findOneBy(['userLogin' => $userLogin]);
            $existingEmail = $this->em->getRepository(User::class)->findOneBy(['email' => $email]);
        }

        return [$userLogin, $email];
    }



}