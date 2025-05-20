<?php
// src/Controller/TaskController.php

namespace App\Controller;

use App\Exception\TaskException;
use App\Service\TaskService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/tasks')]
class TaskController extends AbstractController
{
    private TaskService $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    #[Route('', methods: ['GET'])]
    public function getAllTasks(Request $request): JsonResponse
    {
        $page = max(1, $request->query->getInt('page', 1));
        $limit = max(1, min(100, $request->query->getInt('limit', 10)));
        $status = $request->query->get('status');

        $tasks = $this->taskService->getAllTasks($page, $limit, $status);

        return $this->json([
            'status' => 'success',
            'data' => $tasks,
        ]);
    }

    #[Route('/{id}', methods: ['GET'])]
    public function getTaskById(int $id): JsonResponse
    {
        $task = $this->taskService->getTaskById($id);

        return $this->json([
            'status' => 'success',
            'data' => $task->toArray(),
        ]);
    }

    /**
     * @throws TaskException
     */
    #[Route('', methods: ['POST'])]
    public function createTask(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $task = $this->taskService->createTask($data);

        return $this->json([
            'status' => 'success',
            'message' => 'Task created successfully',
            'data' => $task->toArray(),
        ], 201);
    }

    /**
     * @throws TaskException
     */
    #[Route('/{id}', methods: ['PUT'])]
    public function updateTask(int $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $task = $this->taskService->updateTask($id, $data);

        return $this->json([
            'status' => 'success',
            'message' => 'Task updated successfully',
            'data' => $task->toArray(),
        ]);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function deleteTask(int $id): JsonResponse
    {
        $this->taskService->deleteTask($id);

        return $this->json([
            'status' => 'success',
            'message' => 'Task deleted successfully',
        ]);
    }
}