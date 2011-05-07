UNZIP AND COPY THE FILES into your web root (including the .htaccess file)
Assuming that you are testing it on localhost try the following urls

	http://localhost/
	should return "HelloWorld"
	
	http://localhost/sum and http://localhost/index.php/sum
	should return 0
	
	http://localhost/sum.json and http://localhost/index.php/sum.json
	should return 0
	
	http://localhost/sum.xml and http://localhost/index.php/sum.xml
	should return the following xml (view source if required)
	<?xml version="1.0"?>
	<result>0</result>
	
	http://localhost/sum.json?num1=10&num2=4 and http://localhost/index.php/sum.json?num1=10&num2=4
	should return 14
	
	http://localhost/multiply.json/4/2 and http://localhost/index.php/multiply.json/4/2
	should return 8
	
	http://localhost/protectedsum.json?n1=5&n2=4 and http://localhost/index.php/protectedsum.json?n1=5&n2=4
	should ask for username and password
	give admin and mypass respectively
	then it should return 9
	
Now take a look at simpleservice.php to understand how the mapping is done

Keep this as the starting point and build your own :)