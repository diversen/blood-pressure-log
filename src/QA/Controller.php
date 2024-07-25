<?php

namespace App\QA;;

use Pebble\Attributes\Route;
use Pebble\Router\Request;
use App\AppUtils;
use App\QA\Slug;
use Diversen\Lang;
use Parsedown;
use Pebble\Exception\NotFoundException;

class Controller extends AppUtils
{

    public function __construct()
    {
        parent::__construct();
    }

    private function toMarkdown(string $str)
    {
        $parsedown = new Parsedown();
        $parsedown->setSafeMode(true);
        $markdown = $parsedown->text($str);
        return $markdown;
    }

    private function getQaArray()
    {
        $qa = file_get_contents(__DIR__ . "/content/qa.md");
        $qa = explode("\n", $qa);

        $qa_array = [];
        foreach ($qa as $title) {
            $slug = Slug::utf8SlugString($title);
            $qa_array[] = [
                'title' => $title,
                'slug' => $slug,
            ];
        }

        return $qa_array;
    }

    #[Route(path: '/qa', verbs: ['GET'])]
    public function index()
    {
        $data = [
            'title' => 'QA',
            'qa_array' => $this->getQaArray(),
        ];

        $context = $this->getContext($data);

        echo $this->twig->render('qa/index.twig', $context);
    }

    #[Route(path: '/qa/view/:slug', verbs: ['GET'], cast: ['slug' => 'string'])]
    public function view(Request $request)
    {

        $qa_array = $this->getQaArray();

        $slugs = array_column($qa_array, 'slug');
        if (!in_array($request->param('slug'), $slugs)) {
            throw new NotFoundException(Lang::translate('Page not found'));
        }

        // Get title from slug
        $key = array_search($request->param('slug'), $slugs);
        $title = $qa_array[$key]['title'];
        $slug = $request->param('slug');

        $content = file_get_contents(__DIR__ . "/content/$slug.md");
        $content = $this->toMarkdown($content);

        $data_container = $this->getDataContainer();
        $expected_slug = "/qa/view/" . $slug;

        $canonical_rel = $this->config->get('App.server_url') . $expected_slug;
        $canonical_rel_link = "<link rel='canonical' href='$canonical_rel' />";

        $description = $title;

        $data_container->setArrayData('head_elements', $canonical_rel_link . "\n");
        $data_container->setArrayData('head_elements', "<meta name='description' content='$description' />\n");
        $data_container->setArrayData('head_elements', "<meta property='og:title' content='$title' />\n");
        $data_container->setArrayData('head_elements', "<meta property='og:description' content='$description' />\n");

        $data = [
            'title' => $title,
            'content' => $content,
            'qa_array' => $qa_array,
        ];

        $context = $this->getContext($data);

        // $this->template_utils->renderPage('QA/views/view.tpl.php', $data, ['raw' => true]);
        echo $this->twig->render('qa/view.twig', $context);
    }
}
