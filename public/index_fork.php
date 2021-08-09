<?php
$socket = stream_socket_server("tcp://0.0.0.0:8080", $err_no, $err_str);
while ($conn = stream_socket_accept($socket)) {
    if (pcntl_fork() == 0) {
        $buffer = '';
        do {
            $buffer .= fread($conn, 1024);
        } while (!preg_match('/\r?\n\r?\n/', $buffer));
        fwrite($conn, "HTTP/1.1 200 OK\n\nHello, World!\n");
        stream_socket_shutdown($conn, STREAM_SHUT_RDWR);
        exit(0);
    }
}