<?php

use Cerpus\PubSub\PubSub;

require __DIR__ . '/../vendor/autoload.php';

if (($topic = ($_POST['topic'] ?? false)) && ($data = ($_POST['data'] ?? false))) {
    $connection = require __DIR__ . '/include/connection.php';
    $pubSub = new PubSub($connection);
    $pubSub->publish($topic, $data);

    http_response_code(303);
    header('Location: /');
} else {
    ?>
<!DOCTYPE html>
<meta charset="utf-8">
<form action="" method="POST">
    <blockquote>
        <p><em>Use <kbd>add_topic</kbd> to subscribe to a topic with the given name</em>
    </blockquote>
    <p><label>Topic <input type="text" name="topic"></label>
    <p><label>Data<br>
        <textarea name="data" rows="8" cols="50" placeholder="Send some data..."></textarea>
    </label>
    <p><button>Send!</button>
</form>
<hr>
<pre><code><?= htmlspecialchars(@file_get_contents('mq.log') ?: '') ?></code></pre>
<?php } ?>
