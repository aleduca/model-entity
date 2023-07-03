<?php

use app\database\library\Query;
use app\database\model\Comment;
use app\database\model\User;
use app\database\relations\RelationshipBelongsTo;
use app\database\relations\RelationshipHasMany;

require '../vendor/autoload.php';

// select id,slug,user_id,title where id > 40 and field operator value

$query = new Query;
$query->select('id,firstName,lastName')
->limit(10)
->where('id', '>', 20)
->order('id desc')
->paginate(User::class);


// $posts = $query->modelInstance->execute($query)->makeRelationsWith(
//     [User::class, RelationshipBelongsTo::class, 'author'],
//     [Comment::class, RelationshipHasMany::class, 'comments'],
// );

$users = $query->modelInstance->execute($query)->makeRelationsWith(
    [Comment::class, RelationshipHasMany::class, 'comments'],
);
var_dump(
    $query->paginate->createLinks(3)
);
var_dump($users);
