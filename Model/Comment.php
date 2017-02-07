<?php

namespace Model;

use App\DataBase;

class Comment extends DataBase
{
    const MYSQL_LIMIT = 2;
    const MYSQL_OFFSET = 0;

    protected $mysqli;

    public function __construct()
    {
        $this->mysqli = parent::getInstance()->getConnection();
    }

    public function create($comment, $user)
    {
        $comment = $this->mysqli->real_escape_string($comment);

        $user = $this->mysqli
            ->query('SELECT id FROM `user` WHERE `nickname`= "' . $user . '"')
            ->fetch_array(MYSQLI_ASSOC);

        $query = 'INSERT INTO `comments` (`comment`, `user_id`) VALUES ' .
            '("' . $comment . '", "' . $user['id'] . '")';

        $this->mysqli->query($query);

        $result = $this->getById($this->mysqli->insert_id);

        return $result;
    }

    public function read($offset = self::MYSQL_OFFSET)
    {
        $query = 'SELECT c.id, c.comment, c.created_at, u.nickname, u.avatar_url, u.id as user_id, COUNT(r.rating) as rating ' .
            'FROM `comments` as c ' .
            'INNER JOIN `user` as u ON u.id = c.user_id ' .
            'LEFT JOIN `rating` as r ON r.comment_id = c.id ' .
            'WHERE c.parent_cm_id IS NULL ' .
            'GROUP BY c.created_at ' .
            'ORDER BY c.created_at LIMIT '.$offset.', '.self::MYSQL_LIMIT;

        return $this->mysqli->query($query)->fetch_all(MYSQLI_ASSOC);
    }

    public function getById($id)
    {
        $query = 'SELECT c.id, c.comment, c.created_at, u.nickname, u.avatar_url, u.id as user_id, COUNT(r.rating) as rating ' .
            'FROM `comments` as c ' .
            'INNER JOIN `user` as u ON u.id = c.user_id ' .
            'LEFT JOIN `rating` as r ON r.comment_id = c.id ' .
            'WHERE c.id = ' . $id .
            ' GROUP BY c.created_at ' .
            'ORDER BY c.created_at';

        return $this->mysqli->query($query)->fetch_array(MYSQLI_ASSOC);
    }

    public function childComments(&$commentIds)
    {
        foreach ($commentIds as &$value) {
            if ($value['user_id'] == $_SESSION['id']) {
                $value['editable'] = true;
                unset($value['user_id']);
            } else {
                $value['editable'] = false;
                unset($value['user_id']);
            }

            $query = 'SELECT c.id, c.comment, c.created_at, u.nickname, u.avatar_url, u.id as user_id, COUNT(r.rating) as rating ' .
                'FROM `comments` as c ' .
                'INNER JOIN `user` as u ON u.id = c.user_id ' .
                'LEFT JOIN `rating` as r ON r.comment_id = c.id ' .
                'WHERE c.parent_cm_id = ' . $value['id'] .
                ' GROUP BY c.created_at ' .
                'ORDER BY c.created_at';

            $value['nested'] = $this->mysqli->query($query)->fetch_all(MYSQLI_ASSOC);

            if ($value['nested']) {
                $this->childComments($value['nested']);
            }
        }
    }

    public function rating($id, $user)
    {
        $user = $this->mysqli
            ->query('SELECT id FROM `user` WHERE `nickname`= "' . $user . '"')
            ->fetch_array(MYSQLI_ASSOC);

        $result = $this->mysqli
            ->query('SELECT count(id) as rating FROM `rating` WHERE `user_id`= "' . $user['id'] . '" AND comment_id="' . $id . '"')
            ->fetch_array(MYSQLI_ASSOC);

        if (!$result['rating']) {
            $query = 'INSERT INTO `rating` (`rating`, `comment_id`, `user_id`) VALUES ' .
                '(1, "' . $id . '", "' . $user['id'] . '")';
        } else {
            $query = ('DELETE FROM `rating` WHERE `user_id`= "' . $user['id'] . '" AND comment_id="' . $id . '"');
        }

        $this->mysqli->query($query);

        $count = $this->mysqli
            ->query('SELECT count(id) as rating FROM `rating` WHERE comment_id="' . $id . '"')
            ->fetch_array(MYSQLI_ASSOC);

        return $count['rating'];
    }

    public function update($id, $text)
    {
        $comment = $this->mysqli->real_escape_string($text);

        $query = 'UPDATE `comments` SET `comment` = "' . $comment . '" WHERE id = ' . $id;

        return $this->mysqli->query($query);
    }

    public function delete($id)
    {
        $query = 'DELETE FROM `comments` WHERE id = ' . $id;

        return $this->mysqli->query($query);
    }

    public function createNestedComment($user, $text, $id)
    {
        $comment = $this->mysqli->real_escape_string($text);

        $user = $this->mysqli
            ->query('SELECT id FROM `user` WHERE `nickname`= "' . $user . '"')
            ->fetch_array(MYSQLI_ASSOC);

        $query = 'INSERT INTO `comments` (`comment`, `user_id`, `parent_cm_id`) VALUES ' .
            '("' . $comment . '", "' . $user['id'] . '", "' . $id . '")';

        $this->mysqli->query($query);

        $result = $result = $this->getById($this->mysqli->insert_id);

        return $result;
    }

}