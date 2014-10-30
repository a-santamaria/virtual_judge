Virtual Judge --- try it at: http://162.243.41.208
=============

It is  a virtual judge to be used in programming classes for kids and teenagers.

The idea is that you upload problems that you have already solved, and the kids have to do it either during the class or as homework. It will assign points, depending if the student did it before or after the deadline, although it is easy to modify the code and change how many points they earn.

It was thought to be used with the programming language Discant, that can be downloaded <a href ="dserrano3.github.io/Discant">here</a> Because it offers many advantages to teach how to code, but if you preffer to use it with python it also has the option. And you can modify the code, or ask me to, if you want to use any other language that is runnable in linux.

If you want to try it before installing it, you can try it here: http://162.243.41.208/ , you can create a user if you want, but it will probably be erased after a couple of days, because there are students using this page.

To install it, you must have:<br>
-php, it has been tested with php 5.5.9<br>
-mysql<br>
-apache<br>
-phpmyadmin (usefull)<br>
-If you want to use Discant, you must have the latest jar, which will be updated in this repository.<br>
-It was made to be used in linux, and it has been tested with ubuntu, but if you want to use something different, it is possible changing a few lines of code.<br>

To install:<br>
-Install all of the above.<br>
-Download all the files and folders to your server.<br>
-Create a data base.<br>
-Update the data base user, password and name in the file config.php<br>
-In the database, import all the sql files included in the sql folder.<br>
-It will be created an admin user called, admin with password admin<br>
-Change the password in the database table, keep in mind that it is encrypted using sha1, you can Google any free site to convert a password to sha1.<br>
-Give write and read permissions to the folders uploads and problems.<br>
-Create problems, and tell your students to create users.<br>
-Enjoy.<br>
-Let us know if it worked for you or if you would like something changed.<br>


It is a work in progress so there are some things that will be changing pretty soon.<br>
-An easy way to change the password.<br>
-A completly different place for administrators.<br>
-Using sessions so the users only have to login once.<br>
-A way to change your password if you forgot it.<br>
-A way to get the codes you have submitted.<br>
