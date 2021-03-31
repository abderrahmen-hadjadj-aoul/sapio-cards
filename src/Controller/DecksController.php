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
     * @Route("/decks", name="decks_find", methods={"GET"})
     */
    public function index(DeckRepository $deckRepo): Response
    {

        $user = $this->getUser();

        $decks = $deckRepo->findBy(['published' => true, 'last' => true]);
        $res = [
            "decks" => array_map(fn($deck) => $deck->toJson(), $decks)
        ];

        return new JsonResponse($res);

    }

    /**
     * @Route("/decks/mine", name="decks_find_mine", methods={"GET"})
     */
    public function mine(DeckRepository $deckRepo): Response
    {

        $user = $this->getUser();

        $decks = $deckRepo->findBy(['owner' => $user, 'published' => false]);
        $options = [
            "publishedDecks" => true
        ];
        $res = [
            "decks" => array_map(fn($deck) => $deck->toJson($options), $decks)
        ];

        return new JsonResponse($res);

    }

    /**
     * @Route("/decks/favorites", name="decks_favorites", methods={"GET"})
     */
    public function favorites(DeckRepository $deckRepo): Response
    {

        $user = $this->getUser();

        $decks = $user->getFavorites()->toArray();
        $res = [
            "decks" => array_map(fn($deck) => $deck->toJson(), $decks)
        ];

        return new JsonResponse($res);

    }

    /**
     * @Route("/decks/favorites", name="decks_add_favorite", methods={"POST"})
     */
    public function addFavorites(Request $req, DeckRepository $deckRepo): Response
    {

        $user = $this->getUser();

        $data = $req->toArray();
        $deckid = isset($data['deckid']) ? $data['deckid'] : null;

        if (!$deckid) {
            $body = ["message" => "Missing 'deckid' field"];
            $res = new JsonResponse($body);
            $res->setStatusCode(400);
            return $res;
        }

        $deck = $deckRepo->find($deckid);
        $user->addFavorite($deck);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        $res = [
            "done" => true
        ];

        return new JsonResponse($res);

    }

    /**
     * @Route("/decks/favorites/{deck}", name="decks_delete_favorite", methods={"DELETE"})
     */
    public function deleteFavorites(Request $req, Deck $deck): Response
    {

        $user = $this->getUser();

        $user->removeFavorite($deck);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        $res = [
            "done" => true
        ];

        return new JsonResponse($res);

    }

    /**
     * @Route("/decks/{deck}", name="decks_find_by_id", methods={"GET"})
     */
    public function findOne(DeckRepository $deckRepo, Deck $deck): Response
    {

        if (!$deck) {
            $body = ["message" => "Deck not found"];
            $res = new JsonResponse($body);
            $res->setStatusCode(404);
            return $res;
        }

        $user = $this->getUser();

        if ($deck->getOwner()->getId() != $user->getId()) {
          $message = ["message" => "You are not authorized"];
          $res = new JsonResponse($message);
          $res->setStatusCode(401);
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

        $isOwner = $user->getId() === $deck->getOwner()->getId();
        if (!$isOwner) {
            $body = ["message" => "You are not authorized to update this deck"];
            $res = new JsonResponse($body);
            $res->setStatusCode(401);
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
     * @Route("/decks/{deck}", name="decks_delete", methods={"DELETE"})
     */
    public function deleteDeck(Request $req, UserRepository $userRepo, Deck $deck): Response
    {

        if (!$deck) {
            $body = ["message" => "Deck with not found"];
            $res = new JsonResponse($body);
            $res->setStatusCode(404);
            return $res;
        }

        $user = $this->getUser();

        $isOwner = $user->getId() === $deck->getOwner()->getId();
        if (!$isOwner) {
            $body = ["message" => "You are not authorized to delete this deck"];
            $res = new JsonResponse($body);
            $res->setStatusCode(401);
            return $res;
        }

        // Check published decks
        $countPublished = $deck->getDecks()->count();
        $hasPublishedDecks = $countPublished > 0;
        if ($hasPublishedDecks) {
            $body = ["message" => "Deck have " . $countPublished . " decks(s) published."
            . " You can't delete this deck."];
            $res = new JsonResponse($body);
            $res->setStatusCode(403);
            return $res;
        }

        $entityManager = $this->getDoctrine()->getManager();

        $entityManager->remove($deck);
        $entityManager->flush();

        $res = [
            "deleted" => true
        ];

        return new JsonResponse($res);

    }

    /**
     * @Route("/decks/{deck}/cards", name="decks_find_by_id_with_cards", methods={"GET"})
     */
    public function findOneWithCard(DeckRepository $deckRepo, Deck $deck): Response
    {

        if (!$deck) {
            $body = ["message" => "Card not found"];
            $res = new JsonResponse($body);
            $res->setStatusCode(404);
            return $res;
        }

        $user = $this->getUser();
        $isPrivate = !$deck->getPublished();

        if ($isPrivate && $deck->getOwner()->getId() != $user->getId()) {
            $message = ["message" => "You are not authorized to browse this deck"];
            $res = new JsonResponse($message);
            $res->setStatusCode(401);
            return $res;
        }

        $res = [
            "deck" => $deck->toJson(["cards" => true, "publishedDecks" => true]),
        ];

        $favorite = $user->getFavorites()->contains($deck);
        $res["deck"]["favorite"] = $favorite;

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

        $isOwner = $user->getId() === $deck->getOwner()->getId();
        if (!$isOwner) {
            $body = ["message" => "You are not authorized to update this deck"];
            $res = new JsonResponse($body);
            $res->setStatusCode(401);
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

        $entityManager->persist($card);
        $entityManager->flush();

        $res = [
            "card" => $card->toJson()
        ];

        return new JsonResponse($res);
    }

    /**
     * @Route("/cards/{card}", name="card_get", methods={"GET"})
     */
    public function getCard(Request $req,
      DeckRepository $deckRepo,
      UserRepository $userRepo,
      Card $card): Response
    {

        if (!$card) {
            $body = ["message" => "Card not found"];
            $res = new JsonResponse($body);
            $res->setStatusCode(404);
            return $res;
        }

        $user = $this->getUser();
        $deck = $card->getDeck();

        $isOwner = $user->getId() === $deck->getOwner()->getId();
        if (!$isOwner && !$deck->getPublished()) {
            $body = ["message" => "You are not authorized to update this card"];
            $res = new JsonResponse($body);
            $res->setStatusCode(401);
            return $res;
        }

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
            $res->setStatusCode(401);
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
     * @Route("/cards/{card}", name="card_delete", methods={"DELETE"})
     */
    public function deleteCard(Request $req,
      DeckRepository $deckRepo,
      UserRepository $userRepo,
      Card $card): Response
    {
        if (!$card) {
            $body = ["message" => "Card not found"];
            $res = new JsonResponse($body);
            $res->setStatusCode(404);
            return $res;
        }

        $user = $this->getUser();
        $deck = $card->getDeck();

        $isOwner = $user->getId() === $deck->getOwner()->getId();
        if (!$isOwner || $deck->getPublished()) {
            $body = ["message" => "You are not authorized to delete this card"];
            $res = new JsonResponse($body);
            $res->setStatusCode(401);
            return $res;
        }

        $entityManager = $this->getDoctrine()->getManager();

        $entityManager->remove($card);
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

        $isOwner = $user->getId() === $deck->getOwner()->getId();
        if (!$isOwner) {
            $body = ["message" => "You are not authorized to read this deck"];
            $res = new JsonResponse($body);
            $res->setStatusCode(401);
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

        $isOwner = $user->getId() === $deck->getOwner()->getId();
        if (!$isOwner) {
            $body = ["message" => "You are not authorized to publish this deck"];
            $res = new JsonResponse($body);
            $res->setStatusCode(401);
            return $res;
        }

        $entityManager = $this->getDoctrine()->getManager();

        $decks = $deck->getDecks();
        foreach ($decks as $key => $publishedDeck) {
            $publishedDeck->setLast(false);
            $entityManager->persist($publishedDeck);
        }
        $newDeck = new Deck();
        $newDeck->setName($deck->getName());
        $newDeck->setDescription($deck->getDescription());
        $version = count($deck->getDecks()) + 1;
        $newDeck->setVersion($version);
        $newDeck->setParent($deck);
        $newDeck->setPublished(true);
        $newDeck->setLast(true);
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
