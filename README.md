# DutyCycle
Smart Switch Duty Cycle Web UI
This consists of files held on a web server, a php file, a python file and a json file.
Using a php web interface, a json file is edited which contains run time and pause time for a web connected smart switch. A python file reads this data and triggers webhooks for the Smart Switch connected to ifttt.
The python file relies on an auto starting python script, in my case using systemd.

The php page is protected using .htaccess password for security