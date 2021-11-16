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
     * @Route("/task/listing", name="task_listing")
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

        $task->setCreatedAt(new \DateTime());

        $form  = $this->createForm(TaskType::class, $task, []);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //  Si le formulaire est rempli et qu'il est valide :

            //  Facultatif car déjà fait automatiquement
            /* 
            $task->setName($form['name']->getData())
                ->setDescription($form['description']->getData())
                ->setDueAt($form['dueAt']->getData())
                ->setTag($form['tag']->getData());
            */

            $manager = $this->getDoctrine()->getManager();
            $manager->persist($task);
            $manager->flush();

            return $this->redirectToRoute('task_listing');
        }

        return $this->render('task/create.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
