<?php

namespace App\Controller;

use App\Model;
use App\Storage\DataStorage;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Project controller
 *
 * @Route("/")
 *
 */
class ProjectController extends AbstractController
{
    /**
     * @var DataStorage
     */
    private $storage;

    public function __construct(DataStorage $storage)
    {
        $this->storage = $storage;
    }

    /**
     * @param Request $request
     *
     * @return Response
     * @Route("/", name="index", methods={"GET"})
     */
    public function indexAction(Request $request): Response
    {

        return new Response('there');
    }

    /**
     * @param Request $request
     *
     * @return Response
     * @Route("/test", name="test", methods={"GET"})
     */
    public function testAction(Request $request): Response
    {

        return new Response('test');
    }

    /**
     * @param Request $request
     *
     * @return Response
     * @Route("/projects", name="projects", methods={"GET"})
     */
    public function projectsAction(Request $request): Response
    {

        return new Response('projects');
    }

    /**
     * @param Request $request
     *
     * @return Response
     * @Route("/project/{id}", name="project", methods={"GET"})
     */
    public function projectAction(Request $request): Response
    {
        try {
            $project = $this->storage->getProjectById($request->get('id'));

            return new Response($project->jsonSerialize());
        } catch (Model\NotFoundException $e) {
            return new Response('Not found', 404);
        } catch (\Throwable $e) {
            return new Response('Something went wrong', 500);
        }
    }

    /**
     * @param Request $request
     *
     * @return Response
     * @Route("/project", name="project", methods={"POST"})
     */
    public function projectCreateAction(Request $request): Response
    {
        try {
            $project = $this->storage->createProject($request->request->get('title'));

            return new Response($project->jsonSerialize());
        } catch (Model\NotFoundException $e) {
            return new Response('Not found', 404);
        } catch (\Throwable $e) {
            return new Response('Something went wrong', 500);
        }
    }

    /**
     * @param Request $request
     *
     * @return Response
     * @Route("/project/{id}/tasks", name="project-tasks", methods={"GET"})
     */
    public function projectTaskPagerAction(Request $request): Response
    {
        $tasks = $this->storage->getTasksByProjectId(
            $request->get('id'),
            $request->get('limit'),
            $request->get('offset')
        );

        return new Response(json_encode($tasks));
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @throws Model\NotFoundException
     * @Route("/project/{id}/tasks", name="project-create-task", methods={"PUT"})
     */
    public function projectCreateTaskAction(Request $request): JsonResponse
    {
		$project = $this->storage->getProjectById($request->request->get('id'));
		if (!$project) {
			return new JsonResponse(['error' => 'Not found']);
		}
		
		return new JsonResponse(
			$this->storage->createTask($_REQUEST, $project->getId())
		);
    }
}
