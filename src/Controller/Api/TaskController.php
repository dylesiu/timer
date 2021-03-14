<?php

namespace App\Controller\Api;

use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\PauseRepository;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/task")
 */
class TaskController extends MainController
{
    /**
     * @Route("", name="api_get_tasks", methods={"GET"})
     */
    public function index(Request $request, TaskRepository $taskRepository): JsonResponse
    {
        $start = $request->query->get('start', null);
        $end = $request->query->get('end', null);

        $tasks = $taskRepository->findTasks($this->getUser(), [Task::STATE_ENDED], $start, $end);

        $tasks = array_map(static function (Task $task) {
            return $task->toArray();
        }, $tasks);

        return new JsonResponse([
            'tasks' => $tasks
        ]);
    }

    /**
     * @Route("/current", name="api_current_task", methods={"GET"})
     */
    public function current(TaskRepository $taskRepository, PauseRepository $pauseRepository): JsonResponse
    {
        $task = $taskRepository->findOneBy([
            'state' => [Task::STATE_RUNNING, Task::STATE_PAUSE],
            'user' => $this->getUser()
        ]);

        $pause = null;
        $breakTime = null;

        if ($task) {
            $breakTime = $task->countBreakTime();

            $pause = $pauseRepository->findOneBy([
                'task' => $task,
                'end' => null
            ]);

            if ($pause) {
                $pause = $pause->toArray();
            }

            $task = $task->toArray();
        }

        return new JsonResponse([
            'task' => $task,
            'pause' => $pause,
            'breakTime' => $breakTime
        ]);
    }

    /**
     * @Route("", name="api_create_task", methods={"POST"})
     */
    public function create(Request $request, EntityManagerInterface $entityManager)
    {
        $task = $this->newTask();

        $form = $this->createForm(TaskType::class, $task);
        $form->submit($request->toArray());

        if (!$form->isValid()) {
            $errors = $this->getErrorsFromForm($form);
            return new JsonResponse($errors, Response::HTTP_BAD_REQUEST);
        }

        $entityManager->persist($task);
        $entityManager->flush();

        return new JsonResponse($task->toArray());
    }

    /**
     * @Route("/download", name="api_download_tasks", methods={"GET"})
     */
    public function download(Request $request, TaskRepository $taskRepository)
    {
        $start = $request->query->get('start', null);
        $end = $request->query->get('end', null);

        $tasks = $taskRepository->findTasks($this->getUser(), [Task::STATE_ENDED], $start, $end);

        $rows = [];
        $rows[] = ['Nazwa', 'Opis', 'Start', 'Koniec', 'Czas (s)', 'Liczba przerw'];

        foreach ($tasks as $task) {
            $data = [
                'name' => $task->getName(),
                'description' => $task->getDescription(),
                'start' => $task->getStart()->format('Y-m-d H:i:s'),
                'end' => $task->getEnd()->format('Y-m-d H:i:s'),
                'time' => $task->getTime(),
                'pauses' => $task->countPauses(),
            ];

            $rows[] = $data;
        }

        $filename = tmpfile();
        $path = stream_get_meta_data($filename)['uri'];

        $fp = fopen($path, 'wb');
        foreach ($rows as $row)
        {
            fputcsv($fp, $row, ',');
        }
        fclose($fp);

        $content = file_get_contents($path);
        $response = new Response($content);

        $fileName = 'tasks_' . (new \DateTime())->format('Y:m:d_H:i:s') . '.csv';
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename='.basename($fileName));

        return $response;
    }

    /**
     * @Route("/stop/{id}", name="api_stop_task", methods={"POST"})
     */

    public function stop(
        int $id,
        Request $request,
        TaskRepository $taskRepository,
        PauseRepository $pauseRepository,
        EntityManagerInterface $entityManager
    ) {
        $task = $taskRepository->findOneBy([
            'id' => $id,
            'state' => [Task::STATE_RUNNING, Task::STATE_PAUSE],
            'user' => $this->getUser()
        ]);

        if (!$task) {
            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        }

        $data = $request->toArray();
        $description = $data['description'] ?? null;

        $pause = $pauseRepository->findOneBy([
            'task' => $task,
            'end' => null
        ]);

        if ($pause) {
            $pause->stop();
            $entityManager->flush();
        }

        $task->setDescription($description);
        $task->stop();
        $entityManager->persist($task);
        $entityManager->flush();

        return new JsonResponse($task->toArray());
    }

    protected function newTask(): Task
    {
        $task = new Task();
        $task->setUser($this->getUser());

        return $task;
    }
}
