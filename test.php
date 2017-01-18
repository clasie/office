<?php

$origin = "my realtor";

		 $pos = strpos($origin, 'REAL');
		 if ($pos !== false) {  /* MY REALTOR */
			 echo "found";
		 }else{
		    echo "NOT found";
		 }
