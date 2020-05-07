<?php

//  Author: Shantanu K

const GENDER = ['MALE' => 'male', 'FEMALE' => 'female'];
const POST_TYPE = ['TEXT' => 'text', 'IMAGE' => 'image', 'VIDEO' => 'video', 'AUDIO' => 'audio', 'DOODLE' => 'doodle'];
const POST_ACTIVITY_TYPE = ['COMMENT' => 'COMMENT', 'LIKE' => 'LIKE', 'DISLIKE' => 'dislike', 'FOLLOW' => 'FOLLOW','PINNED'=>'PINNED','DISAPPEAR'=>'DISAPPEAR'];
const COMMENT_ACTIVITY_TYPE = ['LIKE' => 'like', 'REPLY' => 'reply'];
const COMMENT_ACTIVITY_REPLY = ['REPLY'=>'reply'];
const ALIGNMENT = ['left'=>'left','right'=>'right','center'=>'center'];
const FOLLOWTYPE = ['REQUESTED'=>'REQUESTED','FOLLOWING'=>'FOLLOWING','APPROVED'=>'APPROVED'];
return [
    'MALE' => GENDER['MALE'],
    'FEMALE' => GENDER['FEMALE'],
    'POST_TYPE_TEXT' => POST_TYPE['TEXT'],
    'POST_TYPE_IMAGE' => POST_TYPE['IMAGE'],
    'POST_TYPE_VIDEO' => POST_TYPE['VIDEO'],
    'POST_TYPE_AUDIO' => POST_TYPE['AUDIO'],
    'POST_TYPE_DOODLE' => POST_TYPE['DOODLE'],
    'POST_ACTIVITY_LIKE' => POST_ACTIVITY_TYPE['LIKE'],
    'POST_ACTIVITY_COMMENT' => POST_ACTIVITY_TYPE['COMMENT'],
    'POST_ACTIVITY_COMMENT' => POST_ACTIVITY_TYPE['PINNED'],
    'COMMENT_ACTIVITY_LIKE' => COMMENT_ACTIVITY_TYPE['LIKE'],
    'COMMENT_ACTIVITY_REPLY' => COMMENT_ACTIVITY_TYPE['REPLY'],
    'USER_FOLLOW_REQUESTED' => FOLLOWTYPE['REQUESTED'],
    'USER_FOLLOW_FOLLOWING' => FOLLOWTYPE['FOLLOWING'],
    'USER_FOLLOW_APPROVED' => FOLLOWTYPE['APPROVED'],

    'enums' => [
        'text_post'=>'TEXT',
        'alignment'=>[ALIGNMENT['left'],ALIGNMENT['center'],ALIGNMENT['right']],
        'gender' => [GENDER['MALE'], GENDER['FEMALE']],
        'post_type' => [POST_TYPE['TEXT'], POST_TYPE['IMAGE'], POST_TYPE['VIDEO'], POST_TYPE['AUDIO'], POST_TYPE['DOODLE']],
        'post_activities' => [POST_ACTIVITY_TYPE['COMMENT'], POST_ACTIVITY_TYPE['LIKE'],['PINNED'],['MENTIONED'],['DISAPPEAR']],
        'comment_activities' => [COMMENT_ACTIVITY_TYPE['LIKE'], COMMENT_ACTIVITY_TYPE['REPLY']],
         'request_activities'=> ['DOODLE','FOLLOW','REQUESTED'],
    ],
    'paginate_per_page' => env('PAGINATE_PER_PAGE', 10)
];
