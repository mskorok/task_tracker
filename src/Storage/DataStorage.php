<?php

namespace App\Storage;

use App\Model;
use PDO;

class DataStorage
{
    /**
     * @var PDO
     */
    public $pdo;

    public function __construct()
    {
        $this->pdo = new PDO('mysql:dbname=task_tracker;host=localhost', 'root', 'mike999');
    }


    /**
     * @return array
     */
    public function getProjects(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM `project`');
        $projects = [];

        while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {

            $projects[] = $row;
        }

        return $projects;
    }

    /**
     * @param int $projectId
     * @return Model\Project
     * @throws Model\NotFoundException
     */
    public function getProjectById(int $projectId): Model\Project
    {
        $stmt = $this->pdo->query('SELECT * FROM `project` WHERE `id` = ' . $projectId);

        if ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
            return new Model\Project($row);
        }

        throw new Model\NotFoundException();
    }

    /**
     * @param int $project_id
     * @param int|null $limit
     * @param int|null $offset
     * @return array
     */
    public function getTasksByProjectId(int $project_id, int $limit = null, int $offset = null): array
    {
        $_limit = '';

        if ($limit) {
            $_limit = 'LIMIT ' . $limit;
            if ($offset) {
                $_limit .= ',' . $offset;
            }
        }

        $stmt = $this->pdo->query("SELECT * FROM `task` WHERE `project_id` = {$project_id} ORDER BY `id` " . $_limit);
        $stmt->execute();

        $tasks = [];
        while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
            $tasks[] = $row;
        }

        return $tasks;
    }

    /**
     * @param string $title
     * @return object
     */
    public function createProject(string $title): object
    {
        $data = new \stdClass();
        $data->title = $title;
        $this->pdo->query("INSERT INTO `project` (`title`) VALUES ('{$title}')");
        $data->id = (int) $this->pdo->query('SELECT MAX(id) FROM task')->fetchColumn();

        return $data;
    }

    /**
     * @param array $data
     * @param int $projectId
     * @return mixed
     */
    public function createTask(array $data, int $projectId)
    {
        $data['project_id'] = $projectId;

        $fields = implode(',', array_keys($data));
        $values = implode(',', array_map(static function ($v) {
            return is_string($v) ? '"' . $v . '"' : $v;
        }, $data));

        $this->pdo->query("INSERT INTO task ({$fields}) VALUES ({$values})");
        $data['id'] = $this->pdo->query('SELECT MAX(id) FROM task')->fetchColumn();

        return $data;
    }
}
