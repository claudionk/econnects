<?php
/**
 * Implicit FTP 
 * @author Nico
 * Based on
 * http://stackoverflow.com/questions/6589730/ftp-ssl-connect-with-implicit-ftp-over-tls
 * http://stackoverflow.com/questions/845220/get-the-last-modified-date-of-a-remote-file
 */
class FTPS {

    private $hostname;
    private $username;
    private $password;
    private $curlhandle;

    public function __construct($config = array()) {
        if($config){
            $this->initalize($config);
        }
    }

    public function __destruct() {
        $this->close();
    }

    private function initalize($config){
        $this->hostname = preg_replace('|.+?://|', '', $config["hostname"]);
        $this->username = $config["username"];
        $this->password = $config["password"];
        $this->curlhandle = curl_init();
        return true;
    }

    /**
     * @param string $remote remote path
     * @return resource a cURL handle on success, false on errors.
     */
    private function common($remote) {
        curl_reset($this->curlhandle);
        curl_setopt($this->curlhandle, CURLOPT_URL, 'ftps://' . $this->hostname . '/' . $remote);
        curl_setopt($this->curlhandle, CURLOPT_USERPWD, $this->username . ':' . $this->password);
        curl_setopt($this->curlhandle, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($this->curlhandle, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($this->curlhandle, CURLOPT_FTP_SSL, CURLFTPSSL_TRY);
        curl_setopt($this->curlhandle, CURLOPT_FTPSSLAUTH, CURLFTPAUTH_TLS);
        return $this->curlhandle;
    }

    public function connect($config){
        return $this->initalize($config);
    }

    public function close(){
        if (!empty($this->curlhandle)){
            @curl_close($this->curlhandle);
        }
    }
    public function download($remote, $local = null, $mode = "auto") {
        if ($local === null) {
            $local = tempnam('/tmp', 'implicit_ftp');
        }

        if ($fp = fopen($local, 'w')) {
            $this->curlhandle = $this->common($remote);
            curl_setopt($this->curlhandle, CURLOPT_UPLOAD, 0);
            curl_setopt($this->curlhandle, CURLOPT_FILE, $fp);

            curl_exec($this->curlhandle);

            if (curl_error($this->curlhandle)) {
                return false;
            } else {
                return true;
            }
        }
        return false;
    }

    public function upload($local, $remote, $mode = "auto", $permissions = null) {
        if ($fp = fopen($local, 'r')) {
            $this->curlhandle = $this->common($remote);
            curl_setopt($this->curlhandle, CURLOPT_UPLOAD, 1);
            curl_setopt($this->curlhandle, CURLOPT_INFILE, $fp);

            curl_exec($this->curlhandle);
            $err = curl_error($this->curlhandle);

            return !$err;
        }
        return false;
    }

    /**
     * Get file/folder names
     * @param string $remote
     * @return string[]
     */
    public function list_files($remote) {
        if (substr($remote, -1) != '/')
            $remote .= '/';
        $this->curlhandle = $this->common($remote);
        curl_setopt($this->curlhandle, CURLOPT_UPLOAD, 0);
        curl_setopt($this->curlhandle, CURLOPT_FTPLISTONLY, 1);
        curl_setopt($this->curlhandle, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($this->curlhandle);


        if (curl_error($this->curlhandle)) {
            return false;
        } else {
            $output = array();
            $files = explode("\n", trim($result));
            foreach($files as $file){
                $output[] = $remote.$file;
            }
            return $output;
            
        }
    }


    function delete_file($remote) {

        print_r($remote);
        $this->curlhandle = $this->common("");
        curl_setopt($this->curlhandle, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->curlhandle, CURLOPT_QUOTE, array('DELE ' .$remote));
        curl_exec($this->curlhandle);        

        if (curl_error($this->curlhandle)) {
            print_r(curl_error($this->curlhandle));
            return false;
        } else {
            return true;            
        }
    }
    


}