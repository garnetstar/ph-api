### Mandatory env:  
DB_PASSWORD  
DB_USER  
DB_HOST  
DB_NAME  

### Optional env:  
DEBUG (default: false)  
GOOGLE_CLIENT_ID (use only in Login)  

### LoginPresenter 
parameter `id_token` is value get from Google Sign-in  
https://developers.google.com/identity/sign-in/web/backend-auth  

You can get its value from test page `/login.php` (only with DEBUG=true)   

id_token validator:  
https://www.googleapis.com/oauth2/v3/tokeninfo?id_token=XYZ123

