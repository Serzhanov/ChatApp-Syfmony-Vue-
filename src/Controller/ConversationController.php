<?php

namespace App\Controller;

use App\Entity\Conversation;
use App\Entity\Participant;
use App\Repository\ConversationRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\WebLink\Link;
#[Route('/conversations', name: 'app_conversation')]


class ConversationController extends AbstractController
{ 

      /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var ConversationRepository
     */
    private $conversationRepository;
    /**
     * 
     */


    public function __construct(UserRepository $userRepository,EntityManagerInterface $entityManager,ConversationRepository $conversationRepository)
    {
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
        $this->conversationRepository=$conversationRepository;
    }

      
   
    /**
     * @Route("/", name="newConversations",methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */


    public function index(Request $request,int $id): Response
    {   
        $otherUser=$request->get(key:'otherUser',default:0);
        $otherUser=$this->userRepository->find($otherUser);
        
        
    

    
    if (is_null($otherUser)) {
        throw new \Exception("The user was not found");
    }

    // cannot create a conversation with myself
    if ($otherUser->getId() === $this->getUser()->getId()) {
        throw new \Exception("That's deep but you cannot create a conversation with yourself");
    }

    // if($this->$entityManager==null){
    //     throw new \Exception("Repository is null");
    // }

    // Check if conversation already exists
    $conversation = $this->conversationRepository->findConversationByParticipants(
        $otherUser->getId(),
        $this->getUser()->getId()
    );
    if (count($conversation)) {
        throw new \Exception("The conversation already exists");
        //Maybe You should add return statement here
    }

    $conversation = new Conversation();

    $participant = new Participant();
    $participant->setUser($this->getUser());
    $participant->setConversation($conversation);


    $otherParticipant = new Participant();
    $otherParticipant->setUser($otherUser);
    $otherParticipant->setConversation($conversation);

    $this->entityManager->getConnection()->beginTransaction();
    try {
        $this->entityManager->persist($conversation);
        $this->entityManager->persist($participant);
        $this->entityManager->persist($otherParticipant);

        $this->entityManager->flush();
        $this->entityManager->commit();

    } catch (\Exception $e) {
        $this->entityManager->rollback();
        throw $e;
    }

    //CROSS PROBLEM

    $response = new Response();
    $response->setContent(json_encode([
        'id' => $conversation->getId()
    ], Response::HTTP_CREATED, [], []));

    $response->headers->set('Content-Type', 'application/json');
    // Allow all websites
    $response->headers->set('Access-Control-Allow-Origin', '*');



    // return $this->json([
    //     'id' => $conversation->getId()
    // ], Response::HTTP_CREATED, [], []);
    return $response;
    }


    /**
     * @Route("/", name="getConversations", methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     */
    public function getConvs(Request $request) {
        $conversations = $this->conversationRepository->findConversationsByUser($this->getUser()->getId());
        
        $hubUrl = $this->getParameter('mercure.default_hub');

        $this->addLink($request, new Link('mercure', $hubUrl));

        
        return $this->json($conversations);
    }

}
