<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Card;
use App\Entity\Answer;

/**
* @Route("/api")
*/
class CardController extends AbstractController
{
    /**
     * @Route("/cards/{card}/answer", name="card_answer", methods={"POST"})
     */
    public function answer(Request $req, Card $card): Response
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

        if ($deck->getOwner()->getId() != $user->getId()) {
          $message = ["message" => "You are not authorized"];
          $res = new JsonResponse($message);
          $res->setStatus(401);
          return $res;
        }
        $data = $req->toArray();
        $isSuccess = $data["type"] === "success";
        $isFailure = $data["type"] === "failure";
        $answers = $card->getAnswers()->toArray();
        $answers = array_filter($answers,
            fn($answer) => $answer->getOwner()->getId() === $user->getId()
        );
        $answer = null;
        if (count($answers) === 0) {
            $answer = new Answer();
            $answer->setOwner($user);
            $card->addAnswer($answer);
        } else {
            $answer = $answers[0];
        }
        if ($isSuccess) {
            $answer->setSuccess($answer->getSuccess() + 1);
        }
        if ($isFailure) {
            $answer->setFailure($answer->getFailure() + 1);
        }
        $last = $isSuccess ? true : false;
        $list = $answer->getList();
        $list[] = $last;
        $answer->setList($list);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($card);
        $entityManager->persist($answer);
        $entityManager->flush();

        $res = [
            "card" => $card->toJson()
        ];

        return new JsonResponse($res);
    }
}
