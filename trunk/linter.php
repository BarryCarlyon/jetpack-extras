<?php

$pass = $soft = $fails = 0;
$data = $fail = array();

testdir(dirname(__FILE__));

function testdir($dir) {
	global $pass, $fail, $soft, $data, $fails;

	$readdir = new FilesystemIterator($dir);
	foreach ($readdir as $fileinfo) {
		if (strpos($fileinfo->getFilename(), '.php')) {
			exec('php -l ' . $dir . '/' . $fileinfo->getFilename(), $return);

			$test1 = 'No syntax errors detected';
			$test2 = 'Deprecated:';

			foreach ($return as $entry) {
				if (substr($entry, 0, strlen($test1)) == $test1) {
					$pass++;
				} else if (substr($entry, 0, strlen($test2)) == $test2) {
					$soft++;
					$data[] = 'SOFT: ' . $dir . '/' . $fileinfo->getFilename() . '--' . $entry;
				} else if ($entry) {
					$fails++;
					$data[] = 'FAIL: ' . $dir . '/' . $fileinfo->getFilename() . '--' . $entry;
					$fail[$dir . '/' . $fileinfo->getFilename()]++;
				} else {
					$pass++;
				}
			}
			unset($return);
		} else if ($fileinfo->getFilename() == '.svn') {
		} else if (is_dir($dir . '/' . $fileinfo->getFilename())) {
			testdir($dir . '/' . $fileinfo->getFilename());
		}
	}
}

$fail = count($fail);
$total = $pass + $fail + $soft;

/*
echo 'Total: ' . $total . "\n";
echo 'Pass:  ' . $pass . "\n";
echo 'Soft:  ' . $soft . "\n";
echo 'Fail:  ' . $fail . "\n";
echo 'Fails: ' . $fails . "\n";
*/

echo "\n";
print_r($data);
echo "\n";

$strings = array(
	'total', 'pass', 'soft', 'fail'//, 'fails'
);

echo ' | ';

$full = '';
foreach ($strings as $string) {
	$toecho = ' ' . $$string;
	$length = strlen($string);
	$target = strlen($toecho);

	while ($length > $target) {
		$toecho .= ' ';
		$target = strlen($toecho);
	}
	$toecho .= ' | ';
	echo ucwords($string) . ' | ';
	$full .= $toecho;
}
echo "\n";
echo ' | ';
echo $full;
echo "\n";

/*
echo 'Total: ' . $total . "\n";
echo 'Pass:  ' . $pass . "\n";
echo 'Soft:  ' . $soft . "\n";
echo 'Fail:  ' . $fail . "\n";
echo 'Fails: ' . $fails . "\n";
*/
