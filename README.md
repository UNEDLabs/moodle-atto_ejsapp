###################
# EJSApp for Atto #
###################

1. Content
==========

This plugin installs a new button for the Atto editor in Moodle to add EjsS applications.

2. License
==========

EJSApp for Atto is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as
published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.

EJSApp for Atto is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.

The GNU General Public License is available on <http://www.gnu.org/licenses/>

3. Installation
===============

If you downloaded this plugin from github, you will need to change the folder's name to ejsapp. If you downloaded it from Moodle.org,
then you are fine.

This is a module plugin for Moodle so you should place the ejsapp folder in your /lib/editor/atto/plugins folder, inside your Moodle
installation directory.

In order to get this plugin to properly work you may need to do a few things:

	A. Modify your database properties file (my.cnf or similar) so that it accepts big enough file sizes and execution times, e.g:
		wait_timeout            = 3600
		max_allowed_packet 	= 200M
		max_heap_table_size     = 2G
		tmp_table_size          = 2G

	B. In Moodle, go to Site Administration>Secutiry>Site policies and enable the option "Enable trusted content".

	C. In the same place, make sure you use a big enough value in the "Maximum uploaded file size" parameter. You need to check
	that such parameter is also big enough in your Course options and in the options for the activity from which you are going
	to use the EJSApp Atto plugin.
	
	D. Also please note that Javascript applications uploaded with this plugin may not be displayed if filters such as Tex notation
	or MathJax are enabled.

4. Dependencies
===============

This module needs the ejsapp module to be of any use. It works with version 2.4 (or later) of EJSApp. You can find and
download it at https://moodle.org/plugins/view.php?plugin=mod_ejsapp, in the plugins section in the Moodle.org
webpage or at https://github.com/UNEDLabs.

5. Authors
==========

EJSApp for Atto has been developed by:
 - Jorge Esteban Cuenca: jestebanc@gmail.com
 - Luis de la Torre: ldelatorre@dia.uned.es

  at the Computer Science and Automatic Control Department, Spanish Open University (UNED), Madrid, Spain.
