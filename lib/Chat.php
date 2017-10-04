<?php

/**
 * Class Chat
 *
 * Chat creation
 */
class Chat
{

    public $users_online = array();

    /**
     * Chat constructor.
     */
    public function __construct()
    {
        $this->getLinesFromEnd( "chat.log" );

        $this->getTopic();

        $this->getUsersOnline();

    }

    /**
     * returns online users from 'online.log'
     * @return array
     */
    public function getUsersOnline()
    {
        $users = array_map( "trim", file('online.log', FILE_SKIP_EMPTY_LINES));
        return $users;
    }

    /**
     * returns topic from 'topic.log'
     * @return string
     */
    public function getTopic()
    {
        $topic = implode(" ", array_map( "trim", file('topic.log', FILE_SKIP_EMPTY_LINES)));
        return $topic;
    }

    /**
     * get last $n lines from $file
     * @param $file
     * @param int $n
     * @return array
     */
    public function getLinesFromEnd( $file, $n = 50 )
    {
        $result = array();
        $handle = fopen($file, "r");
        while(!feof($handle)){
            $result[] = fgets($handle);
            if (count($result) > 1000)
            {
                // slice to last $n items to better consume memory
                $result = array_slice($result,-$n);
            }
        }
        fclose($handle);
        // finally, slice to last $n items
        $result = array_slice($result,-$n);
        return $result;
    }

    /**
     * adds online user
     * @param User $user
     */
    public function addUser( User $user)
    {
        $this->users_online[] = $user;
    }



}

