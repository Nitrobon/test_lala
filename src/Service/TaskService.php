<?php

namespace App\Service;

use App\Entity\Task;
use App\Enum\TaskStatus;
use App\Exception\TaskException;
use App\Repository\TaskRepository;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TaskService
{
    private TaskRepository $taskRepository;
    private ValidatorInterface $validator;

    public function __construct(TaskRepository $taskRepository, ValidatorInterface $validator)
    {
        $this->taskRepository = $taskRepository;
        $this->validator = $validator;
    }

    public function getAllTasks(int $page = 1, int $limit = 10, ?string $status = null): array
    {
        return $this->taskRepository->findPaginated($page, $limit, $status);
    }

    /**
     * @throws TaskException
     */
    public function getTaskById(int $id): Task
    {
        $task = $this->taskRepository->find($id);
        if (!$task) {
            throw new TaskException("Task with ID $id not found", 404);
        }
        return $task;
    }

    /**
     * @throws TaskException
     */
    public function createTask(array $data): Task
    {
        $task = new Task();
        $this->hydrateTask($task, $data);

        $this->validateTask($task);

        $this->taskRepository->save($task);
        return $task;
    }

    /**
     * @throws TaskException
     */
    public function updateTask(int $id, array $data): Task
    {
        $task = $this->getTaskById($id);
        $this->hydrateTask($task, $data);
        $task->setUpdatedAt();
        $this->validateTask($task);
        $this->taskRepository->save($task);
        return $task;
    }

    /**
     * @throws TaskException
     */
    public function deleteTask(int $id): void
    {
        $task = $this->getTaskById($id);
        $this->taskRepository->remove($task);
    }

    /**
     * @throws TaskException
     */
    private function hydrateTask(Task $task, array $data): void
    {
        if (isset($data['title'])) {
            $task->setTitle($data['title']);
        }

        if (isset($data['description'])) {
            $task->setDescription($data['description']);
        }

        if (isset($data['status'])) {
            if (!TaskStatus::tryFrom($data['status'])) {
                throw new TaskException("Invalid status value: {$data['status']}", 400);
            }
            $task->setStatus(TaskStatus::from($data['status']));
        }
    }

    /**
     * @throws TaskException
     */
    private function validateTask(Task $task): void
    {
        $errors = $this->validator->validate($task);
        if (count($errors) > 0) {
            $messages = array_map(fn($e) => $e->getMessage(), iterator_to_array($errors));
            throw new TaskException(json_encode($messages), 400);
        }
    }
}