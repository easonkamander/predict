<?php
file_get_contents('http://localhost:8000/', false, stream_context_create(array('http' => array(
    'method' => 'POST',
    'header' => 'Content-Type: text/xml',
    'content' => xmlrpc_encode_request('method', array(1, 2, 3))
))));