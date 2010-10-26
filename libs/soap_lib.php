<?

class soap_lib
{
    private $username=null;
    private $password=null;
    private $url=null;
    private $commands = array();
    private $session = null;
    private $persistent = true;
    private $maxcmdsize = 4;
    private $error_msg = null;

    // Input: Command string
    // Return: Numeric means something wrong happened
    //         1 => Already connected
    //         2 => Missing config settings
    //         3 => Connection error
    //         4 => Command not found
    //         5 => Command failed
    //         String with the command result
    public function SendCommand($p_command)
    {
        // Check if command exists in our commands list
        $cmdArray = explode(' ', $p_command, $this->maxcmdsize);
        $cmd = array_shift($cmdArray);
        $count = 0;
        for ($i=0; $i<$this->maxcmdsize; $i++)
        {
            if(!$this->GetCommandInfo($cmd))
                $count++;
            $cmd .= ' '.array_shift($cmdArray);
        }

        // Command not found, return an error
        if ($count >= $this->maxcmdsize)
        {
            $this->error_msg = 'Command not found';
            return 4;
        }

        // Hoho somebody forget to connect before sending commands
        // lets connect
        if ($this->session == null && $this->persistent)
            if ($ret = $this->Connect())
                return $ret;

        if ($this->session != null && !$this->persistent)
            if ($ret = $this->Connect())
                return $ret;

        $result = null;
        try
        {
            $result = $this->session->executeCommand(new SoapParam($p_command, "command"));
        }
        catch (Exception $e)
        {
            $this->error_msg = "Command failed! Reason: ".$e->getMessage();
            return 5;
        }

        if ($this->session != null && $this->persistent)
            $this->Disconnect();

        return $result;
    }

    // Input: Nothing
    // Output: 0 => Connection OK
    //         1 => Already connected
    //         2 => Missing config settings
    //         3 => Connection error
    private function Connect()
    {
        if ($this->session != null)
        {
            $this->error_msg = 'Already connected';
            return 1;
        }
        if ($this->username == null || $this->password == null || $this->url == null)
        {
            $this->error_msg = 'Missing config settings';
            return 2;
        }
        $this->session = new SoapClient(null, array( 'location' => $this->url,
                                               'uri' => "urn:MaNGOS",
                                               'style' => SOAP_RPC,
                                               'login' => $this->username,
                                               'password' => $this->password
                                              )
                                 );
    }

    // Input: Nothing
    private function Disconnect()
    {
        if ($this->session != null)
            $this->session = null;
    }

    // Input: Array of commands
    //        Sets the class commands list.
    public function SetCommandsList($p_cmdList)
    {
        $this->commands = $p_cmdList;
    }

    // Input: Username
    //        Sets the username.
    public function SetUsername($p_username)
    {
        $this->username = $p_username;
    }

    // Input: Password
    //        Sets the password.
    public function SetPassword($p_pwd)
    {
        $this->password = $p_pwd;
    }

    // Input: Url
    //        Sets the soap url.
    public function SetUrl($p_url)
    {
        $this->url = $p_url;
    }

    // Input: Nothing
    //        Return full commands list.
    public function GetCommandsList()
    {
        return $this->commands;
    }

    // Input: Command
    //        Return an array(command level, command help).
    public function GetCommandInfo($p_command)
    {
        $rett = null;
        if (array_key_exists($p_command, $this->commands))
            $rett = $this->commands[$p_command];
        return $rett;
    }

    // Input: Nothing
    //        Return the current username.
    public function GetUsername()
    {
        return $this->username;
    }

    // Input: Nothing
    //        Return the current password.
    public function GetPassword()
    {
        return $this->password;
    }

    // Input: Nothing
    //        Return the server URL.
    public function GetUrl()
    {
        return $this->url;
    }

    // Input: Nothing
    //        Return the last error message.
    public function GetErrorMsg()
    {
        return $this->error_msg;
    }

    // Input: true or false
    //        Sets the session persistence
    //  true: keep session up until the object is destroyed
    //  false: each send command will send a reconnect
    public function SetSessionPersistence($p_bool=true)
    {
        $this->persistent = $p_bool;
    }

}

?>
