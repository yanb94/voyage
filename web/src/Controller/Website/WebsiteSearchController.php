<?php 

namespace App\Controller\Website;

use Massive\Bundle\SearchBundle\Search\SearchManagerInterface;
use Sulu\Bundle\WebsiteBundle\Resolver\ParameterResolverInterface;
use Sulu\Component\Rest\RequestParametersTrait;
use Sulu\Component\Webspace\Analyzer\RequestAnalyzerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Twig\Environment;

/**
 * This controller handles the search for the website.
 */
class WebsiteSearchController
{
    use RequestParametersTrait;

    /**
     * @var SearchManagerInterface
     */
    private $searchManager;

    /**
     * @var RequestAnalyzerInterface
     */
    private $requestAnalyzer;

    /**
     * @var ParameterResolverInterface
     */
    private $parameterResolver;

    /**
     * @var Environment
     */
    private $twig;

    /**
     * @var string[]
     */
    private $indexes;


    public function __construct(
        SearchManagerInterface $searchManager,
        RequestAnalyzerInterface $requestAnalyzer,
        ParameterResolverInterface $parameterResolver,
        Environment $twig,
        public int $maxItem,
        array $indexes = [],
    ) {
        $this->searchManager = $searchManager;
        $this->requestAnalyzer = $requestAnalyzer;
        $this->parameterResolver = $parameterResolver;
        $this->twig = $twig;
        $this->indexes = $indexes;
    }

    /**
     * Returns the search results for the given query.
     *
     * @return Response
     */
    public function queryAction(Request $request)
    {
        $query = $this->getRequestParameter($request, 'q', true, "");

        $locale = $this->requestAnalyzer->getCurrentLocalization()->getLocale();
        $webspace = $this->requestAnalyzer->getWebspace();
        $template = $webspace->getTemplate('search', $request->getRequestFormat());

        if($query == "")
        {
            $response = new Response($this->twig->render(
                $template,
                $this->parameterResolver->resolve(
                    ['query' => false, 'hits' => null, 'nb' => 0, 'page' => 0, 'maxPage' => 0],
                    $this->requestAnalyzer
                )
            ));
        } 
        else
        {
            $page = intval($this->getRequestParameter($request, 'p', true, 1));

            $queryString = '';
            // Reference only article templates
            $queryString .= '+(_structure_type:"article")';
            //
            if (\strlen($query) < 3) {
                $queryString .= '+("' . self::escapeDoubleQuotes($query) . '") ';
            } else {
                $queryValues = \explode(' ', $query);
                foreach ($queryValues as $queryValue) {
                    if (\strlen($queryValue) > 2) {
                        $queryString .= '+("' . self::escapeDoubleQuotes($queryValue) . '" OR ' .
                            \preg_replace('/([^\pL\s\d])/u', '?', $queryValue) . '* OR ' .
                            \preg_replace('/([^\pL\s\d])/u', '', $queryValue) . '~) ';
                    } else {
                        $queryString .= '+("' . self::escapeDoubleQuotes($queryValue) . '") ';
                    }
                }
            }

            $nb = $this->searchManager
            ->createSearch($queryString)
            ->locale($locale)
            ->indexes(
                \str_replace(
                    '#webspace#',
                    $webspace->getKey(),
                    $this->indexes
                )
            )->execute()->getTotal();

            $maxPage = ceil($nb/$this->maxItem);

            $hits = $this->searchManager
                ->createSearch($queryString)
                ->locale($locale)
                ->indexes(
                    \str_replace(
                        '#webspace#',
                        $webspace->getKey(),
                        $this->indexes
                    )
                )
                ->setOffset(($page - 1)*$this->maxItem)
                ->setLimit($this->maxItem)
                ->execute();

            

            if (!$this->twig->getLoader()->exists($template)) {
                throw new NotFoundHttpException();
            }

            $response = new Response($this->twig->render(
                $template,
                $this->parameterResolver->resolve(
                    ['query' => $query, 'hits' => $hits, 'nb' => $nb, 'page' => $page, 'maxPage' => $maxPage],
                    $this->requestAnalyzer
                )
            ));

        }

        // we need to set the content type ourselves here
        // else symfony will use the accept header of the client and the page could be cached with false content-type
        // see following symfony issue: https://github.com/symfony/symfony/issues/35694
        $mimeType = $request->getMimeType($request->getRequestFormat());
        if ($mimeType) {
            $response->headers->set('Content-Type', $mimeType);
        }

        return $response;
    }

    /**
     * Returns the string with escaped quotes.
     *
     * @param string $query
     *
     * @return string
     */
    private static function escapeDoubleQuotes($query)
    {
        return \str_replace('"', '\\"', $query);
    }
}