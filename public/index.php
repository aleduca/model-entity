<?php


use app\database\model\Category;
use app\database\model\Comment;
use app\database\model\Post;
use app\database\model\User;
use app\database\relations\RelationshipBelongsTo;
use app\database\relations\RelationshipHasMany;

require '../vendor/autoload.php';

$category = new Category;
// $posts = $post->relation(User::class, RelationshipBelongsTo::class, 'author');
// $posts = $post->relation(Comment::class, RelationshipHasMany::class, 'comments');
$categories = $category->relation(Post::class, RelationshipHasMany::class, 'posts');

var_dump($categories);
die();
