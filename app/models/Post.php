<?php

class Post
{
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function getPosts() {
        $this->db->query('SELECT *,
                            posts.id as postId,
                            users.id as userId,
                            posts.created_at as postCreated,
                            users.created_at as userCreate
                            FROM posts
                            INNER JOIN users
                            ON posts.user_id = users.id 
                            ORDER BY posts.created_at DESC
                            ');

        $results = $this->db->resultSet();

        return $results;
    }

    public function addPost($data) {
        $this->db->query('INSERT INTO posts SET `title` = :title , `user_id` = :user_id, `body` = :body ');

        //Bind values
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':user_id', $data['user_id']);
        $this->db->bind(':body', $data['body']);

        if($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }
}