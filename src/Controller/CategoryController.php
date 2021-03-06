<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/categories")
 */
class CategoryController extends AbstractController
{
    /**
     * @Route("/", name="categories_index")
     */
    public function index(PaginatorInterface $paginator, Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        $filter = $request->query->get('filter', '');
        $query = $em->getRepository(Category::class)->getPaginate($filter);

        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 5)
        );

        return $this->render(
            'category/index.html.twig',
            array(
                'categories' => $pagination,
                'filter' => $filter
            )
        );
    }

    /**
     * @Route("/new", name="categories_new")
     */
    public function new(Request $request): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $category = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();

            $this->addFlash('success', 'Category created successfully');
            return $this->redirectToRoute('categories_index');
        }

        return $this->render(
            'category/new.html.twig',
            array(
                'form' => $form->createView()
            )
        );
    }

    /**
     * @Route("/show/{id}", name="categories_show")
     */
    public function show(int $id): Response
    {
        $em = $this->getDoctrine()->getManager();
        $category = $em->getRepository(Category::class)->find($id);

        if (!$category) {
            $this->addFlash('error', 'The category does not exist');
            return $this->redirectToRoute('category_index');
        }

        return $this->render(
            'category/show.html.twig',
            array(
                'category' => $category
            )
        );
    }

    /**
     * @Route("/edit/{id}", name="categories_edit")
     */
    public function edit(Request $request, int $id): Response
    {
        $em = $this->getDoctrine()->getManager();
        $category = $em->getRepository(Category::class)->find($id);

        if (!$category) {
            $this->addFlash('error', 'The category does not exist');
            return $this->redirectToRoute('category_index');
        }

        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $category = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();

            $this->addFlash('success', 'Category created successfully');
            return $this->redirectToRoute('categories_index');
        }

        return $this->render(
            'category/edit.html.twig',
            array(
                'form' => $form->createView()
            )
        );
    }

    /**
     * @Route("/delete/{id}", name="categories_delete")
     */
    public function delete(int $id): Response
    {
        $em = $this->getDoctrine()->getManager();
        $category = $em->getRepository(Category::class)->find($id);

        if (!$category) {
            $this->addFlash('error', 'The category does not exist');
            return $this->redirectToRoute('category_index');
        }

        $em->remove($category);
        $em->flush();

        $this->addFlash('success', 'Category deleted successfully');
        return $this->redirectToRoute('categories_index');
    }
}
