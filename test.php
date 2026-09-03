<?php
$req = Illuminate\Http\Request::create('/api/iclock/cdata', 'POST', [], [], [], [
    'HTTP_dev-id' => 'C2609075E32F282D',
    'HTTP_request-code' => 'realtime_glog'
], '{"fk_bin_data_lib":"FKDataHS103","io_mode":16777216,"io_time":"20260319161722","log_image":null,"user_id":"1","verify_mode":268435456}');

$controller = app('App\Http\Controllers\Api\AdmsController');
echo $controller->capture($req)->getContent();
