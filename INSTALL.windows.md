##Windows Install


####Option #1 - built in php web server
----

* Download XAMPP http://www.apachefriends.org/en/xampp-windows.html
    * Installer: http://www.apachefriends.org/download.php?xampp-win32-1.8.2-3-VC9-installer.exe

* Install XAMPP
    * select: Apache and PHP
    *   path: c:\xampp  
    *   ignore any alerts about UAC
* Create Directory c:\opt\RARflix
    * start a command prompt: cmd.exe

    ```
    cd c:\
    mkdir opt
    cd c:\opt
    mkdir RARflix
    exit
    ```

* Download RARflixTools-master.zip: https://github.com/ljunkie/RARflixTools/archive/master.zip

* Unzip RARflixTools-master.zip to c:\opt\RARflix\

* Rename folder c:\opt\RARflix\RARflixTools-master to c:\opt\RARflix\RARflixTools
    * verify directory structure is correct
    * open a command prompt: cmd.exe
    * make sure it looks like the output below
     
    ```
    cd c:\opt\RARflix\RARflixTools
    
    C:\opt\RARflix\RARflixTools> dir
    
    Volume in drive C has no label.
    Volume Serial Number is C852-DB87
    
    Directory of C:\opt\RARflix\RARflixTools
    
    12/27/2013  07:29 PM    <DIR>          .
    12/27/2013  07:29 PM    <DIR>          ..
    12/27/2013  07:29 PM               443 config.php
    12/27/2013  07:26 PM    <DIR>          examples
    12/27/2013  07:26 PM    <DIR>          fonts
    12/27/2013  07:26 PM    <DIR>          inc
    12/27/2013  03:44 PM             1,847 index.php
    12/27/2013  03:44 PM             1,075 LICENSE
    12/27/2013  03:44 PM             6,070 poster.php
    12/27/2013  03:44 PM             2,230 README.md
               5 File(s)         12,107 bytes
               5 Dir(s)   8,928,272,384 bytes free
               
    C:\opt\RARflix\RARflixTools>
    ```

* start the built-in php web server
    * open a command prompt: cmd.exe

    ```
    c:\xampp\php\php.exe -S 0.0.0.0:32499 -t "c:\opt\RARflix\"
    ```
    * DO NOT close this window. You need to keep it running.

* Test your install
    * http://127.0.0.1:32499/RARflixTools/
    
    ```
    {"rarflix":{"PosterTranscoder":true,"PMSaccess":true,"PosterTranscoderUrl":"http:\/\/127.0.0.1:32499\/RARflixTools\/poster.php"}}
    ```



<br>
<br>
<br>
<br>

#### Option #2 - Apache ( todo.. )
----

