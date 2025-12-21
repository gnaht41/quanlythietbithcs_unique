<?php
class connect 
{
    public function ketnoi()
    {
        $host = "localhost";
        $user = "nhicuteso1";
        $password = "123456";
        $database = "qltb";
        $con = new mysqli($host, $user, $password, $database);

        if($con->connect_error)
        {
            return null;
        }
        else
        {
            return $con;
        }
    }
}
?>