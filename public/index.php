<?php

use app\database\model\Comment;
use app\database\model\Post;
use app\database\model\User;

require '../vendor/autoload.php';

$post = new Post;
$posts = $post->belongsTo(User::class, 'author');

$comment = new Comment;
$comments = $comment->belongsTo(User::class);

// var_dump($comments);
// die();


?>

<ul>
  <?php foreach($comments as $comment): ?>
    <li><?php echo $comment->comment ?> - Author: <?php echo $comment->user->firstName; ?></li>
  <?php endforeach; ?>  
</ul>

<ul>
  <?php foreach($posts as $post): ?>
    <li><?php echo $post->title ?> - Author: <?php echo $post->author->firstName; ?></li>
  <?php endforeach; ?>  
</ul>