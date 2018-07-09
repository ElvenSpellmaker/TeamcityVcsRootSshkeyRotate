TeamCity VCS Root Key Replacement
=================================

A small php project to replace all VCS Roots which have one key assigned with
another key.

Tested with PHP 7.1 and 7.2.

Notes:
	- Does not upload the new key to TeamCity, this must be done manually first.

Usage
-----
  1. Copy `config.php` to `config.local.php` and update the values with the key to
     replace, the new key, the base url (without trailing slash) for your TeamCity
     install, and a valid cookie for the TeamCity provided.
  2. Run `php change-key.php` and enjoy.

Example output:
```
Page: 1, Count: 100
Success, BeerApi_Test_Test: OldKey --> NewKey
Page: 2, Count: 100
Page: 3, Count: 15
```

Docker Usage
------------
  1. If you'd like to run this in a Docker container, I have an Alpine container
     prepared. Follow the above steps to ensure you have a working config.
  2. Run the Docker image supplying a bindmount to the directory with the config
	 file:
	 `docker run -v /path/to/config:/bindmount -t elvenspellmaker/docker-tc-vsroot-sshkey-rotate`
