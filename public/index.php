<?php

use app\database\model\Category;
use app\database\model\Comment;
use app\database\model\Post;
use app\database\model\User;
use app\database\relations\RelationshipBelongsTo;
use app\database\relations\RelationshipHasMany;

require '../vendor/autoload.php';

$post = new Post;
$posts = $post->makeRelationsWith(
    [User::class, RelationshipBelongsTo::class, 'author'],
    [Comment::class, RelationshipHasMany::class, 'comments'],
    [Category::class, RelationshipBelongsTo::class, 'category']
);
// $posts = $post->relation(User::class, RelationshipBelongsTo::class, 'author');
// $posts = $post->relation(Comment::class, RelationshipHasMany::class, 'comments');

var_dump($posts);
