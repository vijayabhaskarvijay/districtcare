<?php
header("Content-type: text/css");

$problemStatusSteps = [
    'NEW' => 0,
    'Read' => 1,
    'Preparing' => 2,
    'Working' => 3,
    'Completed' => 4,
];

foreach ($problemStatusSteps as $status => $step) {
    $stepColor = ($problemStatusSteps[$trackedProblemStatus] >= $step) ? '#3498db' : '#ccc';
    $barColor = ($problemStatusSteps[$trackedProblemStatus] >= $step) ? '#3498db' : '#ccc';

    echo <<<CSS
        .step.step-{$step} {
            background-color: {$stepColor};
        }

        .bar.bar-{$step} {
            background-color: {$barColor};
        }
    CSS;
}
