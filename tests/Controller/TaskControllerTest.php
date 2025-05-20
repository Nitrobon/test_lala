<?php
// tests/Controller/TaskControllerTest.php

namespace App\Tests\Controller;

use App\Entity\Task;
use App\Enum\TaskStatus;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskControllerTest extends WebTestCase
{
    public function testGetAllTasks(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/tasks');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());

        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('status', $responseData);
        $this->assertEquals('success', $responseData['status']);
    }

    public function testCreateTask(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/tasks', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'title' => 'Test Task',
            'description' => 'Test Description',
            'status' => TaskStatus::TODO,
        ]));

        $this->assertEquals(201, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());

        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('status', $responseData);
        $this->assertEquals('success', $responseData['status']);
        $this->assertEquals('Test Task', $responseData['data']['title']);
    }

    public function testGetTaskById(): void
    {
        // Create a task first
        $client = static::createClient();
        $client->request('POST', '/api/tasks', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'title' => 'Task for GET test',
            'description' => 'Test Description',
            'status' => TaskStatus::TODO,
        ]));

        $responseData = json_decode($client->getResponse()->getContent(), true);
        $taskId = $responseData['data']['id'];

        // Now test getting the task by ID
        $client->request('GET', '/api/tasks/' . $taskId);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());

        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('status', $responseData);
        $this->assertEquals('success', $responseData['status']);
        $this->assertEquals('Task for GET test', $responseData['data']['title']);
    }

    public function testUpdateTask(): void
    {
        // Create a task first
        $client = static::createClient();
        $client->request('POST', '/api/tasks', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'title' => 'Task to update',
            'description' => 'Test Description',
            'status' => TaskStatus::TODO,
        ]));

        $responseData = json_decode($client->getResponse()->getContent(), true);
        $taskId = $responseData['data']['id'];

        // Now update the task
        $client->request('PUT', '/api/tasks/' . $taskId, [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'title' => 'Updated Task Title',
            'status' => TaskStatus::IN_PROGRESS,
        ]));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());

        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('status', $responseData);
        $this->assertEquals('success', $responseData['status']);
        $this->assertEquals('Updated Task Title', $responseData['data']['title']);
        $this->assertEquals(TaskStatus::IN_PROGRESS, $responseData['data']['status']);
    }

    public function testDeleteTask(): void
    {
        // Create a task first
        $client = static::createClient();
        $client->request('POST', '/api/tasks', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'title' => 'Task to delete',
            'description' => 'Test Description',
            'status' => TaskStatus::TODO,
        ]));

        $responseData = json_decode($client->getResponse()->getContent(), true);
        $taskId = $responseData['data']['id'];

        // Now delete the task
        $client->request('DELETE', '/api/tasks/' . $taskId);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());

        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('status', $responseData);
        $this->assertEquals('success', $responseData['status']);

        // Try to get the deleted task
        $client->request('GET', '/api/tasks/' . $taskId);
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }
}