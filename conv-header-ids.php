#!/usr/bin/env php
<?php
//Convert message IDs to/from Webbooks format
//Copyright  2016 SparkPost

//Licensed under the Apache License, Version 2.0 (the "License");
//you may not use this file except in compliance with the License.
//You may obtain a copy of the License at
//
//    http://www.apache.org/licenses/LICENSE-2.0
//
//Unless required by applicable law or agreed to in writing, software
//distributed under the License is distributed on an "AS IS" BASIS,
//WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
//See the License for the specific language governing permissions and
//limitations under the License.

//
// Author: Steve Tuck (September 2016)
//

function printHelp()
{
    global $argv;

    $progName = $argv[0];
    $shortProgName = basename($progName);
    echo "\nNAME\n";
    echo "   " . $progName . "\n";
    echo "   Convert SMTP header Message-ID: format to webhook message_id format.\n\n";
    echo "SYNOPSIS\n";
    echo "  ./" . $shortProgName . " <id>\n\n";
    echo "  Example <id> 7B/6A-18429-DA8D3E75\n\n";
}

// message_id string order mapping into four discrete chunks that are used in outgoing spool IDs
// chars 0, 1, 2, 3 are not used at all
$map = array(
    0 => array(17, 16),
    1 => array(19, 18),
    2 => array(14, 15, 12, 13),         // this chunk is expressed in decimal, not hex
    3 => array(5, 4, 7, 6, 9, 8, 11, 10)
);

function convToWebhook($id)
{
    global $map;
    $s = preg_split("#[-/.]#", $id);
    if (sizeof($s) !== 4) {
        echo "Invalid input - should contain 4 values separated by - . or /";
        exit(1);
    }
    $s[2] = dechex($s[2]);
    $r = [];
    $r[0] = "x";
    $r[1] = "x";
    $r[2] = "x";
    $r[3] = "x";
    foreach($map as $j => $k) {
        foreach ($k as $i => $m) {
            $r[$m] = substr($s[$j], $i, 1);
        }
    }
    ksort($r);
    $r_str = implode("", $r);
    if(strlen($r_str) == 20) {
        echo "SparkPost webhooks message_id response: " . $r_str. "\n";
    } else {
        echo "Invalid message_id length\n";
        exit(1);
    }
}

// -----------------------------------------------------------------------------------------
// Main code
// -----------------------------------------------------------------------------------------

// Check argument count, otherwise accessing beyond array bounds throws an error in PHP 5.5+
if($argc >= 2) {
    convToWebhook($argv[1]);
}
else {
    printHelp();
    exit(0);
}