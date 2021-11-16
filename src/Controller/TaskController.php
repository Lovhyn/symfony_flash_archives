<?php

namespace App\Controller;

use App\Entity\Tasks;
use App\Form\TaskType;
use App\Repository\TasksRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class TaskController extends AbstractController
{
    /**
     * @var TasksRepository
     */
    private $repository;

    /**
     * @var EntityManagerInterface
     */
    private $manager;

    public function __construct(TasksRepository $repository, EntityManagerInterface $manager)
    {
        $this->repository = $repository;
        $this->manager = $manager;
    }



    /**
     * @Route("/task/listing", name="task_listing")
     */
    public function index(): Response
    {

        //  On va chercher avec doctrine le repository de nos tâches
        //  $repository = $this->getDoctrine()->getRepository(Tasks::class);
        //  Danc ce repository, nous récupérons toutes les entrées
        $tasks = $this->repository->findAll();
        //  Affichage des données dans le var-dumper

        // dd($tasks);

        return $this->render('task/index.html.twig', [
            'tasks' => $tasks,
        ]);
    }

    /**
     * @Route("/task/create", name="task_create") 
     * @Route("/task/update/{id}", name="task_update", requirements={"id"="\d+"})
     */
    public function task(Tasks $task = null, Request $request)
    {

        if (!$task) {
            //  Nouvel objet Tasks
            $task = new Tasks;
            $task->setCreatedAt(new \DateTime());
        }
        $form  = $this->createForm(TaskType::class, $task, []);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            //  Facultatif car se fait automatiquement
            /* 
            $task->setName($form['name']->getData())
                ->setDescription($form['description']->getData())
                ->setDueAt($form['dueAt']->getData())
                ->setTag($form['tag']->getData());
            */

            // $manager = $this->getDoctrine()->getManager();
            $this->manager->persist($task);
            $this->manager->flush();

            return $this->redirectToRoute('task_listing');
        }
        return $this->render('task/create.html.twig', [
            'form' => $form->createView()
        ]);
    }


    /**
     * @Route("/task/delete/{id}", name="task_delete", requirements={"id"="\d+"})
     * @return Response
     */
    public function deleteTask(Tasks $task): Response
    {
        $this->manager->remove($task);
        $this->manager->flush();

        return $this->redirectToRoute("task_listing");
    }
}
