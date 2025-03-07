<?php

namespace App\Service;

use App\Entity\Event;
use App\Entity\Participant;
use App\Repository\EventRepository;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;

class EventService
{
    private EntityManagerInterface $entityManager;
    private EventRepository $eventRepository;
    private ParticipantRepository $participantRepository;


    public function __construct(EntityManagerInterface $entityManager, EventRepository $eventRepository, ParticipantRepository $participantRepository)
    {
        $this->entityManager = $entityManager;
        $this->eventRepository = $eventRepository;
        $this->participantRepository = $participantRepository;
    }

    /**
     * Valide les données d'un évènement avant création.
     *
     * @param array $data
     * @return array Retourne un tableau d'erreurs, vide si aucune erreur.
     */
    public function validateEventData(array $data): array
    {
        $errors = [];

        if (!isset($data['title']) || empty($data['title'])) {
            $errors[] = 'Le titre de l\'évènement est requis';
        }

        if (!isset($data['date']) || empty($data['date'])) {
            $errors[] = 'La date de l\'évènement est requise';
        }
        if (!isset($data['location']) || empty($data['location'])) {
            $errors[] = 'L\'emplacement de l\'évènement est requis';
        }

        return $errors;
    }

    /**
     * Crée un nouvel évènement et l'enregistre en base de données.
     *
     * @param array $data Données de l'évènement
     * @return Events Le livre créé.
     */
    public function createEvent(array $data): Event
    {
        $date = new \DateTime($data['date']);
        $user = $this->participantRepository->find($data['user_id']);

        if (!$user) {
            throw new \Exception("L'utilisateur avec l'ID {$data['user_id']} n'existe pas.");
        }

        $event = new Event();
        $event->setTitle($data['title']);
        $event->setDescription($data['description']);
        $event->setDate($date);
        $event->setMaxNumberParticipants($data['max']);
        $event->setLocation($data['location']);
        $event->addParticipant($user);

        $this->entityManager->persist($event);
        $this->entityManager->flush();

        return $event;
    }

    /**
     * Supprime un évènement
     * @param int $data id de l'évènement
     */
    public function deleteEvent(int $id, int $userId): void
    {
        $event = $this->eventRepository->find($id);
        if (!$event) {
            throw new \Exception('Evènement introuvable');
        }
        $creatorId = $event->getParticipants()->first()->getId();
        if ($creatorId !== $userId) {
            throw new \Exception('Seul le créateur de l\'évènement peut le supprimer');
        }

        $this->entityManager->remove($event);
        $this->entityManager->flush();
    }

    public function registerForEvent($id, $userId): Event
    {
        $event = $this->eventRepository->find($id);
        if (!$event) {
            throw new \Exception('Evènement introuvable');
        }
        $participant = $this->participantRepository->find($userId);
        if (!$participant) {
            throw new \Exception('Participant introuvable');
        }
        if ($event->getMaxNumberParticipants() !== null && $event->getMaxNumberParticipants() <= count($event->getParticipants())) {
            throw new \Exception('L\'évènement est complet');
        }
        $event->addParticipant($participant);
        $this->entityManager->persist($event);
        $this->entityManager->flush();
        return $event;
    }
}
