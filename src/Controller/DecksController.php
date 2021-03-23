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

        $decks = $deckRepo->findBy(['owner' => $user, 'published' => false]);
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

        $isOwner = $user->getId() === $card->getOwner()->getId();
        if (!$isOwner) {
            $body = ["message" => "You are not authorized to update this card"];
            $res = new JsonResponse($body);
            $res->setStatus(401);
            return $res;
        }

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
            "deck" => $deck->toJson(["cards" => true, "publishedDecks" => true]),
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

        $isOwner = $user->getId() === $card->getOwner()->getId();
        if (!$isOwner) {
            $body = ["message" => "You are not authorized to update this card"];
            $res = new JsonResponse($body);
            $res->setStatus(401);
            return $res;
        }

        $data = $req->toArray();
        $question = $data["question"];
        $answer = $data["answer"];
        $entityManager = $this->getDoctrine()->getManager();

        $card = new Card();
        $card->setDeck($deck);
        $card->setQuestion($question);
        $card->setAnswer($answer);
        $card->setOwner($user);

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
        $deck = $card->getDeck();

        $isOwner = $user->getId() === $deck->getOwner()->getId();
        if (!$isOwner) {
            $body = ["message" => "You are not authorized to update this card"];
            $res = new JsonResponse($body);
            $res->setStatus(401);
            return $res;
        }

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

    /**
     * @Route("/decks/{deck}/published", name="published_decks", methods={"GET"})
     */
    public function getPublishedDecks(Deck $deck)
    {

        $user = $this->getUser();

        $isOwner = $user->getId() === $card->getOwner()->getId();
        if (!$isOwner) {
            $body = ["message" => "You are not authorized to update this card"];
            $res = new JsonResponse($body);
            $res->setStatus(401);
            return $res;
        }

        $decks = $deck->getDecks()->toArray();
        $res = [
            "decks" => array_map(fn($deck) => $deck->toJson(), $decks)
        ];
        return null;
    }

    /**
     * @Route("/decks/{deck}/published", name="publish_deck", methods={"POST"})
     */
    public function publishDeck(Deck $deck)
    {

        $user = $this->getUser();

        $isOwner = $user->getId() === $card->getOwner()->getId();
        if (!$isOwner) {
            $body = ["message" => "You are not authorized to update this card"];
            $res = new JsonResponse($body);
            $res->setStatus(401);
            return $res;
        }

        $entityManager = $this->getDoctrine()->getManager();

        $newDeck = new Deck();
        $newDeck->setName($deck->getName());
        $newDeck->setDescription($deck->getDescription());
        $version = count($deck->getDecks()) + 1;
        $newDeck->setVersion($version);
        $newDeck->setParent($deck);
        $newDeck->setPublished(true);
        $newDeck->setOwner($deck->getOwner());

        $cards = $deck->getCards();
        foreach ($cards as $key => $card) {
            $newCard = new Card();
            $newCard->setDeck($newDeck);
            $newCard->setQuestion($card->getQuestion());
            $newCard->setAnswer($card->getAnswer());
            $entityManager->persist($newCard);
        }

        $entityManager->persist($newDeck);
        $entityManager->flush();

        $res = [
            "deck" => $newDeck->toJson()
        ];

        return new JsonResponse($res);
    }

}
