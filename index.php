<?php

    include "lib/User.php";
    include "lib/Chat.php";

    $chat = new Chat();
    $user = new User();
    $chat->addUser($user);
    $user->processRequest($_REQUEST);


?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Chat</title>

    <style>

        h1.round
        {
            border: 6px solid red;
            background-color: lightgrey;
            border-radius: 12px;
            width: 500px;
            text-align: center;
        }

        a:link, a:visited
        {
            background-color: #f44336;
            color: white;
            padding: 10px 15px;
            text-align: center;
        }

        div.online
        {
            font-family: TakaoPGothic;
            position: absolute;
            top: 0;
            right: 0;
            width: 150px;
            background-color: darkgrey;
            padding: 10px 15px;
        }



    </style>



</head>
<body>



    <h1 class="round">CHAT!</h1>
    <p>Hello, <b><span style="color: red"><?=$user ?></span></b>! Topic is - <b><span style="color: green"><?=htmlspecialchars($chat->getTopic()) ?></span></b></p>

    <div class="online">
        <?php
        echo "now online:";
        $names = $chat->getUsersOnline();
        foreach($names as $name)
        {
            if ($name != '')
            {
            echo'<br>'.'• '.($name);
            }
        }
        ?>
    </div>

    <p style="color: gray"><?=$user->getFlash(); ?></p>

<?php if ($user->isAuthenticated()): ?>

    <p><a href="?logout">Log out</a></p>

    <div style="height: 600px; background-color: rgba(169,169,169,0.22); position: relative; overflow-y: scroll; display: flex;  flex-direction: column-reverse;">
    <?php

    foreach($chat->getLinesFromEnd("chat.log") as $line)
    {
        echo(htmlspecialchars($line)).'<br>';
    }
    ?>
    </div>

    <form method="post">
        <input style="width:700px" type="text" placeholder="Text message" name="msg" autofocus autocomplete="off"/>
        <input type="submit" value="Submit"/>
    </form>

<?php else: ?>

    <p>Enter your Nickname and chat!</p>
    <form method="post">
        <input type="text" placeholder="Nickname" name="user"/>
        <input type="submit" value="Submit"/>
    </form>

<?php endif; ?>

</body>
</html>
