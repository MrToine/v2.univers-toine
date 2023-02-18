<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use App\Repository\NewsRepository;
use App\Entity\News;

class HomeController extends BaseController {

	#[Route('/', 'home.index', methods: ['GET'])]
	public function index(
		NewsRepository $repository, 
		Request $request
		): Response 
	{	


		/**
		 * On récupère la liste des news dans un arrray en fixant une limite à 6 
		 * @var array
		 */
		$news = $repository->createQueryBuilder('n')
			->setMaxResults(6)
			->getQuery()
			->getResult();
		
		return $this->render($this->theme .'/home.html.twig', [
            'news' => $news
        ]);
	}
}