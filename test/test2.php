<?php

require "../src/Frd/CommandLine.php";


$lines=[
    'hello',
    'quit',
];

$cl=new Frd\CommandLine($lines);


//while(true)
//{
//}


while(true)
{
    $line=fwrite(STDOUT,">>> ");

    $line=fread(STDIN,1000);
    $ret=$cl->parse($line);

    if($ret)
    {
        $cmd=implode(".",$ret[0]);

        if($cmd == 'quit') 
        {
            echo 'bye',"\n";
            break;
        }
        else
        {
            print_r($ret);
        }
    }
    else
    {
        echo 'unknown command',"\n";
    }
}


//print_r($cl->cmd_lines);
