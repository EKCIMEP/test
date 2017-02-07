<?php
namespace Controller;

use Model\Comment;

class CommentController extends Controller
{
    public function createAction()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'])) {
            $comment = new Comment();

            $user = $this->getUser();
            $text = $_POST['comment'];

            $result = $comment->create($text, $user);

            $result['nested'] = [];
            $result['editable'] = true;

            exit(json_encode(['rating' => $result]));
        }
    }

    public function ratingAction()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment']))
        {
            $comment = new Comment();

            $user = $this->getUser();
            $commentId = $_POST['comment'];

            $count = $comment->rating($commentId, $user);

            exit(json_encode(['rating' => $count]));
        }
    }

    public function updateAction()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment']))
        {
            $comment = new Comment();

            $commentId = $_POST['comment'];
            $text = $_POST['text'];

            $result = $comment->update($commentId, $text);

            exit(json_encode(['rating' => $result]));
        }
    }

    public function deleteAction()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment']))
        {
            $comment = new Comment();

            $commentId = $_POST['comment'];

            $count = $comment->delete($commentId);

            exit(json_encode(['rating' => $count]));
        }
    }

    public function createNestedCommentAction()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment']))
        {
            $comment = new Comment();

            $user = $this->getUser();
            $text = $_POST['text'];
            $commentId = $_POST['comment'];

            $result = $comment->createNestedComment($user, $text, $commentId);

            $result['nested'] = [];
            $result['editable'] = true;

            exit(json_encode(['rating' => $result]));
        }
    }
}