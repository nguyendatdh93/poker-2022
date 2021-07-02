## Deploy step

1. Create new pair key. 
https://docs.rightscale.com/faq/How_Do_I_Generate_My_Own_SSH_Key_Pair.html

2. Access to https://dns11.w3services.net/websites/zerocryptopoker.com/sshAccess and add your pair key.

3. SSH to server via command: ssh zeroc9938@164.68.103.174

4. Access to project: cd /home/zerocryptopoker.com/public_html

5. Deploy by git pull: git pull origin deploy
