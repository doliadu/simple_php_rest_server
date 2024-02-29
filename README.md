# simple_php_rest_server

to run:
php api_mock.php

to test in Ubuntu:
curl -H "Upgrade: websocket" -d 'id=0&name=dusan' -v 127.0.0.1:8888/documents/ --http0.9

you can tweak both php code - parse riadok to get URL or parse parameters, and change curl options to see how it impacts the server echoes
