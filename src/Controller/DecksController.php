<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

use App\Repository\DeckRepository;
use App\Repository\UserRepository;
use App\Entity\Deck;

/**
 * @Route("/api")
 */
class DecksController extends AbstractController
{
    /**
     * @Route("/decks/mine", name="decks_find_mine", methods={"GET"})
     */
    public function index(DeckRepository $deckRepo): Response
    {
        $decks = $deckRepo->findAll();
        $res = [
            "decks" => array_map(fn($deck) => $deck->toJson(), $decks)
        ];
        return new JsonResponse($res);
    }

    /**
     * @Route("/decks/{deck}", name="decks_find_by_id", methods={"GET"})
     */
    public function findOne(DeckRepository $deckRepo, Deck $deck): Response
    {
        $res = [
            "deck" => $deck->toJson()
        ];
        return new JsonResponse($res);
    }

    /**
     * @Route("/decks", name="decks_create", methods={"POST"})
     */
    public function create(Request $req, UserRepository $userRepo): Response
    {

        $user = $userRepo->findAll()[0];

        $data = $req->toArray();
        $name = $data['name'];
        $description = $data['description'];
        $entityManager = $this->getDoctrine()->getManager();

        $deck = new Deck();
        $deck->setOwner($user);
        $deck->setName($name);
        $deck->setDescription($description);

        $entityManager->persist($deck);
        $entityManager->flush();

        $res = [
            "deck" => $deck->toJson()
        ];

        return new JsonResponse($res);

    }
}
