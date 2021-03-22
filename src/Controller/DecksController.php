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
use App\Entity\Card;

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

        $user = $this->getUser();

        $decks = $deckRepo->findBy(['owner' => $user]);
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

        $user = $this->getUser();

        if ($deck->getOwner()->getId() != $user->getId()) {
          $message = ["message" => "You are not authorized"];
          $res = new JsonResponse($message);
          $res->setStatus(401);
          return $res;
        }

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

        $user = $this->getUser();

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

    /**
    * @Route("/decks/{deck}", name="decks_update", methods={"PATCH"})
     */
    public function patchDeck(Request $req, UserRepository $userRepo, Deck $deck): Response
    {

        $user = $this->getUser();

        $data = $req->toArray();
        $entityManager = $this->getDoctrine()->getManager();

        if (isset($data["name"])) {
            $deck->setName($data["name"]);
        }
        if (isset($data["description"])) {
            $deck->setDescription($data["description"]);
        }

        $entityManager->persist($deck);
        $entityManager->flush();

        $res = [
            "deck" => $deck->toJson()
        ];

        return new JsonResponse($res);

    }

    /**
     * @Route("/decks/{deck}/cards", name="decks_find_by_id_with_cards", methods={"GET"})
     */
    public function findOneWithCard(DeckRepository $deckRepo, Deck $deck): Response
    {
        $user = $this->getUser();

        if ($deck->getOwner()->getId() != $user->getId()) {
          $message = ["message" => "You are not authorized"];
          $res = new JsonResponse($message);
          $res->setStatus(401);
          return $res;
        }

        $res = [
            "deck" => $deck->toJson(["cards" => true]),
        ];
        return new JsonResponse($res);
    }

    /**
     * @Route("/decks/{deck}/cards", name="decks_add_card", methods={"POST"})
     */
    public function createCard(Request $req,
      DeckRepository $deckRepo,
      UserRepository $userRepo,
      Deck $deck): Response
    {

        $user = $this->getUser();

        $data = $req->toArray();
        $question = $data["question"];
        $answer = $data["answer"];
        $entityManager = $this->getDoctrine()->getManager();

        $card = new Card();
        $card->setDeck($deck);
        $card->setQuestion($question);
        $card->setAnswer($answer);

        $entityManager->persist($card);
        $entityManager->flush();

        $res = [
            "card" => $card->toJson()
        ];

        return new JsonResponse($res);
    }

    /**
     * @Route("/cards/{card}", name="card_update", methods={"PATCH"})
     */
    public function patchCard(Request $req,
      DeckRepository $deckRepo,
      UserRepository $userRepo,
      Card $card): Response
    {

        $user = $this->getUser();

        $data = $req->toArray();
        $question = $data["question"];
        $answer = $data["answer"];
        $entityManager = $this->getDoctrine()->getManager();

        if (isset($data["question"])) {
          $card->setQuestion($data["question"]);
        }
        if (isset($data["answer"])) {
          $card->setAnswer($data["answer"]);
        }

        $entityManager->persist($card);
        $entityManager->flush();

        $res = [
            "card" => $card->toJson()
        ];

        return new JsonResponse($res);
    }
}
