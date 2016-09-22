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
    echo "   Convert webhook message_id format to SMTP header Message-ID: format.\n\n";
    echo "SYNOPSIS\n";
    echo "  ./" . $shortProgName . " <id>\n\n";
}

// message_id string order mapping into four discrete chunks that are used in outgoing spool IDs
// chars 0, 1, 2, 3 are not used at all
$map = array(
    0 => array(17, 16),
    1 => array(19, 18),
    2 => array(14, 15, 12, 13),         // this chunk is expressed in decimal, not hex
    3 => array(5, 4, 7, 6, 9, 8, 11, 10)
);

function convToHeader($id)
{
    global $map;
    $s = array('', '', '', '');
    $i = str_split($id);                    // break into individual characters, map character order
    if(sizeof($i) == 20) {
        foreach($map as $j => $k) {
            foreach($k as $m) {
                $s[$j] .= $i[$m];
            }
        }
        $spoolID = strtoupper($s[0] . '/' . $s[1] . '-' . hexdec($s[2]) . '-' . $s[3]);
        echo "SparkPost SMTP spool response: \n  250 2.0.0 OK " . $spoolID . "\n";

        $dotID = str_replace('/', '.', str_replace('-', '.', $spoolID));
        echo "Delivered message header equivalent:\n";
        echo "Message-ID: <" . $dotID . "@xxxx>\n";
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
    convToHeader($argv[1]);
}
else {
    printHelp();
    exit(0);
}