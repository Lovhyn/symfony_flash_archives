<?php

namespace App\Controller;

use App\Entity\Task;
use App\Entity\Tasks;
use App\Form\TaskType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class TaskController extends AbstractController
{
    /**
     * @Route("/task/listing", name="task")
     */
    public function index(): Response
    {

        //  On va chercher avec doctrine le repository de nos tâches
        $repository = $this->getDoctrine()->getRepository(Tasks::class);
        //  Danc ce repository, nous récupérons toutes les entrées
        $tasks = $repository->findAll();
        //  Affichage des données dans le var-dumper

        // dd($tasks);

        return $this->render('task/index.html.twig', [
            'tasks' => $tasks,
        ]);
    }

    /**
     * Undocumented function
     *
     * @Route("/task/create", name="task_create") 
     */
    public function createTask(Request $request)
    {
        //  Nouvel objet Tasks
        $task = new Tasks;

        $form  = $this->createForm(TaskType::class, $task, []);

        return $this->render('task/create.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
