<?php

include __DIR__ . '/../bootstrap.php';

$quiz = null;


$triedtoSkip = false;
// if there is an id, we are in an active quiz
if(isset($_GET['id']))
{
    $quizIdEncoded = $_GET['id'];
    $quizId = $hashids->decode($quizIdEncoded)[0];
    $quiz = \Database\Quiz\getQuiz($quizId);
    if($quiz === false) {
        die('quiz not found!');
    }

    // extract all made answers
    $answers = json_decode($quiz->answers, true);

    if(!isset($_GET['q'])) {
        $question = count($answers) + 1;
    } else {
        $question = (int)$_GET['q'];
        if($question > count($answers) + 1) {
            $triedtoSkip = true;
            $question = count($answers) + 1;
        }

        if($question === 0) {
            $question = 1;
        }
    }

} else {
    // there is no quiz id, so we need to display question 1
    $question = 1;
}

// check if its an ajax request
if(\Helper\isAjax()) {
    if($triedtoSkip) {
        return \Helper\sendJson([
            'success' => false,
            'msg' => 'wanted to skip a question'
        ]);
    }

    switch($question)
    {
        default:
        case 1:
            return include __DIR__ . '/include/questions/1/api.php';
            break;
    }

}

?>

<?php include __DIR__ . '/include/head.php'?>

    <div class="info-top">
        <div class="container">
            <div class="headline">Learning Pascal.</div>
        </div>
    </div>
    <div class="container content" id="container_home">
        <div class="row">
            <div class="twelve columns">
                <?php
                switch($question)
                {
                    default:
                    case 1:
                        include __DIR__ . '/include/questions/1/question.php';
                        break;
                }
                ?>
            </div>
        </div>
        <button class="button-primary u-pull-right" onclick="window.location.href='<?=DOMAIN?>/gamify.php?account=' + document.getElementById('account').value">Submit</button>
    </div>
    <!--div class="container content" id="container_question_1">
        <div class="row">
            <div class="twelve columns">
                <h2>Question 1</h2>
                <div class="trivia">
                <p>Like most cryptocurrencies, PascalCoin has a wallet which stores a series of cryptographic Key-Pairs. Key-pairs are comprised of a Private Key and a Public Key. The most important thing you need to know is that you need to protect your Private Key. Don't lose it! Without it, you can't access your Pascals. Don't share it! If someone else gets hold of your Private Key, they have access to your coins!</p>
                <p>Unlike most cryptocurrencies, Pascal (PASC) isn't stored directly against your Public Key. Instead, Pascals and transactions are stored against a PascalCoin account (PASA), and accounts are stored against your Public Key.</p>
                <p>You may give other people your Public Key, but usually only to transfer or assign a PASA. If someone wants to send you PASC, you only need to give them the PASA number you want Pascals sent to.</p>
                </div>
                <p class="question"><strong>What is an Account called in PascalCoin?</strong></p>

                <label for="q1_a1">
                    <input type="radio" id="q1_a1" name="a">
                    <span class="label-body">Wallet</span>
                    <div class="question-wrong-info">Wrong, a wallet manages your key pairs and the accounts associated to them. Try again.</div>
                </label>
                <label for="q1_a2">
                    <input type="radio" id="q1_a2" name="a">
                    <span class="label-body">PASA</span>
                    <div class="question-wrong-info"></div>
                </label>
                <label for="q1_a3">
                    <input type="radio" id="q1_a3" name="a">
                    <span class="label-body">Public Key</span>
                    <div class="question-wrong-info">Wrong, a public key is associated with an account, but not an account itself.</div>
                </label>
                <div class="question-correct-info">Correct, your account is called a PASA (<b>PAS</b>calcoin<b>A</b>ccount)</div>
                <button style="margin-top: 10px;" class="button-primary u-pull-right">Next Question</button>
            </div>
        </div>
    </div>
    <div class="container content" id="container_question_1">
        <div class="row">
            <div class="twelve columns">
                <p><strong>How long is the block time?</strong></p>

                <label for="q1_a1">
                    <input type="radio" id="q1_a1">
                    <span class="label-body">5 Minutes</span>
                </label>
                <label for="q1_a1">
                    <input type="radio" id="q1_a1">
                    <span class="label-body">1 Minute</span>
                </label>
                <label for="q1_a1">
                    <input type="radio" id="q1_a1">
                    <span class="label-body">10 minutes</span>
                </label>
                <div class="error-info">Correct, the right answer is <strong>5 minutes</strong>. <button class="button-primary u-pull-right">Next</button></div>
            </div>
        </div>
    </div>
    <div class="container content" id="container_question_1">
        <div class="row">
            <div class="twelve columns">
                <p><strong>Bitcoin has satoshi, ethereum has Wei. What is the smallest denomication in PascalCoin.:</strong></p>

                <label for="q1_a1">
                    <input type="radio" id="q1_a1">
                    <span class="label-body">Randolina</span>
                </label>
                <label for="q1_a1">
                    <input type="radio" id="q1_a1">
                    <span class="label-body">Jims</span>
                </label>
                <label for="q1_a1">
                    <input type="radio" id="q1_a1">
                    <span class="label-body">Molina</span>
                </label>
                <div class="error-info">Correct, the right answer is <strong>Molina</strong>. <button class="button-primary u-pull-right">Next</button></div>
            </div>
        </div>
    </div>
    <div class="container content" id="container_question_1">
        <div class="row">
            <div class="twelve columns">
                <p><strong>Your action is required: Send 0.0001 PASC to account 123456.</strong></p>
                <div class="error-info">Correct, the right answer is <strong>Molina</strong>. <button class="button-primary u-pull-right">Next</button></div>
            </div>
        </div>
    </div-->

<?php include __DIR__ . '/include/foot.php'?>