<?php


namespace App\Handlers;


use Doctrine\ORM\Query;
use Hateoas\Representation\CollectionRepresentation;
use Hateoas\Representation\PaginatedRepresentation;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiPaginatorHandler
{
    /**
     * @var PaginatorInterface
     */
    private $pager;

    /**
     * ApiPaginatorHandler constructor.
     * @param PaginatorInterface $pager
     */
    public function __construct(PaginatorInterface $pager)
    {
        $this->pager = $pager;
    }

    public function paginate(Request $request, Query $query)
    {
        $page = $this->pager->paginate(
            $query,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 12)
        );

        if ($page->count() > 0) {
            return new PaginatedRepresentation(
                new CollectionRepresentation($page->getItems()),
                'product_index', // route
                array(), // route parameters
                $page->getCurrentPageNumber(),       // page number
                $page->getItemNumberPerPage(),      // limit
                $page->count(),       // total pages
                'page',  // page route parameter name, optional, defaults to 'page'
                'limit', // limit route parameter name, optional, defaults to 'limit'
                false,   // generate relative URIs, optional, defaults to `false`
                $page->getTotalItemCount()       // total collection size, optional, defaults to `null`
            );
        }

        return false;
    }
}