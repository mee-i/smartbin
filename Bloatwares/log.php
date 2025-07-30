<?php
function log_data() {
    $logfile = fopen("../logs/server_log.txt", "a+");
    fwrite($logfile, "[".date("Y/m/d H:i:s")."] ".$_SERVER["REQUEST_METHOD"]." ".$_SERVER["REQUEST_URI"]."\n");
}
?>