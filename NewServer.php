<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Insert title here</title>
</head>
<body>
<form action="new.php" method="post"><p>
ServerName: <input type="text" name="servername" value="<?php echo 'servername';?>" id='servername'><br>

IP: <input type="text" name="ip" value="<?php echo 'ip';?>" id='ip'><br>

Company: <input type="text" name="company" value="<?php echo 'company';?>" id=company><br>



Populate:
<input type="radio" name="populate"
<?php if (isset($populate) && $populate=="yes") echo "checked";?>
value="yes" id='yes'>Yes
<input type="radio" name="populate"
<?php if (isset($populate) && $populate=="no") echo "checked";?>
value="no" id='no'>No
<br>

Description: <textarea name="description" rows="5" cols="40"><?php echo "description";?></textarea>
<br>
<input type="submit">
</p>
</form>
<p>
Have you added the web servers ssh-key to the remote root account /root/.ssh/authorized_keys, if not please do so now! Also please check you can ssh as root the your server without the security check, you will need to access the ip not the servername

</p>
</body>
</html>