<?php

namespace App\Controller;

use Lcobucci\JWT\Token\Builder;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Signer\Key\InMemory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    
    public function index()
    {

        $config = Configuration::forSymmetricSigner(new Sha256(), InMemory::plainText('mercure_secret_key'));
        if($this->getUser()==null){
            throw new \Exception("You have not signed in\n");
        }
        $username =$this->getUser()->getUsername();
        $token =  $config->builder()
            ->withClaim('mercure', ['subscribe' => [sprintf("/%s", $username)]])
            ->getToken(
                $config->signer(), $config->signingKey()
            )
        ;

        $response =  $this->render('index/index.html.twig', [
            'controller_name' => 'IndexController',
        ]);

        $response->headers->setCookie(
            new Cookie(
                'mercureAuthorization',
                $token->toString(),
                (new \DateTime())
                ->add(new \DateInterval('PT2H')),
                '/.well-known/mercure',
                'localhost',
                false,
                true,
                false,
                'strict'
            )
        );
       

        
        
        // Allow all websites
        
        return $response;
    }
}