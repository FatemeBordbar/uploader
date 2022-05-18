# File Uploader
                    Secure file uploader
                    simple and strong ;)
program include  

    1- user registration (user register and user login api)
    2- user authentication (laravel/passport)
    3- and uploading file (file upload api)

   
we have 3 entity that user with specific role can upload file for that.

for example :

    user A with role 1 is uploader
    user B with role 2 is viewer

user A can upload file with specific entities
    
    1 => photo
    2 => video
    3 => pdf

in each entity type we have specific file type,
file size, file mime type , file extension , user allowed role ,
file path and every thing else should be considered.


when uploading, these are checked :

    1- user authentiction
    2- user role
    3- entity type 
    4- file types
    5- file size 
    6- file extention
    7- file mime type
    8- ...

after check every thing that you think is time to upload file and insert data to database
the success result give you, file path and file id inserted in database "file table".




