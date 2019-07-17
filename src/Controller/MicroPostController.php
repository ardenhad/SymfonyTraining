<?php


namespace App\Controller;

use App\Entity\MicroPost;
use App\Repository\MicroPostRepository;
use App\Form\MicroPostType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * @Route("micro-post")
 */
class MicroPostController
{
    private $twig;
    private $microPostRepository;
    private $formFactory;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(
        \Twig_Environment $twig,
        MicroPostRepository $microPostRepository,
        FormFactoryInterface $formFactory,
        EntityManagerInterface $entityManager,
        RouterInterface $router
    ) {
        $this->twig = $twig;
        $this->microPostRepository = $microPostRepository;
        $this->formFactory = $formFactory;
        $this->entityManager = $entityManager;
        $this->router = $router;
    }

    /**
     * @Route ("/", name="micro_post_index")
     */
    public function index()
    {
        $html = $this->twig->render("micro-post/index.html.twig", [
            "posts" => $this->microPostRepository->findAll()
        ]);

        return new Response($html);
    }

    /**
     * @Route("/add", name="micro_post_add")
     */
    public function add(Request $request)
    {
        $microPost = new MicroPost();
        $microPost->setTime(new \DateTime());

        $form = $this->formFactory->create(MicroPostType::class, $microPost);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($microPost);
            $this->entityManager->flush();

            return new RedirectResponse($this->router->generate("micro_post_index"));
        }

        $html = $this->twig->render(
            "micro-post/add.html.twig",
            ["form" => $form->createView()]
        );

        return new Response($html);
    }

    /*
     * IMPORTANT
     *  This has to be at the end because since this can take any value, including an id value = "add",
     *  a confusion is caused and add becomes inaccessible since it would be fetched by /{id} with id = "add".
     *  In order to solve this issue, we put it afterwards so if it is micro-post/add, then it will be caught by the route
     *  at top otherwise, it will come here. Since this has lesser priority.
     *
     *  Alternatively, a requirement such as id has to be of type integer can be another solution?
     */
    /**
     * @Route("/{id}", name="micro_post_post")
     */
    /*
     * Symfony feature called paramConverter. Instead of passing id and looking by id...
     */
    public function post(MicroPost $post)
    {
        //... we can just pass the entity and it autoExec the bottom.(Appears in panel/doctrine)
        //$post = $this->microPostRepository->find($id);

        return new Response(
            $this->twig->render(
                "micro-post/post.html.twig",
                ["post" => $post]
            )
        );
    }
}