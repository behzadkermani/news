<?php

namespace Admin;

use database\Database;

class Post extends Admin
{

    public function index()
    {
        $db = new Database();
        $posts = $db->select('SELECT * FROM posts ORDER BY `id` DESC');
        require_once(BASE_PATH . '/template/admin/posts/index.php');
    }
    public function create()
    {
        $db = new Database();
        $categories = $db->select('SELECT * FROM categories ORDER BY `id` DESC');
        require_once(BASE_PATH . '/template/admin/posts/create.php');
    }
    public function store($request)
    {
        $realTimestamp = substr($request['published_at'], 0, 10);
        $request['published_at'] = date('Y-m-d H:i:s', (int)$realTimestamp);
        $db = new Database();
        if ($request['cat_id'] != null) {
            $request['image'] = $this->saveImage($request['image'], 'post-image');
            if ($request['image']) {
                $request = array_merge($request, ['user_id' => 1]);
                $db->insert('posts', array_keys($request), $request);
                $this->redirect('admin/post');
            } else {
                $this->redirect('admin/post');
            }
        } else {
            $this->redirect('admin/post');
        }
    }
    public function edit($id)
    {
        $db = new Database();
        $category = $db->select('SELECT * FROM categories WHERE `id` =?', [$id])->fetch();
        require_once(BASE_PATH . '/template/admin/categories/edit.php');
    }
    public function update($request, $id)
    {
        $db = new Database();
        $db->update("categories", $id, array_keys($request), $request);
        $this->redirect("admin/category");
    }
    public function delete($id)
    {

        $db = new Database();
        $post = $db->select("select * from posts WHERE `id` =?", [$id])->fetch();
        $this->removeImage($post['image']);
        $db->delete("posts", $id);
        $this->redirectBack();
    }
    public function selected($id)
    {

        $db = new Database();
        $post = $db->select("select * from posts WHERE `id` =?", [$id])->fetch();
        if (empty($post)) {
            $this->redirectBack();
        }
        if ($post['selected'] == 1) {
            $db->update('posts', $id, ['selected'], [2]);
            $this->redirectBack();
        } else {
            $db->update('posts', $id, ['selected'], [1]);
            $this->redirectBack();
        }
        $this->redirectBack();
    }
    public function breakingNews($id)
    {

        $db = new Database();
        $post = $db->select("select * from posts WHERE `id` =?", [$id])->fetch();
        if (empty($post)) {
            $this->redirectBack();
        }
        if ($post['breaking_news'] == 1) {
            $db->update('posts', $id, ['breaking_news'], [2]);
            $this->redirectBack();
        } else {
            $db->update('posts', $id, ['breaking_news'], [1]);
            $this->redirectBack();
        }
        $this->redirectBack();
    }
}
