<?php
namespace App\EventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;

class AuthenticationSuccessListener{
    
    

    /**
     * @param AuthenticationSuccessEvent $event
     */
    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event)
    {  //dd($event->getUser()); 
       /* $data = $event->getData();
        $user = $event->getUser();
     
        if(!$user->getStatus()){
            $data = array(
                'errors' => "Account has been disabled",
            );
        }

        $event->setData($data);*/
    }
}