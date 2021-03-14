<?php

namespace App\Controller\Api;

use App\Entity\Pause;
use App\Entity\Task;
use App\Repository\PauseRepository;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/pause")
 */
class PauseController extends AbstractController
{
    /**
     * @Route("/{taskId}", name="api_create_pause", methods={"POST"})
     */
    public function create(int $taskId, TaskRepository $taskRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        $task = $taskRepository->findOneBy([
            'id' => $taskId,
            'state' => [Task::STATE_RUNNING],
            'user' => $this->getUser()
        ]);

        if (!$task) {
            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        }

        $pause = new Pause();
        $pause->setTask($task);
        $task->setState(Task::STATE_PAUSE);

        $entityManager->persist($pause);
        $entityManager->flush();

        return new JsonResponse($pause->toArray());
    }

    /**
     * @Route("/end/{pauseId}", name="api_end_pause", methods={"POST"})
     */
    public function end(int $pauseId, PauseRepository $pauseRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        $pause = $pauseRepository->find($pauseId);

        if (!$pause) {
            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        }

        $task = $pause->getTask();

        if ($task->getState() !== Task::STATE_PAUSE || $task->getUser() !== $this->getUser()) {
            return new JsonResponse(null, Response::HTTP_BAD_REQUEST);
        }

        $task->setState(Task::STATE_RUNNING);
        $pause->stop();

        $entityManager->flush();

        return new JsonResponse([
            'breakTime' => $task->countBreakTime()
        ]);
    }
}
