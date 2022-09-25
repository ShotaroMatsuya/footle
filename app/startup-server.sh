#!/bin/sh

timeout 300 php job1.php https://www.goal.com/en/ &
timeout 300 php job1.php https://onefootball.com/en/home &
timeout 300 php job1.php https://www.bbc.com/sport/football &
timeout 300 php job1.php https://www.skysports.com/football/news &
timeout 300 php job1.php https://talksport.com/football/ &
timeout 300 php job1.php https://www.transfermarkt.com/ &

wait