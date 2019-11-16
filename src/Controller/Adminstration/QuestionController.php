<?php

namespace App\Controller\Adminstration;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class QuestionController.
 *
 * @Route("/admin/questions")
 */
class QuestionController extends AbstractController
{
    /**
     * @Route("/")
     */
    public function index(Request $request, EntityManagerInterface $em)
    {
        return new Response('HELLO');
    }
}
