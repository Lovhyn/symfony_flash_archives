<?php

namespace App\Controller;

use App\Entity\Archives;
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

        //  Récupérer les infos de l'utilisateur connecté
        $user = $this->getUser();


        //  On va chercher avec doctrine le repository de nos tâches
        //  $repository = $this->getDoctrine()->getRepository(Tasks::class);
        //  Danc ce repository, nous récupérons toutes les entrées
        $tasks = $this->repository->findBy(array('isArchived' => '0'));
        //  Affichage des données dans le var-dumper

        return $this->render('task/index.html.twig', [
            'tasks' => $tasks,
        ]);
    }

    /**
     * @Route("/task/archives", name="task_archives")
     */
    public function indexArchives(): Response
    {

        //  Récupérer les infos de l'utilisateur connecté
        $user = $this->getUser();


        //  On va chercher avec doctrine le repository de nos tâches
        //  $repository = $this->getDoctrine()->getRepository(Tasks::class);
        //  Danc ce repository, nous récupérons toutes les entrées
        $tasks = $this->repository->findBy(array('isArchived' => '1'));
        //  Affichage des données dans le var-dumper

        return $this->render('task/archives.html.twig', [
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

            $this->addFlash(
                'text-primary',
                'La modification s\'est bien effectuée !'
            );

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

        $this->addFlash(
            'warning',
            'La suppression s\'est bien effectuée !'
        );

        return $this->redirectToRoute("task_listing");
    }

    /**
     * @Route("/task/archive/{id}", name="task_archive", requirements={"id"="\d+"})
     * @return Response
     */
    public function archiveTask(Tasks $task): Response
    {
        if ($this->checkDueAt($task)) {
            $task->setIsArchived(1);
            $this->manager->persist($task);
            $this->manager->flush();
            $this->addFlash(
                'success',
                'La tâche a bien été archivée !'
            );
        } else {
            $this->addFlash(
                'warning',
                'Impossible d\'archiver une tâche dont l\'échéance n\'a pas eu lieu'
            );
        }

        return $this->redirectToRoute("task_listing");
    }

    //  Vérifie si la date effective de la tâche est passée ou non.
    public function checkDueAt(Tasks $task)
    {
        $flag = false;
        $dueAt = $task->getDueAt();
        $today = new \DateTime();

        if ($today > $dueAt) {
            $flag = true;
        }
        return $flag;
    }
}
