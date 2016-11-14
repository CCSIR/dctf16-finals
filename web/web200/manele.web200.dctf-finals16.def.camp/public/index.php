<h1>Welcome to ManeleWorld!</h1>
<h2>Your favourite source of manele music!</h2>

<h3>Here are some classics:</h3>
<ul>
    <li>Nicolae Guta - Locul 1 numai 1 <em>(Status: Approved)</em></li>
    <li>Nicolae Guta - La mamaia <em>(Status: Approved)</em></li>
    <li>Adrian Minune - Asa sunt zilele mele <em>(Status: Approved)</em></li>
    <?php 
    include_once('config.php');
    $songs = $db->query('SELECT * FROM songs WHERE ip="'.$_SERVER['REMOTE_ADDR'].'"');
    while($row = $songs->fetch_array()) {
    	echo "<li>".$row['title'].' <em>(Status: '.($row['opened']?'Approved':'Pending').')</em></li>';
    }
    ?>
</ul>

<p>
    Hint: You can submit songs <a href="submit.php">here</a> and I'll add them once in a while to this website!
</p>
