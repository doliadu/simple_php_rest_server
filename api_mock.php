<?php 

// REST API Server
$port = 8888;
$address = '127.0.0.1';

if (($sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) === false) {
    echo "socket_create() failed: reason: " . socket_strerror(socket_last_error()) . "\n";
    exit();
}
if (socket_bind($sock, $address, $port) === false) {
    echo "socket_bind() failed: reason: " . socket_strerror(socket_last_error($sock)) . "\n";
    exit();
}
if (socket_listen($sock, 5) === false) {
    echo "socket_listen() failed: reason: " . socket_strerror(socket_last_error($sock)) . "\n";
}
socket_set_nonblock($sock);
// echo "listening for new connection".PHP_EOL;
$conneted_clients = [];

do {
   $clientsock = socket_accept($sock);
   if($clientsock !== false){

        socket_set_nonblock($clientsock);
        $conneted_clients[] = $clientsock;

        socket_getpeername($clientsock,$address);
        // echo "New Connection from: ".$address.PHP_EOL;
/*
        $msg = PHP_EOL."Welcome to the PHP Test Server. " . PHP_EOL.
        "To quit, type 'quit'. To shut down the server type 'shutdown'." . PHP_EOL;

        socket_write($clientsock, $msg, strlen($msg)); */
    }
    $status = check_clients($conneted_clients);

    if(!$status) break;
    usleep(500000);
} while (true);

function check_clients($clients)
{
    foreach($clients as $key => $con)
    {

        if (false === $buff = socket_read($con, 2048)) {
            continue;
        }

        $buff = trim($buff);

        if(strlen($buff) > 0) {
            echo ">>>>>>>>>>".PHP_EOL;
            // var_dump($buff);
            $riadky = preg_split("/\r\n|\n|\r/", $buff);
            foreach($riadky as $riadok)
            echo $riadok.PHP_EOL;
            echo "<<<<<<<<<<".PHP_EOL;
            $talkback = "200 OK\nContent-Type: text/html; charset=UTF-8\nContent-length: 200;\nThis is my reply\n";
            if (socket_write($con, $talkback, strlen($talkback)+8)=== false ) {
                throw new Exception( sprintf( "Unable to write to socket: %s", socket_strerror( socket_last_error() ) ) );
            }
            usleep(1000000);
        }


        if ($buff == 'quit') {
            socket_close($con);
            unset($clients[$key]);
            continue;
        }
        if (trim($buff) == 'shutdown') {
            socket_close($con);
            echo "shutdown initiated".PHP_EOL;
            return FALSE;
        }
        if($buff != false || $buff != null)
        {
         /*   $talkback = "PHP: You said '$buff'.".PHP_EOL;
            socket_write($con, $talkback, strlen($talkback));
            echo "$buff".PHP_EOL;  */
        }
    }
    return TRUE;
}
echo "Closing Server";
socket_close($sock);



?>
