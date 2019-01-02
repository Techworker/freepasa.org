<?php

namespace Database\Quiz;

\ORM::configure('sqlite:' . __DIR__ . '/data.db');
\ORM::configure('logging', true);
$db = \ORM::getDb();

$db->exec('
CREATE TABLE IF NOT EXISTS quiz (
    id INTEGER PRIMARY KEY, 
    pasa VARCHAR(20),
    answers TEXT DEFAULT(\'{}\'),
    q_ophash_no_password VARCHAR(255),
    q_ophash_with_password VARCHAR(255),
    q_ophash_change_name VARCHAR(255),
    ophash VARCHAR(255),
    block INTEGER,
    amount INTEGER,
    dt UNSIGNED INTEGER 
)');

function createQuiz($pasa) {

    $quiz = \ORM::forTable('quiz')->create();
    $quiz->dt = time();
    $quiz->pasa = $pasa;
    $quiz->save();

    return $quiz;
}

function getQuiz(int $quizId) {
    return \ORM::forTable('quiz')->findOne($quizId);
}