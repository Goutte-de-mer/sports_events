<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Service\EventService;
use App\Repository\EventRepository;

final class EventController extends AbstractController
{
    private $eventService;
    public function __construct(EventService $eventService)
    {
        $this->eventService = $eventService;
    }
    #[Route('/event', methods: ['POST'])]
    public function createEvent(Request $request): JsonResponse
    {

        try {

            $requestData = $request->toArray();

            // Validate required fields
            $validationErrors = $this->eventService->validateEventData($requestData);
            if (!empty($validationErrors)) {
                return $this->json([
                    'error' => $validationErrors[0],
                ], 400);
            }

            $event = $this->eventService->createEvent($requestData);


            return $this->json(["event" => $event, "message" => "succès"], 201, [], ['groups' => 'event:read']);
        } catch (\Exception $e) {
            return $this->json(["message" => "Erreur lors de la création de l'évènement", "error" => $e->getMessage()], 500);
        }
    }

    #[Route('/delete-event/{id}', methods: ['DELETE'])]
    public function deleteEvent($id, Request $request): JsonResponse
    {
        try {

            $requestData = $request->toArray();
            $userId = $requestData['user_id'];

            $this->eventService->deleteEvent($id, $userId);
            return $this->json(["message" => "Évènement supprimé avec succès"], 200);
        } catch (\Exception $e) {
            return $this->json(["message" => "Erreur lors de la suppression de l'évènement", "error" => $e->getMessage()], 500);
        }
    }

    #[Route("/events", methods: ["GET"])]
    public function getUpcommingEvents(EventRepository $eventRepository): JsonResponse
    {
        try {

            $events = $eventRepository->findUpcomingEvents();

            return $this->json(["events" => $events, "message" => "succès"], 200, [], ['groups' => 'event:read']);
        } catch (\Exception $e) {
            return $this->json(["message" => "Erreur lors de la récupération des évènements", "error" => $e->getMessage()], 500);
        }
    }

    #[Route("/event/{id}/register", methods: ["POST"])]
    public function registerForEvent($id, Request $request): JsonResponse
    {
        try {

            $requestData = $request->toArray();
            $userId = $requestData['user_id'];

            $this->eventService->registerForEvent($id, $userId);
            return $this->json(["message" => "Inscription réussie pour l'évènement"], 200, [], ['groups' => 'event:read']);
        } catch (\Exception $e) {
            return $this->json(["message" => "Erreur lors de l'inscription pour l'évènement", "error" => $e->getMessage()], 500);
        }
    }
}
