<?php
	if (isset($_POST)&&!empty($_POST)){
        $newpost = array_map('htmlspecialchars', $_POST);
		if(isset($newpost['param'])&&$newpost['param'] == "connect"){
            connect();
        }
        if(isset($newpost['param'])&&$newpost['param'] == "subscribe"){
            subscribe();
        }
	}
	if(isset($_GET['disconnect'])){
		disconnect();
	}
?>
<div class="remodal" data-remodal-id="connect">
	<button data-remodal-action="close" class="remodal-close"></button>
	<div class="box">
		<img src="images/logo.PNG">
		<form id="connectform" method="post" action="#">
			<div class="row aln-center gtr-uniform">
				<div class="col-7">
					<input type="email" name="email" value placeholder="Email">
				</div>
				<div class="col-7">
					<input type="password" name="password" value placeholder="password">
				</div>
			</div>
			<div class="submitbutton" class="row aln-center">	
				<input type="submit" value="Connect" class="primary">
            </div>
            <input type="hidden" name="param" value="connect">
        </form>
        <a href="#subscribe">Pas encore de compte ? Inscrivez-vous !</a>
	</div>					
</div>
<div class="remodal" data-remodal-id="subscribe">
	<button data-remodal-action="close" class="remodal-close"></button>
	<div class="box">
		<img src="images/logo.PNG">
		<form id="subscribefrom" method="post" action="#">
			<div class="row aln-center gtr-uniform">
				<div class="col-7">
					<input type="email" name="email" value placeholder="Email" required>
				</div>
				<div class="col-7">
					<input type="password" name="password" value placeholder="password" required>
                </div>
                <div class="col-7">
					<input type="password" name="confirmpassword" value placeholder="Confirm password" required>
                </div>
                <div class="col-7">
					<input type="text" name="prenom" value placeholder="First Name" required>
                </div>
                <div class="col-7">
					<input type="text" name="nom" value placeholder="Last Name" required>
				</div>
			</div>
			<div class="submitbutton" class="row aln-center">	
				<input type="submit" value="Suscribe" class="primary">
            </div>
            <input type="hidden" name="param" value="subscribe">
        </form>
	</div>					
</div>

<?php
	function connect(){
			include('dbconnect.php');
			$sql =  "select count(*) from user where email=\"".$newpost['email']."\"";
			if($res = $connexion->query($sql)){
				if($res->fetchColumn() >0){
					$sql = "select * from user where email=\"".$newpost['email']."\"";
					$req = $connexion->query($sql);
					$res = $req->fetch(PDO::FETCH_ASSOC);
					if(password_verify($newpost['password'],$res['passwordHash'])){
						if(password_needs_rehash($res['passwordHash'], PASSWORD_DEFAULT)){
							$newHash = password_hash($newpost['password'],PASSWORD_DEFAULT);
							$sql = "update user set passwordHash=\"".$newHash."\" where id=\"".$res['id']."\"";
							$sth = $connexion->prepare($sql);
							$sth->execute();						
						}
						$_SESSION['id'] = $res['id'];
						$_SESSION['admin'] = 1;
					}
					else {
                        ?>
                            <script type="text/javascript">
                                $('#connectform .row').prepend('<div class="col-7"><p>Attention : There is no user that match this email and password</p></div>');
                            </script>
                        <?php
					}
				}
				else{
                    ?>
                        <script type="text/javascript">
                            $('#connectform .row').prepend('<div class="col-7"><p>Attention : There is no user that match this email and password</p></div>');
                        </script>
                    <?php
				}
			}
	}

	function disconnect(){
		session_unset();
		session_destroy();
    }
    
    function subscribe(){
        include('dbconnect.php');
        if(isset($newpost['prenom']) && !empty($newpost['prenom']) AND isset($newpost['nom']) && !empty($newpost['nom']) AND isset($newpost['email']) && !empty($newpost['email']) AND isset($newpost['password']) && !empty($newpost['password']) AND isset($newpost['confirmpassword']) && !empty($newpost['confirmpassword'])){
            if(filter_var($newpost['email'],FILTER_VALIDATE_EMAIL)){
                if($newpost['password'] == $newpost['confirmpassword']){
                    if($newpost['email'] != $newpost['password']){
                        $sql = "SELECT count(email) FROM user WHERE email = \"".$newpost["email"]."\"";
                        $sql2 = $connexion -> query($sql);
                        $sql3 = $sql2 -> fetch(PDO::FETCH_NUM);
                        if($sql3[0] <= 0){
                            $hash = password_hash(rand(0,1000),PASSWORD_DEFAULT);
                            $sql = "INSERT INTO user VALUES ('',\'".$newpost['email']."','".password_hash($newpost['password'],PASSWORD_DEFAULT)."','user/genericavatar.png','','','".$hash."')";

                            $to = $newpost['email'];
                            $subject = 'La Maison Du Yoga | Email Verification';
                            $message ='
                            Thanks for signing up !
                            Your account has been created.
                            Please click this link  to activate your account :
                            http://localhost/lamaisonduyoga/verify.php?email='.$newpost['email'].'&hash='.$hash.'';
                            $headers = 'From:noreply@lamaisonduyoga.com'."\r\n";
                            mail($to,$subject,$message,$headers);
                        }
                        else{
                            ?>
                            <script type="text/javascript">
                                $('#subscribefrom .row').prepend('<div class="col-7"><p>Attention : An account already exist with this email address</p></div>');
                            </script>
                            <?php
                        }
                    }
                    else{
                        ?>
                            <script type="text/javascript">
                                $('#subscribefrom .row').prepend('<div class="col-7"><p>Attention : Your password must be different than you email</p></div>');
                            </script>
                        <?php
                    }
                }
                else{
                    ?>
                        <script type="text/javascript">
                            $('#subscribefrom .row').prepend('<div class="col-7"><p>Attention : Both passwords must match</p></div>');
                        </script>
                    <?php
                }
                
            }
        }
    }

?>