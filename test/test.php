<?php

require "../src/Frd/CommandLine.php";


$lines=[
    'talk choose :role_id :option',
    'talk :role_id',
];

$cl=new Frd\CommandLine($lines);

$line="talk choose";

$ret=$cl->parse($line);

print_r($ret);


//print_r($cl->cmd_lines);
