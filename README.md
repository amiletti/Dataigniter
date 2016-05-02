# Dataigniter
A library who make simple to work with [Codeigniter](https://codeigniter.com/) and [Datatable](https://www.datatables.net/) [Server Side Processing](https://www.datatables.net/examples/server_side/).

This sample work with [sqlite](https://sqlite.org/). In order to try it you need a basic [LAMP](https://it.wikipedia.org/wiki/LAMP_(piattaforma)) or [WAMP](https://it.wikipedia.org/wiki/WAMP) stack. Just copy all repository in your localhost and visit http://localhost/index.php/users (I haven't used .htaccess)

# Updated on May 2016
On new version isn't required columns in config file. Dataigniter automatically (for mysql e sqlite) found column name and type directly from db. All field are extract on all queries and returned to datatable. Pre e post formatter method are now delegate to js, you can found a sample in /application/views/users/index.php

# What's inside
* /application/libraries/Dataigniter.php // obviously
* /application/config/dataigniter.php // config files
* /assets/js/jquery.dataTables.yadcf.js // [Yet Another Datatable Column Filter](https://github.com/vedmack/yadcf)
* /application/controllers/Users.php // controller used for this sample
* /application/views/users/index.php // the html view with little style & js
* /assets/data/adminer.php // [Adminer](https://www.adminer.org/)
* /assets/data/sample.sqlite // the db for this sample
