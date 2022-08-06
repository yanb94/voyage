<?php

namespace App\Admin;

use App\Entity\Comment;
use Sulu\Bundle\AdminBundle\Admin\Admin;
use Sulu\Bundle\AdminBundle\Admin\View\ToolbarAction;
use Sulu\Bundle\AdminBundle\Admin\View\ViewCollection;
use Sulu\Bundle\AdminBundle\Admin\Navigation\NavigationItem;
use Sulu\Bundle\AdminBundle\Admin\View\ViewBuilderFactoryInterface;
use Sulu\Bundle\AdminBundle\Admin\Navigation\NavigationItemCollection;

class CommentAdmin extends Admin
{
    const COMMENT_FORM_KEY = 'comment_details';
    const COMMENT_LIST_VIEW = "app.comments_list";
    const COMMENT_EDIT_FORM_VIEW = 'app.event_edit_form';

    /**
     * @var ViewBuilderFactoryInterface
     */
    private $viewBuilderFactory;

    public function __construct(ViewBuilderFactoryInterface $viewBuilderFactory)
    {
        $this->viewBuilderFactory = $viewBuilderFactory;
    }

    public function configureNavigationItems(NavigationItemCollection $navigationItemCollection): void
    {
        $eventNavigationItem = new NavigationItem('Commentaires');
        $eventNavigationItem->setView(static::COMMENT_LIST_VIEW);
        $eventNavigationItem->setIcon('fa-comments');
        $eventNavigationItem->setPosition(30);

        $navigationItemCollection->add($eventNavigationItem);
    }

    public function configureViews(ViewCollection $viewCollection): void
    {
        $listView = $this->viewBuilderFactory->createListViewBuilder(static::COMMENT_LIST_VIEW, "/comments")
            ->setResourceKey(Comment::RESOURCE_KEY)
            ->setListKey('comments')
            ->addListAdapters(['table'])
            ->setEditView(static::COMMENT_EDIT_FORM_VIEW)
            ->addToolbarActions([new ToolbarAction('sulu_admin.delete')])
            ;

        $viewCollection->add($listView);

        $editFormView = $this->viewBuilderFactory->createResourceTabViewBuilder(static::COMMENT_EDIT_FORM_VIEW, '/comments/:id')
            ->setResourceKey(Comment::RESOURCE_KEY)
            ->setBackView(static::COMMENT_LIST_VIEW);

        $viewCollection->add($editFormView);

        
        $editDetailsFormView = $this->viewBuilderFactory->createFormViewBuilder(static::COMMENT_EDIT_FORM_VIEW . '.details', '/details')
            ->setResourceKey(Comment::RESOURCE_KEY)
            ->setFormKey(static::COMMENT_FORM_KEY)
            ->setTabTitle('sulu_admin.details')
            ->addToolbarActions([new ToolbarAction('sulu_admin.save'), new ToolbarAction('sulu_admin.delete')])
            ->setParent(static::COMMENT_EDIT_FORM_VIEW);

        $viewCollection->add($editDetailsFormView);
    }
}