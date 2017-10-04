<?php

/**
 * Class User
 *
 * Authorization of user with one of accounts
 */
class User
{

    /**
     * Process request
     * @param $array ($_POST or $_GET)
     */
    public function processRequest( $array )
    {
        // start session here
        session_start();

        // condition 1: user and password are set
        if (isset($array['user']))
        {
            $this->logIn($array['user']);
            $this->redirect();
        }

        // condition 2: logout is set
        if (isset($array['logout']))
        {
            $this->logOut();
            $this->redirect();
            return;
        }

        //condition 3: text message is set
        if (isset($array['msg']))
        {
            $messaga = $array['msg'];
            if ($messaga[0] == '/')
            {
                // process any command begin with /
                $params = explode(" ", $messaga);
                $command = "execute" . ucfirst( substr($params[0], 1) );

                if (!method_exists($this, $command)) return;

                $this->$command($params);

            }
            else {
                $this->writeInChat($array['msg']);
            }
            $this->redirect();
            return;
        }


    }


    /**
     * Check if is this user authorized or not
     * @return bool
     */
    public function isAuthenticated()
    {
        return isset( $_SESSION['user'] );
    }

    /**
     * Authenticate user + check nickname
     * @param $user
     */
    public function logIn( $user )
    {
        $nick = preg_replace("/[^a-zA-Z0-9\s]/", "", $user);

        if ($nick == '' or strlen($nick) > 10)
        {
            return;
        }
        else
        {
            $_SESSION['user'] = $nick;
            $this->chat('User ' . $nick . ' has logged in!');
            file_put_contents('online.log', "\n" . $nick . "\n", FILE_APPEND);
            $this->setFlash("Welcome! You can chat now!");
        }

    }

    /**
     * Log out
     */
    public function logOut()
    {
        $user = $_SESSION['user'];
        $this->chat('User ' . $user . ' has logged out');
        $this->setFlash("Logged out");
        $users = array_map( "trim", file('online.log', FILE_SKIP_EMPTY_LINES));
        $key = array_search( $user, $users);

        if ($key!==false)
        {
            unset ($users[$key]);
            file_put_contents( "online.log", implode("\n", $users));
        }

        $this->refreshList();

        unset( $_SESSION['user'] );

    }

    /**
     * Redirect back to index
     */
    public function redirect()
    {
        header("Location: /");
        exit;
    }

    /**
     * Current user name
     * @return string
     */
    public function getName()
    {
        return isset( $_SESSION['user'] ) ? $_SESSION['user'] : "Anonymous";
    }

    public function __toString()
    {
        return (string) $this->getName();
    }

    /**
     * clears chat.log
     */
    public function executeClear()
    {
        file_put_contents( "chat.log", '');
    }

    /**
     * sets topic
     * @param $params
     */
    public function executeTopic($params)
    {
        unset($params[0]);
        $topic = implode(" ", $params);
        $this->statusInChat( "$this sets topic to $topic" );
        $topic = $topic . " (set by $this)";
        file_put_contents( "topic.log", $topic);
    }


    /**
     * remove inactive user from online list
     * @param $params
     */
    public function executeKick($params)
    {
        $nick = $params[1];
        unset($params[0], $params[1]);
        $message = implode(" ", $params);
        $users = array_map( "trim", file('online.log', FILE_SKIP_EMPTY_LINES));
        $key = array_search( $nick, $users);

        if ($key!==false)
        {
            $this->statusInChat( "$this kicked $nick ($message)" );
            unset ($users[$key]);
            file_put_contents( "online.log", implode("\n", $users));
        }

        $this->refreshList();
    }

    /**
     * execute me function
     * @param $params
     */
    public function executeMe($params)
    {
        $me = substr(implode(' ', $params),3);
        $this->statusInChat( "$this" . " " . "$me" );
    }


    /**
     * execute nickname change
     * @param $params
     */
    public function executeNick($params)
    {
        $nick = preg_replace("/[^a-zA-Z0-9\s]/","",$params[1]);

        if ($nick == '' or strlen($nick)>10)
        {
            $this->statusInChat( "Invalid nickname $params[1]" );
        }
        else
        {
        $this->statusInChat( "$this changed nick to $nick" );
            $users = array_map( "trim", file('online.log', FILE_SKIP_EMPTY_LINES));
            $key = array_search( $_SESSION['user'], $users);

            if ($key!==false)
            {
                $users[$key] = $nick;
                file_put_contents( "online.log", implode("\n", $users));
            }

            $this->refreshList();

        $_SESSION['user'] = $nick;
        }
    }

    /**
     * Get flash message and remove it
     * @return null
     */
    public function getFlash()
    {
        $message = isset($_SESSION['flash']) ? $_SESSION['flash'] : null;
        unset($_SESSION['flash']);
        return $message;
    }

    /**
     * Set flash message
     * @param $message
     */
    public function setFlash($message)
    {
        $_SESSION['flash'] = $message;
    }


    /**
     * Log information
     * @param $message
     */
    public function chat($message)
    {
        file_put_contents('chat.log', $message . "\n", FILE_APPEND);
    }

    /**
     * write status in chat
     * @param $msg
     */
    public function statusInChat($msg)
    {
        $this->chat('*** ' . $msg);
    }

    /**
     * write in chat
     * @param $msg
     */
    public function writeInChat($msg)
    {
        $this->chat($this->getName() . ': ' . $msg);
    }

    /**
     * deletes blank nicks from online.log
     */
    public function refreshList()
    {
        $users = array_map( "trim", file('online.log', FILE_SKIP_EMPTY_LINES));
        $key = array_search( "", $users);

        if ($key!==false)
        {
            unset ($users[$key]);
            file_put_contents( "online.log", implode("\n", $users));
        }
    }

}

