<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Ingredients; 
use App\Exception\FormExeption; 
use App\Form\IngredientsType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\IngredientsRepository ; 
use JMS\Serializer\SerializerInterface;

class IngredientsController extends AbstractController
{
    public function __construct(IngredientsRepository $repository ,  EntityManagerInterface  $entityManager )
{
    $this-> repository= $repository;
    $this->entityManager =  $entityManager;
}
 
    public function index(IngredientsRepository $repository)
    {
        $Ingredients = $repository->transformAll();
        return $this->respond($Ingredients);
    }

     /**
     * @Route("/ingredients", name="createIngredients",methods="POST")
     */

    public function createIngredients(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $ingredients = new Ingredients();
        $form = $this->createForm(IngredientsType::class,$ingredients);
   
           $form->submit($data);
           if (!$form->isSubmitted() || !$form->isValid()) {
            return $this->respond($form, Response::HTTP_BAD_REQUEST);
        }
           if ($form->isSubmitted() && $form->isValid())
           {
            $entityManager  = $this->getDoctrine()->getManager();

            $entityManager ->persist($ingredients);

            $entityManager ->flush();
             
           }
           $response = array(
           
            'code' => 0,
            'message' => 'created with success!',
            'errors' => null, 
            'result' => null
    
        );  
            
           return new JsonResponse($response, Response::HTTP_CREATED);
    }
    /**
     * @Route("/ingredients/{id}", name="show",methods="GET")
     */
    public function show(int $id): Response
    {
        $ingredients = $this->getDoctrine()
            ->getRepository(Ingredients::class)
            ->find($id);

        if (!$ingredients) {
            throw $this->createNotFoundException(
                'No ingredients found for id '.$id
            );
        }
        
        return new Response('Check out this ingredients: '.$ingredients->getNom());
    }

    /**
     * @Route("/ingredients/{id}",name="update", methods="PUT")
     */
    public function update(int $id ,Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        
         $ingredients = $this->getDoctrine()
         ->getRepository(Ingredients::class)
         ->find($id);
         if (!$ingredients) {
            throw $this->createNotFoundException(
                'No ingredients found for id '.$id
            );
        }
    $form = $this->createForm(IngredientsType::class,$ingredients);
    $form-> handleRequest($request);
    $form->submit($data);
    if($form->isSubmitted() && $form->isValid()){
      

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();}
        $response = array(

            'code' => 0,
            'message' => 'update with success!',
            'errors' => null, 
            'result' => null
    
        );
          return new JsonResponse($response, Response::HTTP_CREATED);
         
    }

  /**
   
     * @Route("/ingredients/{id}", name="deleteIngredients",methods={"DELETE"})
     *

     */
    public function deleteIngredients($id):JsonResponse
    {

        $ingredients = new Ingredients();
        $entityManager = $this->getDoctrine()->getManager();
        $ingredients = $this->getDoctrine()
        ->getRepository(Ingredients::class)
        ->find($id);
      if (!$ingredients) {
        throw $this->createNotFoundException(
            'No ingredients found for id '.$id
        );}
        $entityManager->remove($ingredients);
        $entityManager->flush();
        return new JsonResponse(['status' => 'ingredients deleted']);
    }
}
