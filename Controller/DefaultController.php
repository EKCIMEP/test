<?php
namespace Controller;

use Model\Comment;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $comment = new Comment();

        $this->data['comments'] = $comment->read();

        $comment->childComments($this->data['comments']);

        extract($this->data);

        return include($this->rootDir . '/../View/index.html');
    }

    public function loadAction()
    {
        $comment = new Comment();

        $comments = $comment->read($_POST['offset']);

        $comment->childComments($comments);

        exit(json_encode(['comments' => $comments]));
    }
}